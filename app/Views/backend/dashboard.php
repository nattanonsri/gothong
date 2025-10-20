<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fas fa-chart-line me-2"></i>
                    แดชบอร์ดภาพรวม
                </h2>
                <p class="text-muted mb-0">ข้อมูลสรุปและสถิติการใช้งานระบบ</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="date-picker-container">
                    <label class="form-label text-muted small">เลือกช่วงวันที่</label>
                    <div class="input-group shadow-sm">
                        <span id="iconSchedule" class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                        <input type="text" id="datePicker" name="datePicker" class="form-control" placeholder="กรุณาเลือกวันที่">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="dashboardLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">กำลังโหลด...</span>
        </div>
        <p class="mt-2 text-muted">กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Dashboard Content -->
    <div id="dashboardContent" style="display: none;">
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    รายได้รวม
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalIncome">
                                    ฿0.00
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    ค่าใช้จ่ายรวม
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalExpense">
                                    ฿0.00
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    กำไรสุทธิ
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="netProfit">
                                    ฿0.00
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    จำนวนธุรกรรม
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTransactions">
                                    0
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4" style="min-height: 500px;">
            <!-- Monthly Income/Expense Chart -->
            <div class="col-xl-8 col-lg-7 d-flex">
                <div class="card shadow mb-4 flex-fill d-flex flex-column">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">กราฟรายได้และค่าใช้จ่ายรายเดือน</h6>
                    </div>
                    <div class="card-body flex-fill d-flex flex-column">
                        <div class="chart-area flex-fill">
                            <canvas id="monthlyChart" style="height: 100%; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="col-xl-4 col-lg-5 d-flex">
                <div class="card shadow mb-4 flex-fill d-flex flex-column">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">การกระจายตามหมวดหมู่</h6>
                    </div>
                    <div class="card-body flex-fill d-flex flex-column">
                        <div class="chart-pie flex-fill d-flex align-items-center justify-content-center">
                            <canvas id="categoryChart" style="height: 100%; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4" style="min-height: 400px;">
            <!-- Payment Methods -->
            <div class="col-xl-6 col-lg-6 d-flex">
                <div class="card shadow mb-4 flex-fill d-flex flex-column">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">สถิติตามวิธีการชำระเงิน</h6>
                    </div>
                    <div class="card-body flex-fill d-flex flex-column">
                        <div class="table-responsive flex-fill">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>วิธีการชำระ</th>
                                        <th>จำนวน</th>
                                        <th>ยอดรวม</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentStatsTable">
                                    <tr>
                                        <td colspan="3" class="text-center">กำลังโหลดข้อมูล...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Stats -->
            <!-- <div class="col-xl-6 col-lg-6 d-flex">
                <div class="card shadow mb-4 flex-fill d-flex flex-column">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">สถิติระบบ</h6>
                    </div>
                    <div class="card-body flex-fill d-flex flex-column">
                        <div class="row flex-fill">
                            <div class="col-md-6 mb-3 d-flex">
                                <div class="card bg-primary text-white shadow flex-fill">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="row no-gutters align-items-center w-100">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                    ผู้ใช้งาน
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold" id="totalUsers">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 d-flex">
                                <div class="card bg-success text-white shadow flex-fill">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="row no-gutters align-items-center w-100">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                    องค์กร
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold" id="totalOrganizations">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-building fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 d-flex">
                                <div class="card bg-info text-white shadow flex-fill">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="row no-gutters align-items-center w-100">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                    คู่สัญญา
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold" id="totalCounterparties">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-handshake fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 d-flex">
                                <div class="card bg-warning text-white shadow flex-fill">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="row no-gutters align-items-center w-100">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                    กำไรสุทธิ (%)
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold" id="profitMargin">
                                                    0%
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-percentage fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">ธุรกรรมล่าสุด</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>เลขที่อ้างอิง</th>
                                        <th>รายละเอียด</th>
                                        <th>หมวดหมู่</th>
                                        <!-- <th>คู่สัญญา</th> -->
                                        <th>ยอดเงิน</th>
                                    </tr>
                                </thead>
                                <tbody id="recentTransactionsTable">
                                    <tr>
                                        <td colspan="6" class="text-center">กำลังโหลดข้อมูล...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-primary {
    color: #4e73df !important;
}
.text-success {
    color: #1cc88a !important;
}
.text-info {
    color: #36b9cc !important;
}
.text-warning {
    color: #f6c23e !important;
}
.chart-area {
    position: relative;
    height: 10rem;
    width: 100%;
}
.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}
</style>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<script>
$(document).ready(function() {
    // Initialize date picker
    $('#datePicker').daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' - ',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            fromLabel: 'จาก',
            toLabel: 'ถึง',
            customRangeLabel: 'กำหนดเอง',
            weekLabel: 'W',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
            firstDay: 1
        }
    });

    // Load dashboard data
    loadDashboardData();

    // Date picker change event
    $('#datePicker').on('apply.daterangepicker', function(ev, picker) {
        loadDashboardData();
    });
});

