<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CategoryModel;
use App\Models\PaymentModel;

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
                t.total,
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
            $builder->where('c.parent_id', 3);
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
                $totalAmount += floatval($transaction['total']);
                $totalCount++;

                // สถิติตามหมวดหมู่
                $categoryName = $transaction['category_name'] ?: 'ไม่ระบุหมวดหมู่';
                if (!isset($categoryStats[$categoryName])) {
                    $categoryStats[$categoryName] = 0;
                }
                $categoryStats[$categoryName] += floatval($transaction['total']);

                // สถิติรายเดือน
                $month = date('Y-m', strtotime($transaction['datetime']));
                if (!isset($monthlyStats[$month])) {
                    $monthlyStats[$month] = 0;
                }
                $monthlyStats[$month] += floatval($transaction['total']);
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
     * API: ดึงข้อมูลค่าใช้จ่ายสำหรับกราฟและตาราง
     */
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
                t.total,
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
            $builder->where('c.parent_id', 4);
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
                $totalAmount += floatval($transaction['total']);
                $totalCount++;

                // สถิติตามหมวดหมู่
                $categoryName = $transaction['category_name'] ?: 'ไม่ระบุหมวดหมู่';
                if (!isset($categoryStats[$categoryName])) {
                    $categoryStats[$categoryName] = 0;
                }
                $categoryStats[$categoryName] += floatval($transaction['total']);

                // สถิติรายเดือน
                $month = date('Y-m', strtotime($transaction['datetime']));
                if (!isset($monthlyStats[$month])) {
                    $monthlyStats[$month] = 0;
                }
                $monthlyStats[$month] += floatval($transaction['total']);
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
}

