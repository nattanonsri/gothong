<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-money-bill-wave me-2"></i>
                    จัดการช่องทางชำระเงิน
                </h2>
                <p class="text-muted mb-0">จัดการข้อมูลช่องทางการชำระเงิน</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" id="btnAddPayment">
                    <i class="fa-solid fa-plus me-2"></i>
                    เพิ่มช่องทางชำระเงิน
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="paymentsTable" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ประเภท</th>
                            <th>รหัส</th>
                            <th>ชื่อ (ไทย)</th>
                            <th>ชื่อ (อังกฤษ)</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">เพิ่มช่องทางชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="paymentUuid" name="uuid">

                    <div class="mb-3">
                        <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type">
                            <option value="">เลือกประเภท</option>
                            <option value="cash">เงินสด (Cash)</option>
                            <option value="promptpay">พร้อมเพย์ (PromptPay)</option>
                            <option value="debitcard">บัตรเดบิต (Debit Card)</option>
                            <option value="e-wallet">กระเป๋าเงินอิเล็กทรอนิกส์ (E-Wallet)</option>
                        </select>
                        <small id="typeError" class="text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รหัส</label>
                        <input type="text" class="form-control" id="code" name="code" oninput="this.value = this.value.replace(/[^A-Za-z0-9\-]/g, '').toUpperCase()" placeholder="เช่น CASH, PPAY">
                        <small class="text-muted">รหัสสำหรับระบุช่องทางชำระเงิน (ไม่ซ้ำกัน)</small>
                        <small id="codeError" class="text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ชื่อภาษาไทย <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_th" name="name_th" oninput="this.value = this.value.replace(/[^\u0E00-\u0E7F]/g, '')"  placeholder="เช่น เงินสด, พร้อมเพย์">
                        <small id="nameThError" class="text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ชื่อภาษาอังกฤษ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_en" name="name_en" oninput="this.value = this.value.replace(/[^\w\s\-]/g, '')" placeholder="เช่น Cash, PromptPay">
                        <small id="nameEnError" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSavePayment">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>


