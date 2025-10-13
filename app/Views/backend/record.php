<?= $this->extend('front/template') ?>

<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <!-- Header Section -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-file-pen me-2"></i>
                    <?= lang('app.record') ?>
                </h2>
                <p class="text-muted mb-0">จัดการข้อมูลรายการทั้งหมด</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" id="btnAddRecord">
                    <i class="fa-solid fa-plus me-2"></i>
                    เพิ่มรายการใหม่
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" class="form-control" id="searchRecord" placeholder="ค้นหารายการ...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ช่วงวันที่</label>
                    <input type="text" id="dateRangeRecord" class="form-control" placeholder="เลือกช่วงวันที่">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button class="btn btn-success w-100" id="btnSearchRecord">
                            <i class="fa-solid fa-search me-2"></i>
                            ค้นหา
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="recordTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">รายการ</th>
                            <th width="15%">หมวดหมู่</th>
                            <th width="15%">จำนวน</th>
                            <th width="15%">วันที่</th>
                            <th width="15%">สถานะ</th>
                            <th width="15%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                                <p>ยังไม่มีข้อมูลรายการ</p>
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

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .btn {
        border-radius: 8px;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize date range picker
        $('#dateRangeRecord').daterangepicker({
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

        $('#dateRangeRecord').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        // Add record button
        $('#btnAddRecord').click(function() {
            Swal.fire({
                title: 'เพิ่มรายการใหม่',
                text: 'ฟังก์ชันนี้กำลังพัฒนา',
                icon: 'info'
            });
        });

        // Search button
        $('#btnSearchRecord').click(function() {
            const searchTerm = $('#searchRecord').val();
            console.log('Search:', searchTerm);
        });
    });
</script>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>