function loadDashboardData() {
    const dateRange = $('#datePicker').val();
    const dates = dateRange.split(' - ');
    
    $.ajax({
        url: base_url + 'dashboard/getDashboardData',
        type: 'GET',
        data: {
            start_date: dates[0],
            end_date: dates[1]
        },
        beforeSend: function() {
            $('#dashboardLoading').show();
            $('#dashboardContent').hide();
        },
        success: function(response) {
            if (response.success) {
                updateDashboard(response.data);
                $('#dashboardLoading').hide();
                $('#dashboardContent').show();
            } else {
                Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
}

function updateDashboard(data) {
    // Update summary cards
    $('#totalIncome').text('฿' + data.income_expense_stats.total_income);
    $('#totalExpense').text('฿' + data.income_expense_stats.total_expense);
    $('#netProfit').text('฿' + data.income_expense_stats.net_profit);
    $('#totalTransactions').text(data.transaction_stats.total_transactions);
    $('#profitMargin').text(data.income_expense_stats.profit_margin + '%');

    // Update system stats
    $('#totalUsers').text(data.user_org_stats.total_users);
    $('#totalOrganizations').text(data.user_org_stats.total_organizations);
    $('#totalCounterparties').text(data.user_org_stats.total_counterparties);

    // Update payment stats table
    updatePaymentStatsTable(data.payment_stats);

    // Update recent transactions table
    updateRecentTransactionsTable(data.recent_transactions);

    // Update charts
    updateMonthlyChart(data.monthly_chart);
    updateCategoryChart(data.category_stats);
}

function updatePaymentStatsTable(paymentStats) {
    let html = '';
    if (paymentStats.length > 0) {
        paymentStats.forEach(function(stat) {
            html += `
                <tr>
                    <td>${stat.payment_name}</td>
                    <td>${stat.transaction_count}</td>
                    <td>฿${stat.total_amount}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="3" class="text-center">ไม่มีข้อมูล</td></tr>';
    }
    $('#paymentStatsTable').html(html);
}

function updateRecentTransactionsTable(transactions) {
    let html = '';
    if (transactions.length > 0) {
        transactions.forEach(function(transaction) {
            html += `
                <tr>
                    <td>${moment(transaction.datetime).format('DD/MM/YYYY HH:mm')}</td>
                    <td>${transaction.ref_no || '-'}</td>
                    <td>${transaction.description || '-'}</td>
                    <td>${transaction.category_name}</td>
                    <td>฿${transaction.total}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="6" class="text-center">ไม่มีข้อมูล</td></tr>';
    }
    $('#recentTransactionsTable').html(html);
}

function updateMonthlyChart(chartData) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.monthlyChartInstance) {
        window.monthlyChartInstance.destroy();
    }
    
    window.monthlyChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'รายได้',
                data: chartData.income,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'ค่าใช้จ่าย',
                data: chartData.expense,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '฿' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ฿' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateCategoryChart(categoryStats) {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.categoryChartInstance) {
        window.categoryChartInstance.destroy();
    }
    
    const labels = categoryStats.map(stat => stat.category_name);
    const data = categoryStats.map(stat => parseFloat(stat.total_amount.replace(/,/g, '')));
    
    window.categoryChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF',
                    '#4BC0C0',
                    '#FF6384'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ฿' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
</script>

<?= $this->endSection() ?>