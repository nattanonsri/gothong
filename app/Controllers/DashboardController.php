<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function goto_dashboard()
    {

        return view('backend/dashboard');
    }
}
