<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CategoryModel;
use App\Models\PaymentModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends BaseController
{
    protected $transactionModel;
    protected $transactionItemModel;
    protected $categoryModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->categoryModel = new CategoryModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * หน้ารายงานยอดรายได้โดยรวม
     */
    public function totalIncome()
    {
        $data = [
            'title' => lang('app.totalIncome'),
            'reportType' => 'income'
        ];
        
        return view('backend/reports/report_income', $data);
    }

    /**
     * หน้ารายงานยอดค่าใช้จ่ายโดยรวม
     */
    public function totalExpenses()
    {
        $data = [
            'title' => lang('app.totalExpenses'),
            'reportType' => 'expenses'
        ];
        
        return view('backend/reports/report_expenses', $data);
    }

    /**
     * API: ดึงข้อมูลรายได้สำหรับกราฟและตาราง
     */
    public function getIncomeData()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $categoryId = $this->request->getGet('category_id');

        try {
            $builder = $this->transactionModel->db->table('tb_transaction t');
            $builder->select('
                t.id,
                t.datetime,
                t.ref_no,
                t.descripton,
                ti.name as item_name,
                ti.quantity,
                ti.price,
                ti.note,
                c.name as category_name,
                p.name_th as payment_name
            ');
            $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
            $builder->where('c.node_id', 3);
            $builder->where('t.deleted_at IS NULL');

            // กรองตามวันที่
            if ($startDate && $endDate) {
                $builder->where('DATE(t.datetime) >=', $startDate);
                $builder->where('DATE(t.datetime) <=', $endDate);
            }

            // กรองตามหมวดหมู่
            if ($categoryId) {
                $builder->where('ti.category_id', $categoryId);
            }

            $builder->orderBy('t.datetime', 'DESC');
            $transactions = $builder->get()->getResultArray();
            // $transactions = $builder->getCompiledSelect();
            

            // คำนวณสถิติ
            $totalAmount = 0;
            $totalCount = 0;
            $categoryStats = [];
            $monthlyStats = [];

            foreach ($transactions as $transaction) {
                // คำนวณราคารวม = price * quantity
                $totalPrice = floatval($transaction['price']) * floatval($transaction['quantity']);
                $totalAmount += $totalPrice;
                $totalCount++;

                // สถิติตามหมวดหมู่
                $categoryName = $transaction['category_name'] ?: 'ไม่ระบุหมวดหมู่';
                if (!isset($categoryStats[$categoryName])) {
                    $categoryStats[$categoryName] = 0;
                }
                $categoryStats[$categoryName] += $totalPrice;

                // สถิติรายเดือน
                $month = date('Y-m', strtotime($transaction['datetime']));
                if (!isset($monthlyStats[$month])) {
                    $monthlyStats[$month] = 0;
                }
                $monthlyStats[$month] += $totalPrice;
            }

            $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;

            // จัดรูปแบบข้อมูลสำหรับกราฟ
            $monthlyChartData = [];
            $categoryChartData = [];

            // ข้อมูลกราฟรายเดือน (6 เดือนล่าสุด)
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $months[] = date('M', strtotime($month));
                $monthlyChartData[] = $monthlyStats[$month] ?? 0;
            }

            // ข้อมูลกราฟหมวดหมู่
            foreach ($categoryStats as $category => $amount) {
                $categoryChartData[] = [
                    'label' => $category,
                    'value' => $amount
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_amount' => number_format($totalAmount, 2),
                        'total_count' => $totalCount,
                        'average_amount' => number_format($averageAmount, 2)
                    ],
                    'transactions' => $transactions,
                    'charts' => [
                        'monthly' => [
                            'labels' => $months,
                            'data' => $monthlyChartData
                        ],
                        'category' => $categoryChartData
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function getExpensesData()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $categoryId = $this->request->getGet('category_id');

        try {
            // สร้าง query สำหรับดึงข้อมูลค่าใช้จ่าย
            $builder = $this->transactionModel->db->table('tb_transaction t');
            $builder->select('
                t.id,
                t.datetime,
                t.ref_no,
                t.descripton,
                ti.name as item_name,
                ti.quantity,
                ti.price,
                ti.note,
                c.name as category_name,
                p.name_th as payment_name
            ');
            $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
            $builder->where('c.node_id', 4);
            $builder->where('t.deleted_at IS NULL');

            // กรองตามวันที่
            if ($startDate && $endDate) {
                $builder->where('DATE(t.datetime) >=', $startDate);
                $builder->where('DATE(t.datetime) <=', $endDate);
            }

            // กรองตามหมวดหมู่
            if ($categoryId) {
                $builder->where('ti.category_id', $categoryId);
            }

            $builder->orderBy('t.datetime', 'DESC');
            $transactions = $builder->get()->getResultArray();

            // คำนวณสถิติ
            $totalAmount = 0;
            $totalCount = 0;
            $categoryStats = [];
            $monthlyStats = [];

            foreach ($transactions as $transaction) {
                // คำนวณราคารวม = price * quantity
                $totalPrice = floatval($transaction['price']) * floatval($transaction['quantity']);
                $totalAmount += $totalPrice;
                $totalCount++;

                // สถิติตามหมวดหมู่
                $categoryName = $transaction['category_name'] ?: 'ไม่ระบุหมวดหมู่';
                if (!isset($categoryStats[$categoryName])) {
                    $categoryStats[$categoryName] = 0;
                }
                $categoryStats[$categoryName] += $totalPrice;

                // สถิติรายเดือน
                $month = date('Y-m', strtotime($transaction['datetime']));
                if (!isset($monthlyStats[$month])) {
                    $monthlyStats[$month] = 0;
                }
                $monthlyStats[$month] += $totalPrice;
            }

            $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;

            // จัดรูปแบบข้อมูลสำหรับกราฟ
            $monthlyChartData = [];
            $categoryChartData = [];

            // ข้อมูลกราฟรายเดือน (6 เดือนล่าสุด)
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $months[] = date('M', strtotime($month));
                $monthlyChartData[] = $monthlyStats[$month] ?? 0;
            }

            // ข้อมูลกราฟหมวดหมู่
            foreach ($categoryStats as $category => $amount) {
                $categoryChartData[] = [
                    'label' => $category,
                    'value' => $amount
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_amount' => number_format($totalAmount, 2),
                        'total_count' => $totalCount,
                        'average_amount' => number_format($averageAmount, 2)
                    ],
                    'transactions' => $transactions,
                    'charts' => [
                        'monthly' => [
                            'labels' => $months,
                            'data' => $monthlyChartData
                        ],
                        'category' => $categoryChartData
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: ดึงรายการหมวดหมู่สำหรับ dropdown
     */
    public function getCategories()
    {
        try {
            $categories = $this->categoryModel->where('status', 'active')->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export รายงานรายได้เป็น Excel
     */
    public function exportIncome()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $categoryId = $this->request->getGet('category_id');


        try {
            // ดึงข้อมูลรายได้
            $builder = $this->transactionModel->db->table('tb_transaction t');
            $builder->select('
                t.id,
                t.datetime,
                t.ref_no,
                t.descripton,
                ti.name as item_name,
                ti.quantity,
                ti.price,
                ti.note,
                c.name as category_name,
                p.name_th as payment_name
            ');
            $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
            $builder->where('c.node_id', 3);
            $builder->where('t.deleted_at IS NULL');

            // กรองตามวันที่
            if (!empty($startDate) && !empty($endDate)) {
                $builder->where('DATE(t.datetime) >=', $startDate);
                $builder->where('DATE(t.datetime) <=', $endDate);
            }

            // กรองตามหมวดหมู่
            if (!empty($categoryId)) {
                $builder->where('ti.category_id', $categoryId);
            }

            $builder->orderBy('t.datetime', 'DESC');
            $transactions = $builder->get()->getResultArray();
    
            // สร้าง Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('รายงานรายได้');

            // ตั้งค่าหัวข้อ
            $headers = [
                'A1' => 'ลำดับ',
                'B1' => 'วันที่',
                // 'C1' => 'เลขที่อ้างอิง',
                'C1' => 'รายการ',
                'D1' => 'หมวดหมู่',
                'E1' => 'จำนวนเงิน',
                'F1' => 'วิธีการชำระเงิน',
                'G1' => 'หมายเหตุ'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // กำหนดสไตล์หัวข้อ
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '11998e']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

            // เพิ่มข้อมูล
            $row = 2;
            $totalAmount = 0;
            foreach ($transactions as $index => $transaction) {
                // คำนวณราคารวม = price * quantity
                $totalPrice = floatval($transaction['price']) * floatval($transaction['quantity']);
                
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($transaction['datetime'])));
                // $sheet->setCellValue('C' . $row, $transaction['ref_no'] ?: '-');
                $sheet->setCellValue('C' . $row, $transaction['item_name'] ?: $transaction['descripton'] ?: '-');
                $sheet->setCellValue('D' . $row, $transaction['category_name'] ?: '-');
                $sheet->setCellValue('E' . $row, number_format($totalPrice, 2));
                $sheet->setCellValue('F' . $row, $transaction['payment_name'] ?: '-');
                $sheet->setCellValue('G' . $row, $transaction['note'] ?: '-');
                
                $totalAmount += $totalPrice;
                $row++;
            }

            // กำหนดสไตล์ข้อมูล
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            if ($row > 2) {
                $sheet->getStyle('A2:H' . ($row - 1))->applyFromArray($dataStyle);
            }

            // เพิ่มแถวสรุป
            $summaryRow = $row + 1;
            $sheet->setCellValue('E' . $summaryRow, 'รวมทั้งหมด');
            $sheet->setCellValue('F' . $summaryRow, number_format($totalAmount, 2));

            // กำหนดสไตล์แถวสรุป
            $summaryStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '38ef7d']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            $sheet->getStyle('E' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

            // ปรับความกว้างคอลัมน์
            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(18);
            // $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(25);

            // สร้างไฟล์ Excel
            $filename = 'รายงานรายได้_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // ล้าง output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // ตั้งค่า header สำหรับ download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Pragma: no-cache');

            // ส่งออกไฟล์
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการส่งออกข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export รายงานค่าใช้จ่ายเป็น Excel
     */
    public function exportExpenses()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $categoryId = $this->request->getGet('category_id');

        try {
            // ดึงข้อมูลค่าใช้จ่าย
            $builder = $this->transactionModel->db->table('tb_transaction t');
            $builder->select('
                t.id,
                t.datetime,
                t.ref_no,
                t.descripton,
                ti.name as item_name,
                ti.quantity,
                ti.price,
                ti.note,
                c.name as category_name,
                p.name_th as payment_name
            ');
            $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
            $builder->where('c.node_id', 4);
            $builder->where('t.deleted_at IS NULL');

            // กรองตามวันที่
            if ($startDate && $endDate) {
                $builder->where('DATE(t.datetime) >=', $startDate);
                $builder->where('DATE(t.datetime) <=', $endDate);
            }

            // กรองตามหมวดหมู่
            if ($categoryId) {
                $builder->where('ti.category_id', $categoryId);
            }

            $builder->orderBy('t.datetime', 'DESC');
            $transactions = $builder->get()->getResultArray();

            // สร้าง Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('รายงานค่าใช้จ่าย');

            // ตั้งค่าหัวข้อ
            $headers = [
                'A1' => 'ลำดับ',
                'B1' => 'วันที่',
                // 'C1' => 'เลขที่อ้างอิง',
                'C1' => 'รายการ',
                'D1' => 'หมวดหมู่',
                'E1' => 'จำนวนเงิน',
                'F1' => 'วิธีการชำระเงิน',
                'G1' => 'หมายเหตุ'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // กำหนดสไตล์หัวข้อ
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'dc3545']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

            // เพิ่มข้อมูล
            $row = 2;
            $totalAmount = 0;
            foreach ($transactions as $index => $transaction) {
                // คำนวณราคารวม = price * quantity
                $totalPrice = floatval($transaction['price']) * floatval($transaction['quantity']);
                
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($transaction['datetime'])));
                // $sheet->setCellValue('C' . $row, $transaction['ref_no'] ?: '-');
                $sheet->setCellValue('C' . $row, $transaction['item_name'] ?: $transaction['descripton'] ?: '-');
                $sheet->setCellValue('D' . $row, $transaction['category_name'] ?: '-');
                $sheet->setCellValue('E' . $row, number_format($totalPrice, 2));
                $sheet->setCellValue('F' . $row, $transaction['payment_name'] ?: '-');
                $sheet->setCellValue('G' . $row, $transaction['note'] ?: '-');
                
                $totalAmount += $totalPrice;
                $row++;
            }

            // กำหนดสไตล์ข้อมูล
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            if ($row > 2) {
                $sheet->getStyle('A2:H' . ($row - 1))->applyFromArray($dataStyle);
            }

            // เพิ่มแถวสรุป
            $summaryRow = $row + 1;
            $sheet->setCellValue('E' . $summaryRow, 'รวมทั้งหมด');
            $sheet->setCellValue('F' . $summaryRow, number_format($totalAmount, 2));

            // กำหนดสไตล์แถวสรุป
            $summaryStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'fd7e14']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            $sheet->getStyle('E' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

            // ปรับความกว้างคอลัมน์
            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(18);
            // $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(25);

            // สร้างไฟล์ Excel
            $filename = 'รายงานค่าใช้จ่าย_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // ล้าง output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // ตั้งค่า header สำหรับ download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Pragma: no-cache');

            // ส่งออกไฟล์
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการส่งออกข้อมูล: ' . $e->getMessage()
            ]);
        }
    }
}

