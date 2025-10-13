<div class="container-fluid">
    <form id="frmEditCategory">
        <?= csrf_field() ?>
        <input type="hidden" id="edit-uuid" name="uuid" value="<?= $uuid ?>">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-folder me-2"></i>หมวดหมู่หลัก
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-lg" id="edit-main" name="main" value="<?= $category['name'] ?>" required>
                            <label for="edit-main">
                                <i class="fas fa-tag me-1"></i>ชื่อหมวดหมู่หลัก
                            </label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status" value="active" <?= ($category['status'] == 'active') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="edit-status">ใช้งาน</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-folder-open me-2"></i>หมวดหมู่ย่อย
                    <span class="badge bg-light text-dark ms-2" id="subCategoryCount">0</span>
                </h6>
                <button type="button" class="btn btn-sm btn-light" onclick="addSubCategory()" data-bs-toggle="tooltip" title="เพิ่มหมวดหมู่ย่อย">
                    <i class="fas fa-plus me-1"></i>เพิ่มหมวดหมู่ย่อย
                </button>
            </div>
            <div class="card-body">
                <div id="subCategoriesContainer">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>ยกเลิก
            </button>
            <button type="submit" class="btn btn-primary" id="btnEditCategory">
                <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>
<script>
    (function() {
        'use strict';

        window.EditCategory = window.EditCategory || {};

        const EditCategoryConfig = {
            subCategoryCounter: 0,
            brandsData: <?= json_encode($brands ?? []) ?>,
            selectedBrandsData: <?= json_encode($selectedBrands ?? []) ?>,
            categoryData: <?= json_encode($category ?? []) ?>,
            subCategoriesData: <?= json_encode($subCate ?? []) ?>
        };

        $(document).ready(function() {
            initializeEditCategoryModal();
        });

        function initializeEditCategoryModal() {
            EditCategoryConfig.subCategoryCounter = 0;

            $('#subCategoriesContainer').empty();

            $('#frmEditCategory').off('submit.editCategory');

            loadExistingSubCategories();

            updateSubCategoryCount();

            $('#frmEditCategory').on('submit.editCategory', handleFormSubmit);

            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        function loadExistingSubCategories() {
            if (EditCategoryConfig.subCategoriesData && EditCategoryConfig.subCategoriesData.length > 0) {
                EditCategoryConfig.subCategoriesData.forEach(function(subCategory) {
                    addSubCategory(subCategory);
                });
            }
        }

        function addSubCategory(existingData = null) {
            try {
                const currentIndex = EditCategoryConfig.subCategoryCounter;
                const subCategoryHtml = createSubCategoryHtml(currentIndex, existingData);

                $('#subCategoriesContainer').append(subCategoryHtml);
                initializeBrandSelect(currentIndex, existingData);

                EditCategoryConfig.subCategoryCounter++;
                updateSubCategoryCount();

                $('[data-bs-toggle="tooltip"]').tooltip();

            } catch (error) {
                console.error('Error adding sub category:', error);
                showNotification('error', 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่ย่อย');
            }
        }

        function createSubCategoryHtml(index, existingData = null) {
            const data = {
                name: existingData?.name || '',
                id: existingData?.id || '',
                uuid: existingData?.uuid || '',
                status: existingData?.status || 'active',
                parentId: existingData?.parent_id || EditCategoryConfig.categoryData?.id || '',
                sortOrder: existingData?.sort_order || 0
            };

            return `
        <div class="sub-category-item border rounded mb-3 p-3 bg-light" data-index="${index}" data-uuid="${data.uuid}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="text-primary mb-0 fw-bold">
                    <i class="fas fa-folder-open me-2"></i>หมวดหมู่ย่อย #${index + 1}
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-success btn-sm" 
                            onclick="EditCategory.addNestedSubCategory(${index})" 
                            data-bs-toggle="tooltip" 
                            title="เพิ่มหมวดหมู่ย่อยใน">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            onclick="EditCategory.removeSubCategory(${index})" 
                            data-bs-toggle="tooltip" 
                            title="ลบหมวดหมู่ย่อย">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="sub_categories[${index}][id]" value="${data.id}">
            <input type="hidden" name="sub_categories[${index}][uuid]" value="${data.uuid}">
            <input type="hidden" name="sub_categories[${index}][parent_id]" value="${data.parentId}">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="sub_name_${index}" 
                               name="sub_categories[${index}][name]" 
                               value="${data.name}" 
                               placeholder="ชื่อหมวดหมู่ย่อย"
                               required>
                        <label for="sub_name_${index}">
                            <i class="fas fa-tag me-1"></i>ชื่อหมวดหมู่ย่อย
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" 
                               class="form-control" 
                               id="sub_sort_${index}" 
                               name="sub_categories[${index}][sort_order]" 
                               value="${data.sortOrder}" 
                               min="0"
                               placeholder="ลำดับ">
                        <label for="sub_sort_${index}">
                            <i class="fas fa-sort-numeric-up me-1"></i>ลำดับ
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center h-100">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="sub_status_${index}" 
                                   name="sub_categories[${index}][status]" 
                                   value="active" 
                                   ${data.status === 'active' ? 'checked' : ''}>
                            <label class="form-check-label fw-medium" for="sub_status_${index}">
                                ใช้งาน
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-tags me-1 text-info"></i>แบรนด์ที่เกี่ยวข้อง
                </label>
                <select class="form-select brand-select" 
                        id="brands_${index}" 
                        name="sub_categories[${index}][brands][]" 
                        multiple>
                    ${createBrandOptions()}
                </select>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>เลือกแบรนด์ที่เกี่ยวข้องกับหมวดหมู่นี้ (สามารถเลือกหลายแบรนด์)
                </div>
            </div>
            
            <div class="nested-sub-categories mt-3" id="nested_${index}">
            </div>
        </div>
        `;
        }

        function createBrandOptions() {
            let options = '<option value="">-- เลือกแบรนด์ --</option>';
            EditCategoryConfig.brandsData.forEach(function(brand) {
                options += `<option value="${brand.uuid}">${brand.name}</option>`;
            });
            return options;
        }

        function initializeBrandSelect(index, existingData = null) {
            const selectId = `#brands_${index}`;

            if ($(selectId).hasClass('select2-hidden-accessible')) {
                $(selectId).select2('destroy');
            }

            $(selectId).select2({
                placeholder: 'เลือกแบรนด์...',
                allowClear: true,
                dropdownParent: $('#editCategoryModal'),
                width: '100%',
                theme: 'bootstrap-5'
            });

            if (existingData?.selected_brands?.length > 0) {
                const selectedBrandUuids = existingData.selected_brands.map(brand => brand.uuid);
                $(selectId).val(selectedBrandUuids).trigger('change');
            }
        }

        function addNestedSubCategory(parentIndex) {
            try {
                const nestedIndex = EditCategoryConfig.subCategoryCounter;
                const nestedHtml = createNestedSubCategoryHtml(nestedIndex, parentIndex);

                $(`#nested_${parentIndex}`).append(nestedHtml);
                initializeBrandSelect(nestedIndex);

                EditCategoryConfig.subCategoryCounter++;
                updateSubCategoryCount();

                $('[data-bs-toggle="tooltip"]').tooltip();

            } catch (error) {
                console.error('Error adding nested sub category:', error);
                showNotification('error', 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่ย่อยใน');
            }
        }

        function createNestedSubCategoryHtml(index, parentIndex) {
            return `
        <div class="nested-sub-category-item border rounded mb-2 p-3 ms-4 bg-white shadow-sm" data-index="${index}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="text-success mb-0 fw-bold small">
                    <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i>
                    <i class="fas fa-folder-open me-1"></i>หมวดหมู่ย่อยใน #${index + 1}
                </h6>
                <button type="button" 
                        class="btn btn-outline-danger btn-sm" 
                        onclick="EditCategory.removeNestedSubCategory(${index})" 
                        data-bs-toggle="tooltip" 
                        title="ลบหมวดหมู่ย่อยใน">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input type="hidden" name="sub_categories[${index}][parent_id]" value="parent_${parentIndex}">
            <input type="hidden" name="sub_categories[${index}][level]" value="2">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="nested_name_${index}" 
                               name="sub_categories[${index}][name]" 
                               placeholder="ชื่อหมวดหมู่ย่อยใน"
                               required>
                        <label for="nested_name_${index}">
                            <i class="fas fa-tag me-1"></i>ชื่อหมวดหมู่ย่อยใน
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" 
                               class="form-control form-control-sm" 
                               id="nested_sort_${index}" 
                               name="sub_categories[${index}][sort_order]" 
                               value="0" 
                               min="0"
                               placeholder="ลำดับ">
                        <label for="nested_sort_${index}">
                            <i class="fas fa-sort-numeric-up me-1"></i>ลำดับ
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center h-100">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="nested_status_${index}" 
                                   name="sub_categories[${index}][status]" 
                                   value="active" 
                                   checked>
                            <label class="form-check-label fw-medium small" for="nested_status_${index}">
                                ใช้งาน
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label fw-bold small mb-2">
                    <i class="fas fa-tags me-1 text-info"></i>แบรนด์ที่เกี่ยวข้อง
                </label>
                <select class="form-select form-select-sm brand-select" 
                        id="brands_${index}" 
                        name="sub_categories[${index}][brands][]" 
                        multiple>
                    ${createBrandOptions()}
                </select>
            </div>
        </div>
        `;
        }

        function removeSubCategory(index) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบหมวดหมู่ย่อยนี้และหมวดหมู่ย่อยในทั้งหมดหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i>ลบ',
                cancelButtonText: '<i class="fas fa-times me-1"></i>ยกเลิก',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $item = $(`.sub-category-item[data-index="${index}"]`);

                    $item.find('.select2-hidden-accessible').each(function() {
                        $(this).select2('destroy');
                    });

                    $item.fadeOut(300, function() {
                        $(this).remove();
                        updateSubCategoryCount();
                    });

                    showNotification('success', 'ลบหมวดหมู่ย่อยเรียบร้อยแล้ว');
                }
            });
        }

        function removeNestedSubCategory(index) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบหมวดหมู่ย่อยในนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i>ลบ',
                cancelButtonText: '<i class="fas fa-times me-1"></i>ยกเลิก',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $item = $(`.nested-sub-category-item[data-index="${index}"]`);

                    $item.find('.select2-hidden-accessible').each(function() {
                        $(this).select2('destroy');
                    });

                    $item.fadeOut(300, function() {
                        $(this).remove();
                        updateSubCategoryCount();
                    });

                    showNotification('success', 'ลบหมวดหมู่ย่อยในเรียบร้อยแล้ว');
                }
            });
        }

        function updateSubCategoryCount() {
            const mainCount = $('.sub-category-item').length;
            const nestedCount = $('.nested-sub-category-item').length;
            const totalCount = mainCount + nestedCount;

            // $('#subCategoryCount').text(`${totalCount}`);
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            if (!validateForm()) {
                return false;
            }

            const formData = new FormData($('#frmEditCategory')[0]);

            setLoadingState(true);

            $.ajax({
                url: `${base_url}backend/editCategory`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    '<?= csrf_header() ?>': $('input[name="<?= csrf_token() ?>"]').val()
                },
                success: function(response) {
                    setLoadingState(false);

                    if (response.status === 200) {
                        $('#editCategoryModal').modal('hide');
                        showNotification('success', response.message || 'บันทึกข้อมูลเรียบร้อยแล้ว');

                        if (typeof window.refreshData === 'function') {
                            window.refreshData();
                        }
                    } else {
                        showNotification('error', response.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    }
                },
                error: function(xhr, status, error) {
                    setLoadingState(false);

                    console.error('Form submission error:', {
                        xhr,
                        status,
                        error
                    });

                    const response = xhr.responseJSON;
                    const errorMessage = response?.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';

                    showNotification('error', errorMessage);
                }
            });
        }

        function validateForm() {
            let isValid = true;

            $('.is-invalid').removeClass('is-invalid');

            const mainName = $('#edit-main').val().trim();
            if (!mainName) {
                $('#edit-main').addClass('is-invalid');
                isValid = false;
            }

            $('.sub-category-item').each(function() {
                const index = $(this).data('index');
                const subName = $(`#sub_name_${index}`).val().trim();

                if (!subName) {
                    $(`#sub_name_${index}`).addClass('is-invalid');
                    isValid = false;
                }
            });

            $('.nested-sub-category-item').each(function() {
                const index = $(this).data('index');
                const nestedName = $(`#nested_name_${index}`).val().trim();

                if (!nestedName) {
                    $(`#nested_name_${index}`).addClass('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                showNotification('warning', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            }

            return isValid;
        }

        function setLoadingState(loading) {
            const $btn = $('#btnEditCategory');

            if (loading) {
                $btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...');
            } else {
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-save me-1"></i>บันทึกการแก้ไข');
            }
        }

        function showNotification(type, message) {
            const icons = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };

            Swal.fire({
                icon: icons[type] || 'info',
                title: type === 'success' ? 'สำเร็จ' : type === 'error' ? 'เกิดข้อผิดพลาด' : type === 'warning' ? 'คำเตือน' : 'ข้อมูล',
                text: message,
                confirmButtonColor: colors[type] || '#007bff',
                timer: type === 'success' ? 2000 : undefined,
                showConfirmButton: type !== 'success'
            });
        }

        function cleanup() {
            $('.select2-hidden-accessible').each(function() {
                try {
                    $(this).select2('destroy');
                } catch (e) {
                    console.warn('Error destroying select2:', e);
                }
            });

            $('.select2-container').remove();

            const form = document.getElementById('frmEditCategory');
            if (form) {
                form.reset();
            }
            $('#subCategoriesContainer').empty();
            $('.is-invalid').removeClass('is-invalid');
            EditCategoryConfig.subCategoryCounter = 0;
            $('#frmEditCategory').off('.editCategory');
        }

        $('#editCategoryModal').on('hidden.bs.modal', cleanup);

        $(document).on('input.editCategory change.editCategory', '.form-control, .form-select', function() {
            $(this).removeClass('is-invalid');
        });

        window.EditCategory = {
            addSubCategory: addSubCategory,
            addNestedSubCategory: addNestedSubCategory,
            removeSubCategory: removeSubCategory,
            removeNestedSubCategory: removeNestedSubCategory,
            cleanup: cleanup
        };

    })();
</script>

<style>
    .sub-category-item {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .sub-category-item:hover {
        border-color: #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.25);
    }

    .nested-sub-category-item {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-left: 4px solid #28a745;
    }

    .nested-sub-category-item:hover {
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.25);
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-control:not(:placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }

    .form-floating>label {
        opacity: 0.65;
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        opacity: 1;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .card-header {
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.765625rem;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--multiple:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    @media (max-width: 768px) {
        .nested-sub-category-item {
            margin-left: 1rem !important;
        }

        .btn-group-sm>.btn {
            padding: 0.125rem 0.25rem;
            font-size: 0.6875rem;
        }
    }
</style>