<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CategoryModel;
use App\Models\PaymentModel;
use App\Models\CounterpartieModel;
use App\Models\OrganizationModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $transactionModel;
    protected $transactionItemModel;
    protected $categoryModel;
    protected $paymentModel;
    protected $counterpartieModel;
    protected $organizationModel;
    protected $userModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->categoryModel = new CategoryModel();
        $this->paymentModel = new PaymentModel();
        $this->counterpartieModel = new CounterpartieModel();
        $this->organizationModel = new OrganizationModel();
        $this->userModel = new UserModel();
    }

    public function goto_dashboard()
    {
        $data = [
            'title' => 'แดชบอร์ดภาพรวม'
        ];

        return view('backend/dashboard', $data);
    }

    /**
     * API: ดึงข้อมูลสรุปสำหรับ dashboard
     */
    public function getDashboardData()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        try {
            // กำหนดช่วงวันที่ (ถ้าไม่ระบุจะใช้ 30 วันล่าสุด)
            if (!$startDate || !$endDate) {
                $endDate = date('Y-m-d');
                $startDate = date('Y-m-d', strtotime('-30 days'));
            }

            // สถิติธุรกรรม
            $transactionStats = $this->getTransactionStats($startDate, $endDate);
            
            // สถิติรายได้และค่าใช้จ่าย
            $incomeExpenseStats = $this->getIncomeExpenseStats($startDate, $endDate);
            
            // สถิติตามหมวดหมู่
            $categoryStats = $this->getCategoryStats($startDate, $endDate);
            
            // สถิติตามวิธีการชำระเงิน
            $paymentStats = $this->getPaymentStats($startDate, $endDate);
            
            // สถิติผู้ใช้และองค์กร
            $userOrgStats = $this->getUserOrgStats();
            
            // ข้อมูลกราฟรายเดือน (6 เดือนล่าสุด)
            $monthlyChartData = $this->getMonthlyChartData();
            
            // ธุรกรรมล่าสุด
            $recentTransactions = $this->getRecentTransactions(10);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'date_range' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ],
                    'transaction_stats' => $transactionStats,
                    'income_expense_stats' => $incomeExpenseStats,
                    'category_stats' => $categoryStats,
                    'payment_stats' => $paymentStats,
                    'user_org_stats' => $userOrgStats,
                    'monthly_chart' => $monthlyChartData,
                    'recent_transactions' => $recentTransactions
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    private function getTransactionStats($startDate, $endDate)
    {
        $builder = $this->transactionModel->db->table('tb_transaction t');
        $builder->select('
            COUNT(*) as total_transactions,
            SUM(t.total) as total_amount,
            AVG(t.total) as average_amount
        ');
        $builder->where('DATE(t.datetime) >=', $startDate);
        $builder->where('DATE(t.datetime) <=', $endDate);
        $builder->where('t.deleted_at IS NULL');
        
        $result = $builder->get()->getRowArray();
        
        return [
            'total_transactions' => (int)($result['total_transactions'] ?? 0),
            'total_amount' => number_format((float)($result['total_amount'] ?? 0), 2),
            'average_amount' => number_format((float)($result['average_amount'] ?? 0), 2)
        ];
    }

    private function getIncomeExpenseStats($startDate, $endDate)
    {
        // รายได้ (parent_id = 3)
        $incomeBuilder = $this->transactionModel->db->table('tb_transaction t');
        $incomeBuilder->select('SUM(ti.price) as total_income');
        $incomeBuilder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
        $incomeBuilder->join('tb_category c', 'ti.category_id = c.id', 'left');
        $incomeBuilder->where('c.parent_id', 3);
        $incomeBuilder->where('DATE(t.datetime) >=', $startDate);
        $incomeBuilder->where('DATE(t.datetime) <=', $endDate);
        $incomeBuilder->where('t.deleted_at IS NULL');
        $incomeResult = $incomeBuilder->get()->getRowArray();

        // ค่าใช้จ่าย (parent_id = 4)
        $expenseBuilder = $this->transactionModel->db->table('tb_transaction t');
        $expenseBuilder->select('SUM(ti.price) as total_expense');
        $expenseBuilder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
        $expenseBuilder->join('tb_category c', 'ti.category_id = c.id', 'left');
        $expenseBuilder->where('c.parent_id', 4);
        $expenseBuilder->where('DATE(t.datetime) >=', $startDate);
        $expenseBuilder->where('DATE(t.datetime) <=', $endDate);
        $expenseBuilder->where('t.deleted_at IS NULL');
        $expenseResult = $expenseBuilder->get()->getRowArray();

        $totalIncome = (float)($incomeResult['total_income'] ?? 0);
        $totalExpense = (float)($expenseResult['total_expense'] ?? 0);
        $netProfit = $totalIncome - $totalExpense;

        return [
            'total_income' => number_format($totalIncome, 2),
            'total_expense' => number_format($totalExpense, 2),
            'net_profit' => number_format($netProfit, 2),
            'profit_margin' => $totalIncome > 0 ? number_format(($netProfit / $totalIncome) * 100, 2) : '0.00'
        ];
    }

    private function getCategoryStats($startDate, $endDate)
    {
        $builder = $this->transactionModel->db->table('tb_transaction t');
        $builder->select('
            c.name as category_name,
            COUNT(*) as transaction_count,
            SUM(ti.price) as total_amount
        ');
        $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
        $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
        $builder->where('DATE(t.datetime) >=', $startDate);
        $builder->where('DATE(t.datetime) <=', $endDate);
        $builder->where('t.deleted_at IS NULL');
        $builder->where('c.name IS NOT NULL');
        $builder->groupBy('c.id, c.name');
        $builder->orderBy('total_amount', 'DESC');
        $builder->limit(10);
        
        $results = $builder->get()->getResultArray();
        
        $stats = [];
        foreach ($results as $result) {
            $stats[] = [
                'category_name' => $result['category_name'],
                'transaction_count' => (int)$result['transaction_count'],
                'total_amount' => number_format((float)$result['total_amount'], 2)
            ];
        }
        
        return $stats;
    }

    private function getPaymentStats($startDate, $endDate)
    {
        $builder = $this->transactionModel->db->table('tb_transaction t');
        $builder->select('
            p.name_th as payment_name,
            p.type as payment_type,
            COUNT(*) as transaction_count,
            SUM(t.total) as total_amount
        ');
        $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
        $builder->where('DATE(t.datetime) >=', $startDate);
        $builder->where('DATE(t.datetime) <=', $endDate);
        $builder->where('t.deleted_at IS NULL');
        $builder->where('p.name_th IS NOT NULL');
        $builder->groupBy('p.id, p.name_th, p.type');
        $builder->orderBy('total_amount', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        $stats = [];
        foreach ($results as $result) {
            $stats[] = [
                'payment_name' => $result['payment_name'],
                'payment_type' => $result['payment_type'],
                'transaction_count' => (int)$result['transaction_count'],
                'total_amount' => number_format((float)$result['total_amount'], 2)
            ];
        }
        
        return $stats;
    }

    private function getUserOrgStats()
    {
        $userCount = $this->userModel->countAllResults();
        $orgCount = $this->organizationModel->countAllResults();
        $counterpartieCount = $this->counterpartieModel->countAllResults();
        
        return [
            'total_users' => $userCount,
            'total_organizations' => $orgCount,
            'total_counterparties' => $counterpartieCount
        ];
    }

    private function getMonthlyChartData()
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($month));
            
            // รายได้
            $incomeBuilder = $this->transactionModel->db->table('tb_transaction t');
            $incomeBuilder->select('SUM(ti.price) as total_income');
            $incomeBuilder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $incomeBuilder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $incomeBuilder->where('DATE_FORMAT(t.datetime, "%Y-%m")', $month);
            $incomeBuilder->where('c.parent_id', 3);
            $incomeBuilder->where('t.deleted_at IS NULL');
            $incomeResult = $incomeBuilder->get()->getRowArray();
            $incomeData[] = (float)($incomeResult['total_income'] ?? 0);
            
            // ค่าใช้จ่าย
            $expenseBuilder = $this->transactionModel->db->table('tb_transaction t');
            $expenseBuilder->select('SUM(ti.price) as total_expense');
            $expenseBuilder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
            $expenseBuilder->join('tb_category c', 'ti.category_id = c.id', 'left');
            $expenseBuilder->where('DATE_FORMAT(t.datetime, "%Y-%m")', $month);
            $expenseBuilder->where('c.parent_id', 4);
            $expenseBuilder->where('t.deleted_at IS NULL');
            $expenseResult = $expenseBuilder->get()->getRowArray();
            $expenseData[] = (float)($expenseResult['total_expense'] ?? 0);
        }
        
        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    private function getRecentTransactions($limit = 10)
    {
        $builder = $this->transactionModel->db->table('tb_transaction t');
        $builder->select('
            t.id,
            t.datetime,
            t.ref_no,
            t.descripton,
            t.total,
            c.name as category_name,
            p.name_th as payment_name,
            cp.name as counterpartie_name
        ');
        $builder->join('tb_transaction_item ti', 't.id = ti.transaction_id', 'left');
        $builder->join('tb_category c', 'ti.category_id = c.id', 'left');
        $builder->join('tb_payment p', 't.payment_id = p.id', 'left');
        $builder->join('tb_counterpartie cp', 't.counterpartie_id = cp.id', 'left');
        $builder->where('t.deleted_at IS NULL');
        $builder->orderBy('t.datetime', 'DESC');
        $builder->limit($limit);
        
        $results = $builder->get()->getResultArray();
        
        $transactions = [];
        foreach ($results as $result) {
            $transactions[] = [
                'id' => $result['id'],
                'datetime' => $result['datetime'],
                'ref_no' => $result['ref_no'],
                'description' => $result['descripton'],
                'total' => number_format((float)$result['total'], 2),
                'category_name' => $result['category_name'] ?: 'ไม่ระบุหมวดหมู่',
                'payment_name' => $result['payment_name'] ?: 'ไม่ระบุวิธีการชำระ',
                'counterpartie_name' => $result['counterpartie_name'] ?: 'ไม่ระบุคู่สัญญา'
            ];
        }
        
        return $transactions;
    }
}