<script>
    const BASE_URL = '<?= base_url() ?>';

    let paymentsDataTable;
    let currentEditUuid = null;

    $(document).ready(function() {
        initDataTable();
        initEventHandlers();
    });

    function initDataTable() {
        paymentsDataTable = $('#paymentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: BASE_URL + 'payment/list',
                type: 'POST',
                error: function(xhr, error, code) {
                    console.error('DataTable error:', error);
                    Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'type',
                    render: function(data) {
                        const types = {
                            'cash': '<span class="badge bg-success">เงินสด</span>',
                            'promptpay': '<span class="badge bg-primary">พร้อมเพย์</span>',
                            'debitcard': '<span class="badge bg-info">บัตรเดบิต</span>',
                            'e-wallet': '<span class="badge bg-warning">E-Wallet</span>'
                        };
                        return types[data] || data;
                    }
                },
                {
                    data: 'code',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'name_th',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'name_en',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-warning btn-edit" data-uuid="${row.uuid}" title="แก้ไข">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-delete" data-uuid="${row.uuid}" title="ลบ">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [
                [1, 'asc']
            ],
            language: {
                decimal: "",
                emptyTable: "ไม่มีข้อมูลในตาราง",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "แสดง _MENU_ รายการ",
                loadingRecords: "กำลังโหลด...",
                processing: "กำลังประมวลผล...",
                search: "ค้นหา:",
                searchPlaceholder: "ค้นหา...",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                },
                aria: {
                    sortAscending: ": เปิดใช้งานการเรียงลำดับจากน้อยไปมาก",
                    sortDescending: ": เปิดใช้งานการเรียงลำดับจากมากไปน้อย"
                }
            }
        });
    }

    function initEventHandlers() {
        $('#btnAddPayment').on('click', function() {
            openPaymentModal();
        });

        $(document).on('click', '.btn-edit', function() {
            const uuid = $(this).data('uuid');
            editPayment(uuid);
        });

        $(document).on('click', '.btn-delete', function() {
            const uuid = $(this).data('uuid');
            deletePayment(uuid);
        });

        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            savePayment();
        });
        
        // Clear errors when modal is hidden
        $('#paymentModal').on('hidden.bs.modal', function() {
            clearAllErrors();
        });
        
        // Real-time validation
        $('#type').on('change', function() {
            clearError('type');
            if (!$(this).val()) {
                showError('type', 'กรุณาเลือกประเภท');
            } else {
                showSuccess('type');
            }
        });
        
        $('#code').on('input', function() {
            clearError('code');
            const value = $(this).val().trim();
            if (value) {
                if (value.length < 2) {
                    showError('code', 'รหัสต้องมีอย่างน้อย 2 ตัวอักษร');
                } else if (value.length > 10) {
                    showError('code', 'รหัสต้องไม่เกิน 10 ตัวอักษร');
                } else {
                    showSuccess('code');
                }
            }
        });
        
        $('#name_th').on('input', function() {
            clearError('nameTh');
            const value = $(this).val().trim();
            if (!value) {
                showError('nameTh', 'กรุณากรอกชื่อภาษาไทย');
            } else if (value.length < 2) {
                showError('nameTh', 'ชื่อภาษาไทยต้องมีอย่างน้อย 2 ตัวอักษร');
            } else if (value.length > 50) {
                showError('nameTh', 'ชื่อภาษาไทยต้องไม่เกิน 50 ตัวอักษร');
            } else {
                showSuccess('nameTh');
            }
        });
        
        $('#name_en').on('input', function() {
            clearError('nameEn');
            const value = $(this).val().trim();
            if (!value) {
                showError('nameEn', 'กรุณากรอกชื่อภาษาอังกฤษ');
            } else if (value.length < 2) {
                showError('nameEn', 'ชื่อภาษาอังกฤษต้องมีอย่างน้อย 2 ตัวอักษร');
            } else if (value.length > 50) {
                showError('nameEn', 'ชื่อภาษาอังกฤษต้องไม่เกิน 50 ตัวอักษร');
            } else {
                showSuccess('nameEn');
            }
        });
    }

    function openPaymentModal() {
        currentEditUuid = null;
        $('#paymentModalLabel').text('เพิ่มช่องทางชำระเงิน');
        $('#paymentForm')[0].reset();
        $('#paymentUuid').val('');
        clearAllErrors();

        $('#paymentModal').modal('show');
    }
    
    // Helper functions for error handling
    function showError(fieldName, message) {
        const errorElement = $('#' + fieldName + 'Error');
        errorElement.text(message);
        $('#' + fieldName).removeClass('is-valid').addClass('is-invalid');
    }
    
    function showSuccess(fieldName) {
        const errorElement = $('#' + fieldName + 'Error');
        errorElement.text('');
        $('#' + fieldName).removeClass('is-invalid').addClass('is-valid');
    }
    
    function clearError(fieldName) {
        const errorElement = $('#' + fieldName + 'Error');
        errorElement.text('');
        $('#' + fieldName).removeClass('is-invalid is-valid');
    }
    
    function clearAllErrors() {
        const fields = ['type', 'code', 'nameTh', 'nameEn'];
        fields.forEach(field => {
            clearError(field);
        });
    }

    function savePayment() {
        // Clear all previous errors
        clearAllErrors();
        
        let isValid = true;
        
        // Validate type (required)
        if (!$('#type').val()) {
            showError('type', 'กรุณาเลือกประเภท');
            isValid = false;
        }
        
        // Validate code (optional but if provided, must be unique format)
        const code = $('#code').val().trim();
        if (code) {
            if (code.length < 2) {
                showError('code', 'รหัสต้องมีอย่างน้อย 2 ตัวอักษร');
                isValid = false;
            } else if (code.length > 10) {
                showError('code', 'รหัสต้องไม่เกิน 10 ตัวอักษร');
                isValid = false;
            }
        }
        
        // Validate name_th (required)
        const nameTh = $('#name_th').val().trim();
        if (!nameTh) {
            showError('nameTh', 'กรุณากรอกชื่อภาษาไทย');
            isValid = false;
        } else if (nameTh.length < 2) {
            showError('nameTh', 'ชื่อภาษาไทยต้องมีอย่างน้อย 2 ตัวอักษร');
            isValid = false;
        } else if (nameTh.length > 50) {
            showError('nameTh', 'ชื่อภาษาไทยต้องไม่เกิน 50 ตัวอักษร');
            isValid = false;
        }
        
        // Validate name_en (required)
        const nameEn = $('#name_en').val().trim();
        if (!nameEn) {
            showError('nameEn', 'กรุณากรอกชื่อภาษาอังกฤษ');
            isValid = false;
        } else if (nameEn.length < 2) {
            showError('nameEn', 'ชื่อภาษาอังกฤษต้องมีอย่างน้อย 2 ตัวอักษร');
            isValid = false;
        } else if (nameEn.length > 50) {
            showError('nameEn', 'ชื่อภาษาอังกฤษต้องไม่เกิน 50 ตัวอักษร');
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }

        const formData = new FormData($('#paymentForm')[0]);
        const url = currentEditUuid ?
            BASE_URL + 'payment/update/' + currentEditUuid :
            BASE_URL + 'payment/create';

        Swal.fire({
            title: 'กำลังบันทึก...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                Swal.close();
                if (res.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#paymentModal').modal('hide');
                        paymentsDataTable.ajax.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                if(xhr.responseJSON && xhr.responseJSON.status == 400) {
                    // Handle validation errors from server
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const fieldName = field === 'name_th' ? 'nameTh' : 
                                            field === 'name_en' ? 'nameEn' : field;
                            showError(fieldName, errors[field]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'แจ้งเตือน',
                            text: xhr.responseJSON.message,
                            confirmButtonText: 'ตกลง',
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                    });
                }
               
            }
        });
    }

    function editPayment(uuid) {
        $.ajax({
            url: BASE_URL + 'payment/get/' + uuid,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 200) {
                    const data = res.data;
                    currentEditUuid = uuid;

                    $('#paymentModalLabel').text('แก้ไขช่องทางชำระเงิน');
                    $('#paymentUuid').val(uuid);
                    $('#type').val(data.type);
                    $('#code').val(data.code);
                    $('#name_th').val(data.name_th);
                    $('#name_en').val(data.name_en);
                    
                    // Clear all errors when editing
                    clearAllErrors();

                    $('#paymentModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                if(xhr.responseJSON && xhr.responseJSON.status == 400) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: xhr.responseJSON.message,
                        confirmButtonText: 'ตกลง',
                    });
                }
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'ไม่สามารถโหลดข้อมูลได้'
                });
            }
        });
    }

    function deletePayment(uuid) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบช่องทางชำระเงินนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'payment/delete/' + uuid,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                paymentsDataTable.ajax.reload();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        if(xhr.responseJSON && xhr.responseJSON.status == 400) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'แจ้งเตือน',
                                text: xhr.responseJSON.message,
                                confirmButtonText: 'ตกลง',
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: xhr.responseJSON ? xhr.responseJSON.message : 'ไม่สามารถลบข้อมูลได้'
                        });
                    }
                });
            }
        });
    }
</script>

<?= $this->endSection() ?>