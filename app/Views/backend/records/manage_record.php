<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-file-pen me-2"></i>
                    <?= lang('app.record') ?>
                </h2>
                <p class="text-muted mb-0">จัดการข้อมูลรายรับรายจ่าย</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" id="btnAddRecord">
                    <i class="fa-solid fa-plus me-2"></i>
                    เพิ่มรายการใหม่
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="recordsTable" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>วันที่</th>
                            <!-- <th>เลขที่เอกสาร</th> -->
                            <!-- <th>คู่ค้า/ลูกค้า</th> -->
                            <th>ช่องทางชำระเงิน</th>
                            <th>ยอดเงิน</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="recordModal" tabindex="-1" aria-labelledby="recordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordModalLabel">เพิ่มรายการใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recordForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="recordUuid" name="uuid">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่-เวลา <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="datetime" name="datetime">
                                <small id="datetimeError" class="text-danger"></small>
                            </div>

                            <!-- <div class="mb-3">
                                <label class="form-label">เลขที่เอกสาร <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ref_no" name="ref_no" placeholder="เช่น INV-001">
                                <small id="refNoError" class="text-danger"></small>
                            </div> -->
<!-- 
                            <div class="mb-3">
                                <label class="form-label">คู่ค้า/ลูกค้า <span class="text-danger">*</span></label>
                                <select class="form-select" id="counterpartie_id" name="counterpartie_id">
                                    <option value="">เลือกคู่ค้า หรือสร้างใหม่</option>
                                </select>
                                <small id="counterpartieIdError" class="text-danger"></small>
                            </div> -->

                            <div id="newCounterpartieFields" style="display:none;">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">ข้อมูลคู่ค้าใหม่</h6>
                                        <div class="mb-3">
                                            <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                                            <select class="form-select" name="counterpartie_type">
                                                <option value="cash">เงินสด</option>
                                                <option value="invoice">ใบแจ้งหนี้</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="counterpartie_name" placeholder="ชื่อคู่ค้า/ลูกค้า">
                                            <small id="counterpartieNameError" class="text-danger"></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">เลขประจำตัวผู้เสียภาษี <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="counterpartie_tax_id" maxlength="13" placeholder="0000000000000">
                                            <small id="counterpartieTaxIdError" class="text-danger"></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="counterpartie_phone" maxlength="10" placeholder="0812345678">
                                            <small id="counterpartiePhoneError" class="text-danger"></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="counterpartie_email" placeholder="email@example.com">
                                            <small id="counterpartieEmailError" class="text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ช่องทางชำระเงิน <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_id" name="payment_id">
                                    <option value="">เลือกช่องทางชำระเงิน</option>
                                    <?php foreach ($payments as $payment): ?>
                                        <option value="<?= $payment['id'] ?>"><?= $payment['name_th'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="paymentIdError" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รายละเอียด</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="รายละเอียดเพิ่มเติม"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ไฟล์แนบ (รูปภาพ)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept="image/*">
                                <div id="attachmentPreview" class="mt-2"></div>
                                <div id="existingAttachments" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">รายการสินค้า/บริการ</h6>
                            <button type="button" class="btn btn-sm btn-success" id="btnAddItem">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่มรายการ
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="30%">ชื่อรายการ</th>
                                        <th width="20%">หมวดหมู่</th>
                                        <th width="10%">จำนวน</th>
                                        <th width="15%">ราคา/หน่วย</th>
                                        <th width="15%">รวม</th>
                                        <th width="10%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>ยอดรวมทั้งสิ้น:</strong></td>
                                        <td colspan="2"><strong id="totalAmount">0.00 บาท</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRecord">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRecordModalLabel">รายละเอียดรายการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewRecordContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>


<script>
    const BASE_URL = '<?= base_url() ?>';
    const categories = <?= json_encode($categories) ?>;

    let recordsDataTable;
    let currentEditUuid = null;
    let transactionItems = [];

    $(document).ready(function() {
        initDataTable();
        // loadCounterparties();
        initEventHandlers();
        initSelect2();

        // Set default datetime to now
        setDefaultDateTime();

    });

    function initSelect2() {
        // เริ่มต้น Select2 สำหรับ select ที่มีอยู่แล้ว
        $('.item-category').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'เลือกหมวดหมู่',
                    allowClear: true,
                    width: '100%',
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        return $('<span>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                });
            }
        });
    }

    function initDataTable() {
        recordsDataTable = $('#recordsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: BASE_URL + 'record/list',
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
                    data: 'datetime',
                    render: function(data) {
                        if (!data) return '-';
                        const date = new Date(data);
                        return date.toLocaleDateString('th-TH', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                },
                // {
                //     data: 'ref_no',
                //     render: function(data) {
                //         return data || '-';
                //     }
                // },
                // {
                //     data: 'counterpartie_name',
                //     render: function(data) {
                //         return data || '-';
                //     }
                // },
                {
                    data: 'payment_name',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'total',
                    render: function(data) {
                        return parseFloat(data || 0).toLocaleString('th-TH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' บาท';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-info btn-view" data-uuid="${row.uuid}" title="ดูรายละเอียด">
                                <i class="fa-solid fa-eye"></i>
                            </button>
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
                [1, 'desc']
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

    // function loadCounterparties() {
    //     $.ajax({
    //         url: BASE_URL + 'record/counterparties',
    //         type: 'GET',
    //         success: function(res) {
    //             if (res.status === 200) {
    //                 const select = $('#counterpartie_id');
    //                 select.empty();
    //                 select.append('<option value="">เลือกคู่ค้า</option>');
    //                 select.append('<option value="new">+ สร้างคู่ค้าใหม่</option>');

    //                 res.data.forEach(function(item) {
    //                     select.append(`<option value="${item.id}">${item.name}</option>`);
    //                 });
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error loading counterparties:', error);
    //         }
    //     });
    // }


    function initEventHandlers() {
        $('#btnAddRecord').on('click', function() {
            openRecordModal();
        });

        $(document).on('click', '.btn-view', function() {
            const uuid = $(this).data('uuid');
            viewRecord(uuid);
        });

        $(document).on('click', '.btn-edit', function() {
            const uuid = $(this).data('uuid');
            editRecord(uuid);
        });

        $(document).on('click', '.btn-delete', function() {
            const uuid = $(this).data('uuid');
            deleteRecord(uuid);
        });

        // $('#counterpartie_id').on('change', function() {
        //     if ($(this).val() === 'new') {
        //         $('#newCounterpartieFields').slideDown();
        //     } else {
        //         $('#newCounterpartieFields').slideUp();
        //     }
        // });

        $('#btnAddItem').on('click', function() {
            addTransactionItem();
        });

        $(document).on('click', '.btn-remove-item', function() {
            // ทำลาย Select2 ก่อนลบแถว
            $(this).closest('tr').find('.item-category').select2('destroy');
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $(document).on('input', '.item-quantity, .item-price', function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const total = quantity * price;
            row.find('.item-total').text(total.toFixed(2));
            calculateTotal();
        });

        $('#recordForm').on('submit', function(e) {
            e.preventDefault();
            saveRecord();
        });

        $('#attachments').on('change', function(e) {
            previewAttachments(e.target.files);
        });

        $(document).on('click', '.btn-delete-attachment', function() {
            const attachmentUuid = $(this).data('uuid');
            deleteAttachment(attachmentUuid);
        });
        
        // Clear errors when modal is hidden
        $('#recordModal').on('hidden.bs.modal', function() {
            clearAllErrors();
            // ทำลาย Select2 ทั้งหมดใน modal
            $('.item-category').select2('destroy');
        });
        
        // Real-time validation
        $('#datetime').on('change', function() {
            clearError('datetime');
            if (!$(this).val()) {
                showError('datetime', 'กรุณาเลือกวันที่-เวลา');
            } else {
                showSuccess('datetime');
            }
        });
        
        // $('#ref_no').on('input', function() {
        //     clearError('refNo');
        //     const value = $(this).val().trim();
        //     if (!value) {
        //         showError('refNo', 'กรุณากรอกเลขที่เอกสาร');
        //     } else if (value.length < 3) {
        //         showError('refNo', 'เลขที่เอกสารต้องมีอย่างน้อย 3 ตัวอักษร');
        //     } else if (value.length > 50) {
        //         showError('refNo', 'เลขที่เอกสารต้องไม่เกิน 50 ตัวอักษร');
        //     } else {
        //         showSuccess('refNo');
        //     }
        // });
        
        // $('#counterpartie_id').on('change', function() {
        //     clearError('counterpartieId');
        //     const value = $(this).val();
        //     if (!value) {
        //         showError('counterpartieId', 'กรุณาเลือกคู่ค้า/ลูกค้า');
        //     } else if (value === 'new') {
        //         $('#newCounterpartieFields').slideDown();
        //         showSuccess('counterpartieId');
        //     } else {
        //         $('#newCounterpartieFields').slideUp();
        //         showSuccess('counterpartieId');
        //     }
        // });
        
        $('#payment_id').on('change', function() {
            clearError('paymentId');
            if (!$(this).val()) {
                showError('paymentId', 'กรุณาเลือกช่องทางชำระเงิน');
            } else {
                showSuccess('paymentId');
            }
        });
        
        // New counterpartie validation
        $('input[name="counterpartie_name"]').on('input', function() {
            clearError('counterpartieName');
            const value = $(this).val().trim();
            if (!value) {
                showError('counterpartieName', 'กรุณากรอกชื่อคู่ค้า/ลูกค้า');
            } else if (value.length < 2) {
                showError('counterpartieName', 'ชื่อต้องมีอย่างน้อย 2 ตัวอักษร');
            } else if (value.length > 100) {
                showError('counterpartieName', 'ชื่อต้องไม่เกิน 100 ตัวอักษร');
            } else {
                showSuccess('counterpartieName');
            }
        });
        
        $('input[name="counterpartie_tax_id"]').on('input', function() {
            clearError('counterpartieTaxId');
            const value = $(this).val().trim();
            if (value) {
                if (!/^\d{13}$/.test(value.replace(/-/g, ''))) {
                    showError('counterpartieTaxId', 'เลขประจำตัวผู้เสียภาษีต้องเป็นตัวเลข 13 หลัก');
                } else {
                    showSuccess('counterpartieTaxId');
                }
            }
        });
        
        $('input[name="counterpartie_phone"]').on('input', function() {
            clearError('counterpartiePhone');
            const value = $(this).val().trim();
            if (value) {
                if (!/^[0-9]{9,10}$/.test(value.replace(/-/g, ''))) {
                    showError('counterpartiePhone', 'เบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็นตัวเลข 9-10 หลัก)');
                } else {
                    showSuccess('counterpartiePhone');
                }
            }
        });
        
        $('input[name="counterpartie_email"]').on('input', function() {
            clearError('counterpartieEmail');
            const value = $(this).val().trim();
            if (value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(value)) {
                    showError('counterpartieEmail', 'รูปแบบอีเมลไม่ถูกต้อง');
                } else {
                    showSuccess('counterpartieEmail');
                }
            }
        });
    }

    function setDefaultDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        $('#datetime').val(`${year}-${month}-${day}T${hours}:${minutes}`);
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
        const fields = ['datetime', 'paymentId', 'counterpartieName', 'counterpartieTaxId', 'counterpartiePhone', 'counterpartieEmail'];
        fields.forEach(field => {
            clearError(field);
        });
    }

    function openRecordModal() {
        currentEditUuid = null;
        $('#recordModalLabel').text('เพิ่มรายการใหม่');
        $('#recordForm')[0].reset();
        $('#recordUuid').val('');
        $('#newCounterpartieFields').hide();
        $('#itemsTableBody').empty();
        $('#attachmentPreview').empty();
        $('#existingAttachments').empty();
        transactionItems = [];
        
        // Clear all errors
        clearAllErrors();

        setDefaultDateTime();
        addTransactionItem(); // Add first item row

        const modal = new bootstrap.Modal(document.getElementById('recordModal'));
        modal.show();
        
        // เริ่มต้น Select2 หลังจาก modal แสดง
        setTimeout(() => {
            initSelect2();
        }, 300);
    }


    function addTransactionItem(item = null) {
        const index = $('#itemsTableBody tr').length;

        // สร้าง options แบบแยกกลุ่ม
        let categoryOptions = '<option value="">เลือกหมวดหมู่</option>';
        
        categories.forEach(parentCategory => {
            // เพิ่มหมวดหมู่หลัก
            categoryOptions += `<optgroup label="${parentCategory.name}">`;
            
            // เพิ่มหมวดหมู่ย่อย
            if (parentCategory.children && parentCategory.children.length > 0) {
                parentCategory.children.forEach(childCategory => {
                    const selected = item && item.category_id == childCategory.id ? 'selected' : '';
                    categoryOptions += `<option value="${childCategory.id}" ${selected}>${childCategory.name}</option>`;
                });
            }
            
            categoryOptions += '</optgroup>';
        });

        const row = `
        <tr>
            <td>
                <input type="text" class="form-control form-control-sm item-name" 
                       value="${item ? item.name : ''}" placeholder="ชื่อรายการ" >
            </td>
            <td>
                <select class="form-select form-select-sm item-category">
                    ${categoryOptions}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm item-quantity" 
                       value="${item ? item.quantity : 1}" min="1" step="1" >
            </td>
            <td>
                <input type="number" class="form-control form-control-sm item-price" 
                       value="${item ? item.price : 0}" min="0" step="0.01" >
            </td>
            <td class="item-total">${item ? (item.quantity * item.price).toFixed(2) : '0.00'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

        $('#itemsTableBody').append(row);
        
        // เริ่มต้น Select2 สำหรับ select ใหม่
        $('.item-category').last().select2({
            theme: 'bootstrap-5',
            placeholder: 'เลือกหมวดหมู่',
            allowClear: true,
            width: '100%',
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return $('<span>' + data.text + '</span>');
            },
            templateSelection: function(data) {
                return data.text;
            }
        });
        
        calculateTotal();
    }


    function calculateTotal() {
        let total = 0;
        $('#itemsTableBody tr').each(function() {
            const itemTotal = parseFloat($(this).find('.item-total').text()) || 0;
            total += itemTotal;
        });

        $('#totalAmount').text(total.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
    }


    function getItemsData() {
        const items = [];
        $('#itemsTableBody tr').each(function() {
            const row = $(this);
            items.push({
                name: row.find('.item-name').val(),
                category_id: row.find('.item-category').val() || null,
                quantity: parseFloat(row.find('.item-quantity').val()) || 1,
                price: parseFloat(row.find('.item-price').val()) || 0,
                note: ''
            });
        });
        return items;
    }


    function previewAttachments(files) {
        $('#attachmentPreview').empty();

        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = `
                    <div class="d-inline-block position-relative me-2 mb-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <span class="badge bg-secondary">${file.name}</span>
                    </div>
                `;
                    $('#attachmentPreview').append(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }


    function saveRecord() {
        if (!validateForm()) {
            return;
        }

        const formData = new FormData($('#recordForm')[0]);

        const items = getItemsData();
        if (items.length === 0) {
            // Swal.fire('แจ้งเตือน', 'กรุณาเพิ่มรายการสินค้า/บริการอย่างน้อย 1 รายการ', 'warning');
            $('#itemsTableBody').focus();
            $('#itemsTableBody').addClass('is-invalid');
            
            return;
        }

        formData.append('items', JSON.stringify(items));

        let total = 0;
        items.forEach(item => {
            total += item.quantity * item.price;
        });
        formData.append('total', total);

        const url = currentEditUuid ?
            BASE_URL + 'record/update/' + currentEditUuid :
            BASE_URL + 'record/create';

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
                        $('#recordModal').modal('hide');
                        recordsDataTable.ajax.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                if (xhr.responseJSON && xhr.responseJSON.status == 400) {
                    // Handle validation errors from server
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const fieldName = //field === 'ref_no' ? 'refNo' : 
                                            // field === 'counterpartie_id' ? 'counterpartieId' :
                                            field === 'payment_id' ? 'paymentId' :
                                            field === 'counterpartie_name' ? 'counterpartieName' :
                                            field === 'counterpartie_tax_id' ? 'counterpartieTaxId' :
                                            field === 'counterpartie_phone' ? 'counterpartiePhone' :
                                            field === 'counterpartie_email' ? 'counterpartieEmail' : field;
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


    function viewRecord(uuid) {
        $.ajax({
            url: BASE_URL + 'record/get/' + uuid,
            type: 'GET',
            success: function(res) {
                if (res.status === 200) {
                    const data = res.data;
                    let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>วันที่:</strong> ${formatDateTime(data.datetime)}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>ช่องทางชำระเงิน:</strong> ${data.payment_name || '-'}</p>
                            <p><strong>ยอดรวม:</strong> ${parseFloat(data.total).toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</p>
                            <p><strong>รายละเอียด:</strong> ${data.descripton || '-'}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>รายการสินค้า/บริการ</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ชื่อรายการ</th>
                                <th>หมวดหมู่</th>
                                <th>จำนวน</th>
                                <th>ราคา/หน่วย</th>
                                <th>รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                    data.items.forEach(item => {
                        html += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.category_name || '-'}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toLocaleString('th-TH', {minimumFractionDigits: 2})}</td>
                            <td>${(item.quantity * item.price).toLocaleString('th-TH', {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                    });

                    html += `
                        </tbody>
                    </table>
                `;

                    if (data.attachments && data.attachments.length > 0) {
                        html += '<hr><h6>ไฟล์แนบ</h6><div class="row">';
                        data.attachments.forEach(att => {
                            html += `
                            <div class="col-md-3 mb-2">
                                <img src="${BASE_URL}/record/image/${att.file_path}" class="img-thumbnail" style="width: 100%; cursor: pointer;" 
                                     onclick="window.open('${BASE_URL}/record/image/${att.file_path}', '_blank')">
                                <p class="small text-center mt-1">${att.file_name}</p>
                            </div>
                        `;
                        });
                        html += '</div>';
                    }

                    $('#viewRecordContent').html(html);
                    $('#viewRecordModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.status == 400) {
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

    function editRecord(uuid) {
        $.ajax({
            url: BASE_URL + 'record/get/' + uuid,
            type: 'GET',
            success: function(res) {
                if (res.status === 200) {
                    const data = res.data;
                    currentEditUuid = uuid;

                    $('#recordModalLabel').text('แก้ไขรายการ');
                    $('#recordUuid').val(uuid);

                    const datetime = new Date(data.datetime);
                    const year = datetime.getFullYear();
                    const month = String(datetime.getMonth() + 1).padStart(2, '0');
                    const day = String(datetime.getDate()).padStart(2, '0');
                    const hours = String(datetime.getHours()).padStart(2, '0');
                    const minutes = String(datetime.getMinutes()).padStart(2, '0');
                    $('#datetime').val(`${year}-${month}-${day}T${hours}:${minutes}`);

                    // $('#ref_no').val(data.ref_no || '');
                    // $('#counterpartie_id').val(data.counterpartie_id || '');
                    $('#payment_id').val(data.payment_id || '');
                    $('#description').val(data.descripton || '');

                    $('#itemsTableBody').empty();
                    data.items.forEach(item => {
                        addTransactionItem(item);
                    });

                    // Display existing attachments
                    displayExistingAttachments(data.attachments);
                    
                    // Clear all errors when editing
                    clearAllErrors();

                    $('#recordModal').modal('show');
                    
                    // เริ่มต้น Select2 หลังจาก modal แสดง
                    setTimeout(() => {
                        initSelect2();
                    }, 300);
                }
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.status == 400) {
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

    function deleteRecord(uuid) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบรายการนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'record/delete/' + uuid,
                    type: 'POST',
                    success: function(res) {
                        if (res.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                recordsDataTable.ajax.reload();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.status == 400) {
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

    function formatDateTime(datetime) {
        if (!datetime) return '-';
        const date = new Date(datetime);
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function deleteAttachment(attachmentUuid) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบไฟล์แนบนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'record/delete-attachment/' + attachmentUuid,
                    type: 'POST',
                    success: function(res) {
                        if (res.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Remove the attachment from the display
                                $(`.btn-delete-attachment[data-uuid="${attachmentUuid}"]`).closest('.attachment-item').remove();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.status == 400) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'แจ้งเตือน',
                                text: xhr.responseJSON.message,
                                confirmButtonText: 'ตกลง',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'ไม่สามารถลบไฟล์แนบได้'
                            });
                        }
                    }
                });
            }
        });
    }

    function displayExistingAttachments(attachments) {
        $('#existingAttachments').empty();
        
        if (attachments && attachments.length > 0) {
            let html = '<h6 class="mb-2">ไฟล์แนบที่มีอยู่:</h6><div class="row">';
            
            attachments.forEach(attachment => {
                html += `
                    <div class="col-md-3 mb-3 attachment-item">
                        <div class="position-relative">
                            <img src="${BASE_URL}record/image/${attachment.file_path}" 
                                 class="img-thumbnail" 
                                 style="width: 100%; height: 120px; object-fit: cover; cursor: pointer; border-radius: 8px;"
                                 onclick="window.open('${BASE_URL}record/image/${attachment.file_path}', '_blank')">
                            <button type="button" 
                                    class="btn btn-sm btn-delete-attachment position-absolute" 
                                    data-uuid="${attachment.uuid}"
                                    style="top: 5px; right: 5px; width: 28px; height: 28px; border-radius: 50%; background: rgba(220, 53, 69, 0.9); border: 2px solid white; color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: all 0.2s ease;"
                                    onmouseover="this.style.background='rgba(220, 53, 69, 1)'; this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.background='rgba(220, 53, 69, 0.9)'; this.style.transform='scale(1)'"
                                    title="ลบไฟล์">
                                <i class="fa-solid fa-times" style="font-size: 12px;"></i>
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted" style="font-size: 11px; word-break: break-all;">${attachment.file_name}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            $('#existingAttachments').html(html);
        }
    }

    function validateForm() {
        // Clear all previous errors
        clearAllErrors();
        
        let isValid = true;
        
        // Validate datetime ()
        const datetime = $('#datetime').val();
        if (!datetime) {
            showError('datetime', 'กรุณาเลือกวันที่-เวลา');
            isValid = false;
        }
        
        // Validate ref_no ()
        // const refNo = $('#ref_no').val().trim();
        // if (!refNo) {
        //     showError('refNo', 'กรุณากรอกเลขที่เอกสาร');
        //     isValid = false;
        // } else if (refNo.length < 3) {
        //     showError('refNo', 'เลขที่เอกสารต้องมีอย่างน้อย 3 ตัวอักษร');
        //     isValid = false;
        // } else if (refNo.length > 50) {
        //     showError('refNo', 'เลขที่เอกสารต้องไม่เกิน 50 ตัวอักษร');
        //     isValid = false;
        // }
        
        // Validate counterpartie_id ()
        // const counterpartieId = $('#counterpartie_id').val();
        // if (!counterpartieId) {
        //     showError('counterpartieId', 'กรุณาเลือกคู่ค้า/ลูกค้า');
        //     isValid = false;
        // }
        
        // Validate payment_id ()
        const paymentId = $('#payment_id').val();
        if (!paymentId) {
            showError('paymentId', 'กรุณาเลือกช่องทางชำระเงิน');
            isValid = false;
        }
        
        // Validate new counterpartie fields if creating new
        // if (counterpartieId === 'new') {
        //     const counterpartieName = $('input[name="counterpartie_name"]').val().trim();
        //     if (!counterpartieName) {
        //         showError('counterpartieName', 'กรุณากรอกชื่อคู่ค้า/ลูกค้า');
        //         isValid = false;
        //     } else if (counterpartieName.length < 2) {
        //         showError('counterpartieName', 'ชื่อต้องมีอย่างน้อย 2 ตัวอักษร');
        //         isValid = false;
        //     } else if (counterpartieName.length > 100) {
        //         showError('counterpartieName', 'ชื่อต้องไม่เกิน 100 ตัวอักษร');
        //         isValid = false;
        //     }

        //     const taxId = $('input[name="counterpartie_tax_id"]').val().trim();
        //     if (taxId) {
        //         if (!/^\d{13}$/.test(taxId.replace(/-/g, ''))) {
        //             showError('counterpartieTaxId', 'เลขประจำตัวผู้เสียภาษีต้องเป็นตัวเลข 13 หลัก');
        //             isValid = false;
        //         }
        //     }

        //     const phone = $('input[name="counterpartie_phone"]').val().trim();
        //     if (phone) {
        //         if (!/^[0-9]{9,10}$/.test(phone.replace(/-/g, ''))) {
        //             showError('counterpartiePhone', 'เบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็นตัวเลข 9-10 หลัก)');
        //             isValid = false;
        //         }
        //     }

        //     const email = $('input[name="counterpartie_email"]').val().trim();
        //     if (email) {
        //         const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        //         if (!emailPattern.test(email)) {
        //             showError('counterpartieEmail', 'รูปแบบอีเมลไม่ถูกต้อง');
        //             isValid = false;
        //         }
        //     }
        // }

        // Validate transaction items
        let hasItemError = false;
        $('#itemsTableBody tr').each(function() {
            const row = $(this);
            const name = row.find('.item-name').val().trim();
            const quantity = parseFloat(row.find('.item-quantity').val());
            const price = parseFloat(row.find('.item-price').val());

            if (!name) {
                // Swal.fire('แจ้งเตือน', 'กรุณากรอกชื่อรายการให้ครบทุกรายการ', 'warning');
                showError('itemName', 'กรุณากรอกชื่อรายการให้ครบทุกรายการ');
                row.find('.item-name').focus();
                hasItemError = true;
                return false;
            }

            if (isNaN(quantity) || quantity <= 0) {
                // Swal.fire('แจ้งเตือน', 'จำนวนต้องมากกว่า 0', 'warning');
                showError('itemQuantity', 'จำนวนต้องมากกว่า 0');
                row.find('.item-quantity').focus();
                hasItemError = true;
                return false;
            }

            if (isNaN(price) || price < 0) {
                // Swal.fire('แจ้งเตือน', 'ราคาต้องไม่ติดลบ', 'warning');
                showError('itemPrice', 'ราคาต้องไม่ติดลบ');
                row.find('.item-price').focus();
                hasItemError = true;
                return false;
            }
        });

        if (hasItemError) {
            isValid = false;
        }

        return isValid;
    }
</script>


<?= $this->endSection() ?>