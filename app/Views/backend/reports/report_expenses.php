<?= $this->extend('front/template') ?>

<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <!-- Header Section -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-money-bill-wave me-2"></i>
                    <?= $title ?? lang('app.totalExpenses') ?>
                </h2>
                <p class="text-muted mb-0">รายงานสรุปยอดค่าใช้จ่ายโดยรวม</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-success" id="btnExportExcel">
                        <i class="fa-solid fa-file-excel me-2"></i>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ช่วงวันที่</label>
                    <input type="text" id="dateRangeExpenses" class="form-control" placeholder="เลือกช่วงวันที่">
                </div>
                <div class="col-md-4">
                    <label class="form-label">หมวดหมู่</label>
                    <select class="form-select" id="selectCategory">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button class="btn btn-primary w-100" id="btnFilterExpenses">
                            <i class="fa-solid fa-filter me-2"></i>
                            กรองข้อมูล
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="metric-card expenses-card">
                <div class="metric-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-label">ค่าใช้จ่ายรวมทั้งหมด</h6>
                    <div class="metric-value">
                        <span class="amount">0.00</span>
                        <span class="currency">บาท</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card expenses-card">
                <div class="metric-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-label">จำนวนรายการ</h6>
                    <div class="metric-value">
                        <span class="count">0</span>
                        <span class="unit">รายการ</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card expenses-card">
                <div class="metric-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-label">ค่าเฉลี่ยต่อรายการ</h6>
                    <div class="metric-value">
                        <span class="amount">0.00</span>
                        <span class="currency">บาท</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-chart-bar me-2 text-danger"></i>
                        กราฟค่าใช้จ่ายรายเดือน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="expensesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-pie-chart me-2 text-danger"></i>
                        ค่าใช้จ่ายตามหมวดหมู่
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fa-solid fa-table me-2 text-danger"></i>
                รายละเอียดค่าใช้จ่าย
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="expensesTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">วันที่</th>
                            <th width="25%">รายการ</th>
                            <th width="15%">หมวดหมู่</th>
                            <th width="15%" class="text-end">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fa-solid fa-chart-simple fa-3x mb-3"></i>
                                <p>ยังไม่มีข้อมูลค่าใช้จ่าย</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-header {
        padding: 1.5rem 0;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .dashboard-title {
        color: #1f2937;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .card {
        border-radius: 12px;
        border: none;
    }

    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .expenses-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .metric-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        z-index: 1;
        color: white;
    }

    .metric-content {
        position: relative;
        z-index: 1;
    }

    .metric-label {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
        opacity: 0.9;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .metric-value .currency,
    .metric-value .unit {
        font-size: 1rem;
        opacity: 0.8;
        font-weight: 600;
        margin-left: 0.25rem;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .btn {
        border-radius: 8px;
    }

    .card-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.25rem 1.5rem;
    }
</style>

<script>
    let expensesChart, categoryChart;
    let currentFilters = {
        start_date: '',
        end_date: '',   
        category_id: ''
    };

    $(document).ready(function() {
        $('#dateRangeExpenses').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'ตกลง',
                cancelLabel: 'ยกเลิก',
                daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
                monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                ]
            },
            autoUpdateInput: false
        });

        $('#dateRangeExpenses').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            currentFilters.start_date = picker.startDate.format('YYYY-MM-DD');
            currentFilters.end_date = picker.endDate.format('YYYY-MM-DD');
        });

        loadCategories();

        $('#btnFilterExpenses').click(function() {
            loadExpensesData();
        });

        $('#btnExportExcel').click(function() {
            exportExpensesData();
        });

        initExpensesCharts();
        loadExpensesData();
    });

    function loadCategories() {
        $.ajax({
            url: base_url + '/report/categories',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#selectCategory');
                    select.empty().append('<option value="">ทั้งหมด</option>');
                    
                    response.data.forEach(function(category) {
                        select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            },
            error: function() {
                console.error('Error loading categories');
            }
        });
    }

    function loadExpensesData() {
        currentFilters.category_id = $('#selectCategory').val();

        showLoading();

        $.ajax({
            url: base_url + '/report/expenses-data',
            type: 'GET',
            data: currentFilters,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateSummaryCards(response.data.summary);
                    updateCharts(response.data.charts);
                    updateTable(response.data.transactions);
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function updateSummaryCards(summary) {
        $('.metric-card .amount').first().text(summary.total_amount);
        $('.metric-card .count').text(summary.total_count);
        $('.metric-card .amount').last().text(summary.average_amount);
    }

    function updateCharts(charts) {
        if (expensesChart) {
            expensesChart.data.labels = charts.monthly.labels;
            expensesChart.data.datasets[0].data = charts.monthly.data;
            expensesChart.update();
        }

        if (categoryChart) {
            if (charts.category.length > 0) {
                categoryChart.data.labels = charts.category.map(item => item.label);
                categoryChart.data.datasets[0].data = charts.category.map(item => item.value);
                categoryChart.data.datasets[0].backgroundColor = generateColors(charts.category.length);
            } else {
                categoryChart.data.labels = ['ไม่มีข้อมูล'];
                categoryChart.data.datasets[0].data = [1];
                categoryChart.data.datasets[0].backgroundColor = ['rgba(200, 200, 200, 0.8)'];
            }
            categoryChart.update();
        }
    }

    function updateTable(transactions) {
        const tbody = $('#expensesTable tbody');
        tbody.empty();

        if (transactions.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fa-solid fa-chart-simple fa-3x mb-3"></i>
                        <p>ยังไม่มีข้อมูลค่าใช้จ่าย</p>
                    </td>
                </tr>
            `);
        } else {
            transactions.forEach(function(transaction, index) {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${formatDate(transaction.datetime)}</td>
                        <td>${transaction.item_name || transaction.descripton || '-'}</td>
                        <td>${transaction.category_name || '-'}</td>
                        <td class="text-end">${formatCurrency(transaction.price)}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
    }

    function initExpensesCharts() {
        const ctxExpenses = document.getElementById('expensesChart').getContext('2d');
        expensesChart = new Chart(ctxExpenses, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'ค่าใช้จ่าย (บาท)',
                    data: [],
                    backgroundColor: 'rgba(245, 87, 108, 0.8)',
                    borderColor: 'rgba(245, 87, 108, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });

        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        categoryChart = new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: []
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function generateColors(count) {
        const colors = [
            'rgba(245, 87, 108, 0.8)',
            'rgba(240, 147, 251, 0.8)',
            'rgba(255, 193, 7, 0.8)',
            'rgba(220, 53, 69, 0.8)',
            'rgba(13, 110, 253, 0.8)',
            'rgba(111, 66, 193, 0.8)',
            'rgba(253, 126, 20, 0.8)',
            'rgba(25, 135, 84, 0.8)'
        ];
        
        const result = [];
        for (let i = 0; i < count; i++) {
            result.push(colors[i % colors.length]);
        }
        return result;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('th-TH');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function showLoading() {
        $('#btnFilterExpenses').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>กำลังโหลด...');
    }

    function hideLoading() {
        $('#btnFilterExpenses').prop('disabled', false).html('<i class="fa-solid fa-filter me-2"></i>กรองข้อมูล');
    }

    function exportExpensesData() {
        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (currentFilters.start_date === '' && currentFilters.end_date === '' && currentFilters.category_id === '') {
            Swal.fire({
                title: 'ยืนยันการส่งออก',
                text: 'คุณต้องการส่งออกข้อมูลทั้งหมดหรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ส่งออก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    performExport();
                }
            });
        } else {
            performExport();
        }
    }

    function performExport() {
        // แสดง loading
        const originalText = $('#btnExportExcel').html();
        $('#btnExportExcel').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>กำลังส่งออก...');

        // สร้าง URL พร้อม parameters
        const params = new URLSearchParams();
        if (currentFilters.start_date) params.append('start_date', currentFilters.start_date);
        if (currentFilters.end_date) params.append('end_date', currentFilters.end_date);
        if (currentFilters.category_id) params.append('category_id', currentFilters.category_id);

        const exportUrl = base_url + '/report/export-expenses' + (params.toString() ? '?' + params.toString() : '');

        // สร้าง hidden link เพื่อ download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // คืนสถานะปุ่ม
        setTimeout(() => {
            $('#btnExportExcel').prop('disabled', false).html(originalText);
            Swal.fire({
                title: 'สำเร็จ',
                text: 'ไฟล์รายงานถูกส่งออกเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1000);
    }
</script>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>

