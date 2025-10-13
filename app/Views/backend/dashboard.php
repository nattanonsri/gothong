<?= $this->extend('front/template') ?>

<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <!-- Header Section -->
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

    <!-- Key Metrics Section -->
    <div class="metrics-section mb-4">
        <div class="row g-3">
            <!-- Metric Card 1: มูลค่าคูปอง -->
            <div class="col-lg-4">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="metric-content">
                        <h6 class="metric-label">
                            <i class="fas fa-ticket-alt me-1"></i>
                        </h6>
                        <div class="metric-value" id="coupon">
                
                                <span class="no-data">ไม่มีข้อมูล</span>
               
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric Card 2: จำนวนคูปอง -->
            <div class="col-lg-4">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="metric-content">
                        <h6 class="metric-label">
                            <i class="fas fa-list-ol me-1"></i>
                        </h6>
                        <div class="metric-value" id="total">
                                <span class="no-data">ไม่มีข้อมูล</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric Card 3: ผู้รับคูปอง -->
            <div class="col-lg-4">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-content">
                        <h6 class="metric-label">
                            <i class="fas fa-user-check me-1"></i>
                        </h6>
                        <div class="metric-value" id="customers">
                                <span class="no-data">ไม่มีข้อมูล</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Dashboard Header */
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

    .date-picker-container label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    /* Metric Cards */
    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        min-height: 200px;
    }

    .metric-card:nth-child(1) {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .metric-card:nth-child(2) {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .metric-card:nth-child(3) {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 0;
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
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: baseline;
        gap: 0.25rem;
    }

    .metric-value .currency-symbol {
        font-size: 1.5rem;
        opacity: 0.8;
        font-weight: 600;
    }

    .metric-value .amount {
        font-size: 2.5rem;
        font-weight: 800;
    }

    .metric-value .count {
        font-size: 2.5rem;
        font-weight: 800;
    }

    .metric-value .unit {
        font-size: 1.2rem;
        opacity: 0.8;
        font-weight: 600;
        margin-left: 0.25rem;
    }

    .metric-value .no-data {
        font-size: 1.5rem;
        font-weight: 600;
        opacity: 0.7;
        font-style: italic;
    }

    /* Dynamic font sizing for large numbers */
    .metric-value .amount.large-number,
    .metric-value .count.large-number {
        font-size: 2.0rem !important;
    }

    .metric-value .amount.extra-large-number,
    .metric-value .count.extra-large-number {
        font-size: 1.8rem !important;
    }

    /* Tooltip styling */
    .tooltip-inner {
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        max-width: 300px;
    }

    /* Hover effect for numbers with tooltips */
    .metric-value .amount[title],
    .metric-value .count[title] {
        cursor: help;
        transition: all 0.3s ease;
    }

    .metric-value .amount[title]:hover,
    .metric-value .count[title]:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }

    .metric-description {
        font-size: 0.75rem;
        opacity: 0.8;
        font-weight: 500;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        color: inherit;
        opacity: 0.7;
    }

    /* Status and Analytics Cards */
    .status-card,
    .analytics-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-header {
        border: none;
        padding: 1.5rem 1.5rem 1rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Status Legend */
    .status-legend {
        padding: 1rem 0;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
    }

    .legend-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 0.75rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .legend-label {
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }

    /* Chart Containers */
    .chart-container {
        position: relative;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-fallback {
        display: none;
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }

    canvas {
        max-height: 300px;
        border-radius: 8px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-header {
            text-align: center;
        }

        .dashboard-header .col-md-6:last-child {
            text-align: center !important;
            margin-top: 1rem;
        }

        .metric-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
            min-height: 180px;
        }

        .metric-value {
            font-size: 2rem;
        }

        .metric-value .amount,
        .metric-value .count {
            font-size: 2rem;
        }

        .metric-value .currency-symbol {
            font-size: 1.2rem;
        }

        .metric-value .unit {
            font-size: 1rem;
        }

        .metric-value .no-data {
            font-size: 1.2rem;
        }

        /* Mobile responsive for large numbers */
        .metric-value .amount.large-number,
        .metric-value .count.large-number {
            font-size: 1.6rem !important;
        }

        .metric-value .amount.extra-large-number,
        .metric-value .count.extra-large-number {
            font-size: 1.4rem !important;
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .chart-container {
            height: 250px;
        }

        /* Stack metrics vertically on mobile */
        .metrics-section .col-lg-4 {
            margin-bottom: 1rem;
        }
    }

    /* Animation for loading states */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .loading-spinner i {
        animation: pulse 2s infinite;
    }

    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    }

    /* Card Header Improvements */
    .card-header h5 {
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    .card-header small {
        display: block;
        margin-top: 0.25rem;
        font-weight: 400;
    }

    /* Hover Effects */
    .input-group:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Better spacing */
    .metrics-section,
    .status-section,
    .analytics-section {
        margin-bottom: 2rem;
    }

    /* Top Stores Ranking Styles */
    .store-row {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .store-row:hover {
        background-color: #f8f9fa;
        border-left-color: #28a745;
        transform: translateX(2px);
    }

    .rank-badge {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 40px;
    }

    .rank-medal {
        font-size: 1.5rem;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .store-info .store-name {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .store-info small {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .sales-amount {
        font-size: 1.1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .progress-container .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-container .progress-bar {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        border-radius: 10px;
        transition: width 0.8s ease-in-out;
    }

    /* Special styling for top 3 ranks */
    .store-row[data-rank="1"] {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
    }

    .store-row[data-rank="2"] {
        background: linear-gradient(135deg, rgba(192, 192, 192, 0.1) 0%, rgba(192, 192, 192, 0.05) 100%);
    }

    .store-row[data-rank="3"] {
        background: linear-gradient(135deg, rgba(205, 127, 50, 0.1) 0%, rgba(205, 127, 50, 0.05) 100%);
    }

    /* Responsive adjustments for top stores table */
    @media (max-width: 768px) {
        .sales-amount {
            font-size: 0.9rem;
        }
        
        .store-info .store-name {
            font-size: 0.9rem;
        }
        
        .rank-medal {
            font-size: 1.2rem;
        }
    }

    /* Category and Brand Ranking Styles */
    .category-ranking-list,
    .brand-ranking-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .ranking-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.3s ease;
        position: relative;
    }

    .ranking-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }

    .ranking-item:last-child {
        border-bottom: none;
    }

    .ranking-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rank-info {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .rank-info .rank-badge {
        width: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 0.75rem;
    }

    .item-details .item-name {
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .item-details small {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .sales-info {
        text-align: right;
        min-width: 120px;
    }

    .sales-info .sales-amount {
        font-size: 0.9rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .progress-mini {
        width: 100%;
    }

    .progress-mini .progress {
        background-color: #e9ecef;
        border-radius: 2px;
        margin-bottom: 2px;
    }

    .progress-mini .progress-bar {
        border-radius: 2px;
        transition: width 0.6s ease;
    }

    /* Special highlighting for top 3 in categories and brands */
    .category-item[data-rank="1"],
    .brand-item[data-rank="1"] {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.08) 0%, rgba(255, 215, 0, 0.03) 100%);
        border-left: 3px solid #ffd700;
    }

    .category-item[data-rank="2"],
    .brand-item[data-rank="2"] {
        background: linear-gradient(135deg, rgba(192, 192, 192, 0.08) 0%, rgba(192, 192, 192, 0.03) 100%);
        border-left: 3px solid #c0c0c0;
    }

    .category-item[data-rank="3"],
    .brand-item[data-rank="3"] {
        background: linear-gradient(135deg, rgba(205, 127, 50, 0.08) 0%, rgba(205, 127, 50, 0.03) 100%);
        border-left: 3px solid #cd7f32;
    }

    /* Responsive adjustments for ranking items */
    @media (max-width: 768px) {
        .ranking-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .sales-info {
            align-self: flex-end;
            min-width: auto;
        }
        
        .rank-info .rank-badge {
            width: 35px;
            margin-right: 0.5rem;
        }
        
        .item-details .item-name {
            font-size: 0.85rem;
        }
        
        .sales-info .sales-amount {
            font-size: 0.85rem;
        }
    }

    /* Scrollbar styling for ranking lists */
    .category-ranking-list::-webkit-scrollbar,
    .brand-ranking-list::-webkit-scrollbar {
        width: 4px;
    }

    .category-ranking-list::-webkit-scrollbar-track,
    .brand-ranking-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .category-ranking-list::-webkit-scrollbar-thumb,
    .brand-ranking-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }

    .category-ranking-list::-webkit-scrollbar-thumb:hover,
    .brand-ranking-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>


<script>
   

    $(document).ready(function() {
        // Apply dynamic formatting to all metric values
        $('.amount').each(function() {
            const value = $(this).data('value');
            if (value) {
                const formattedValue = formatLargeNumber(value, this);
                $(this).text(formattedValue);
                
                // Add tooltip with full value
                $(this).attr('title', '฿' + parseFloat(value).toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        });
        
        $('.count').each(function() {
            const value = $(this).data('value');
            if (value) {
                const formattedValue = formatCount(value, this);
                $(this).text(formattedValue);
                
                // Add tooltip with full value
                $(this).attr('title', parseInt(value).toLocaleString('th-TH') + ' คน');
            }
        });
        
        // Initialize tooltips
        $('[title]').tooltip();

        $('#datePicker').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'ตกลง',
                cancelLabel: 'ยกเลิก',
                fromLabel: 'จาก',
                toLabel: 'ถึง',
                customRangeLabel: 'กำหนดเอง',
                weekLabel: 'W',
                daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
                monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                ],
                firstDay: 1
            },
            autoApply: false,
            showDropdowns: true,
            opens: 'left'
        });

        $('#datePicker').on('apply.daterangepicker', function(ev, picker) {
            showLoading();

            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');

            // Show user feedback
            showToast('กำลังโหลดข้อมูลใหม่...', 'info');

            // Redirect with new date parameters
            var url = new URL(window.location.href);
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
            url.searchParams.set('page', '1');

            setTimeout(() => {
                window.location.href = url.toString();
            }, 500);
        });

        // Add loading animation to table rows
        $('.table tbody tr').each(function(index) {
            $(this).css('animation-delay', (index * 50) + 'ms');
        });

        // Initialize Receipt Status Chart
        initReceiptStatusChart();
    })

    function initReceiptStatusChart() {
        const receiptStatusData = <?= json_encode($receipt_status_counts ?? []) ?>;
        
        // Check if we have data
        const hasData = Object.values(receiptStatusData).some(count => count > 0);
        
        if (!hasData) {
            $('#percentageFallback').show();
            $('#chartContainer').hide();
            return;
        }

        // Hide fallback and show chart
        $('#percentageFallback').hide();
        $('#chartContainer').show();

        // Prepare chart data
        const labels = Object.keys(receiptStatusData);
        const data = Object.values(receiptStatusData);
        const total = data.reduce((sum, count) => sum + count, 0);
        
        // Chart colors matching the legend
        const colors = [
            'rgba(251, 191, 36, 0.8)',   // รอตรวจสอบ - Orange
            'rgba(16, 185, 129, 0.8)',   // ตรวจสอบเสร็จสิ้น - Green
            'rgba(59, 130, 246, 0.8)'    // กำลังทำรายการ - Blue
        ];
        
        const borderColors = [
            'rgba(245, 158, 11, 1)',
            'rgba(5, 150, 105, 1)',
            'rgba(29, 78, 216, 1)'
        ];

        const ctx = document.getElementById('percentageChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: borderColors,
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We have custom legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label;
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} รายการ (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Show loading overlay
    function showLoading() {
        $('#loadingOverlay').removeClass('d-none');
    }

    // Hide loading overlay
    function hideLoading() {
        $('#loadingOverlay').addClass('d-none');
    }

    function clearAllFilters() {
        Swal.fire({
            title: 'คุณต้องการล้างตัวกรองทั้งหมดหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                showToast('กำลังล้างตัวกรอง...', 'info');

                var url = new URL(window.location.href);
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
                url.searchParams.delete('page');

                window.location.href = url.toString();
            }

        });
    }
</script>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>