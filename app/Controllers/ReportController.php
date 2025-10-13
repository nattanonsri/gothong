<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ReportController extends BaseController
{
    /**
     * หน้ารายงานยอดรายได้โดยรวม
     */
    public function totalIncome()
    {
        $data = [
            'title' => lang('app.totalIncome'),
            'reportType' => 'income'
        ];
        
        return view('backend/report_income', $data);
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
        
        return view('backend/report_expenses', $data);
    }
}

