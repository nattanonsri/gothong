<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>
<div class="main-content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="fs-3 fw-5"><i class="fas fa-layer-group me-2"></i><?= lang('app.category') ?></div>
    </div>

    <div class="d-flex justify-content-end align-items-center mb-4 gap-2">
        <button type="button" class="btn btn-gradient btn-sm px-4" onclick="addCategory()">
            <i class="fas fa-plus me-2"></i><?= lang('category.add_category') ?>
        </button>

        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle px-4" type="button" id="importExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-exchange-alt me-2"></i>นำเข้า/นำออก
            </button>
            <ul class="dropdown-menu" aria-labelledby="importExportDropdown">
                <li>
                    <a class="dropdown-item" role="button" onclick="document.getElementById('importCategoryFile').click()">
                        <i class="fas fa-file-import me-2 text-primary"></i> นำเข้าหมวดหมู่ Excel
                    </a>
                    <input type="file" id="importCategoryFile" style="display: none;" accept=".xlsx,.xls">
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('backend/exportCategory') ?>">
                        <i class="fas fa-file-export me-2 text-success"></i> นำออกหมวดหมู่ Excel
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('backend/downloadTemplateCategory') ?>">
                        <i class="fas fa-file-download me-2 text-info"></i> ดาวน์โหลดไฟล์ตัวอย่าง Excel
                    </a>
                </li>
            </ul>
        </div>
    </div>


    <!-- Stats Cards -->
    <div class="row g-2">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stats-icon bg-primary-subtle text-primary">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['total'] ?></div>
                            <div class="stats-label"><?= lang('category.total_categories') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stats-icon bg-info-subtle text-info">
                                <i class="fas fa-folder"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['root_categories'] ?></div>
                            <div class="stats-label"><?= lang('category.root_categories') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stats-icon bg-warning-subtle text-warning">
                                <i class="fas fa-folder-open"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['sub_categories'] ?></div>
                            <div class="stats-label"><?= lang('category.sub_categories') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stats-icon bg-success-subtle text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['active'] ?></div>
                            <div class="stats-label"><?= lang('category.active_categories') ?></div>
                            <?php if ($stats['inactive'] > 0): ?>
                                <small class="text-muted">(<?= lang('category.inactive_categories') ?> <?= $stats['inactive'] ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            <i class="fas fa-sitemap text-muted me-2"></i>
                            <h6 class="mb-0 fw-semibold"><?= lang('category.category_structure') ?></h6>
                        </div>

                        <div>
                            <button class="btn btn-gradient-outline btn-sm rounded-3" id="collapseExpandBtn" onclick="toggleCategoryTree()" title="ยุบ/ขยายทั้งหมด">
                                <i class="fas fa-compress-alt"></i>
                                <span class="ms-1">ยุบ</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-folder-plus text-muted"></i>
                            </div>
                            <h6 class="text-muted"><?= lang('category.no_categories_yet') ?></h6>
                            <p class="text-muted small mb-3"><?= lang('category.start_by_adding') ?></p>
                            <button class="btn btn-gradient-outline btn-sm rounded-3" onclick="addCategory()">
                                <i class="fas fa-plus me-1"></i><?= lang('category.add_first_category') ?>
                            </button>
                        </div>
                    <?php else: ?>
                        <div id="category-tree"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-lightbulb text-muted me-2"></i><?= lang('category.tips') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-mouse-pointer text-primary"></i>
                        </div>
                        <div class="help-text">
                            <?= lang('category.right_click_tip') ?>
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-plus text-success"></i>
                        </div>
                        <div class="help-text">
                            <?= lang('category.add_subcategory_tip') ?>
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-arrows-alt text-info"></i>
                        </div>
                        <div class="help-text">
                            <?= lang('category.drag_drop_tip') ?>
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-edit text-warning"></i>
                        </div>
                        <div class="help-text">
                            <?= lang('category.edit_tip') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="nodeId" name="node_id">
                    <input type="hidden" id="parentId" name="parent_id">

                    <div class="mb-3" id="parentInfo" style="display: none;">
                        <div class="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            หมวดหมู่หลัก: <strong id="parentName"></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categoryName" class="form-label"><?= lang('category.category_name') ?></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required
                            placeholder="<?= lang('category.enter_category_name') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryStatus" class="form-label"><?= lang('category.category_status') ?></label>
                                <select class="form-select" id="categoryStatus" name="status">
                                    <option value="active"><?= lang('category.active') ?></option>
                                    <option value="inactive"><?= lang('category.inactive') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorySort" class="form-label"><?= lang('category.sort_order') ?></label>
                                <input type="number" class="form-control" id="categorySort" name="sort" min="1" placeholder="<?= lang('category.sort_number') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('category.cancel') ?></button>
                    <button type="submit" class="btn btn-primary"><?= lang('category.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Brand Management Modal -->
<div class="modal fade" id="brandManagementModal" tabindex="-1" aria-labelledby="brandManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandManagementModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Available Brands -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-tags me-2"></i><?= lang('category.available_brands') ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="searchAvailableBrands"
                                        placeholder="<?= lang('category.search_brands') ?>">
                                </div>
                                <div class="brand-container" id="availableBrandsContainer">
                                    <!-- Brands will be loaded here -->
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-primary" onclick="addSelectedBrands()">
                                        <i class="fas fa-plus me-2"></i><?= lang('category.add_selected_brands') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brands in Category -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-tags me-2"></i><?= lang('category.brands_in_category') ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="searchCategoryBrands"
                                        placeholder="<?= lang('category.search_brands') ?>">
                                </div>
                                <div class="brand-container" id="categoryBrandsContainer">
                                    <!-- Brands will be loaded here -->
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-danger" onclick="removeSelectedBrands()">
                                        <i class="fas fa-trash me-2"></i><?= lang('category.remove_selected_brands') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div id="contextMenu" class="dropdown-menu border-0 shadow-lg" style="position: absolute; display: none; z-index: 1000;">
    <h6 class="dropdown-header text-muted">
        <i class="fas fa-cogs me-1"></i><?= lang('category.manage_categories') ?>
    </h6>
    <a class="dropdown-item" role="button" onclick="addSubCategory()">
        <i class="fas fa-plus text-success me-2"></i><?= lang('category.add_subcategory') ?>
    </a>
    <a class="dropdown-item" role="button" onclick="editCategory()">
        <i class="fas fa-edit text-warning me-2"></i><?= lang('category.edit') ?>
    </a>

    <div class="dropdown-divider"></div>
    <a class="dropdown-item text-danger" role="button" onclick="deleteCategory()">
        <i class="fas fa-trash me-2"></i><?= lang('category.delete') ?>
    </a>
</div>


<script>
    let categoryTree;
    let selectedNode = null;
    let treeData = <?= $tree_json ?>;
    let isTreeCollapsed = false;

    function initWhenReady() {
        if (treeData && treeData.length > 0) {
            initializeTree();
        }
        setupEventHandlers();
    }

    $(document).ready(function() {
        initWhenReady();
    });

    function initializeTree() {
        $("#category-tree").fancytree({
            extensions: ["dnd"],
            source: treeData,
            activate: function(event, data) {
                selectedNode = data.node;
            },
            renderNode: function(event, data) {
                // Add custom icons and styling
                const node = data.node;
                const $nodeSpan = $(node.span);

                // Remove existing status badge first
                $nodeSpan.find('.fancytree-title .status-badge').remove();

                // Add status badge
                const status = node.data.status == 'active' ? 'active' : 'inactive';
                const statusClass = status === 'active' ? 'text-success' : 'text-muted';
                const statusIcon = status === 'active' ? 'fa-check-circle' : 'fa-times-circle';
                $nodeSpan.find('.fancytree-title').append(
                    `<small class="ms-2 status-badge ${statusClass}"><i class="fas ${statusIcon}"></i></small>`
                );
            },
            dnd: {
                preventVoidMoves: true,
                preventRecursiveMoves: true,
                autoExpandMS: 400,
                dragStart: function(node, data) {
                    return true;
                },
                dragEnter: function(node, data) {
                    return true;
                },
                dragDrop: function(node, data) {
                    moveCategory(data.otherNode.key, node.key);
                }
            }
        });

        categoryTree = $("#category-tree").fancytree("getTree");

        // Context menu
        $("#category-tree").on("contextmenu", ".fancytree-node", function(e) {
            e.preventDefault();
            selectedNode = $.ui.fancytree.getNode(e.target);
            if (selectedNode) {
                showContextMenu(e.pageX, e.pageY);
            }
        });

        // Hide context menu on outside click
        $(document).on("click", function() {
            $("#contextMenu").hide();
        });
    }

    function setupEventHandlers() {
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            saveCategory();
        });
    }

    function showContextMenu(x, y) {
        $("#contextMenu").css({
            left: x + "px",
            top: y + "px",
            display: "block"
        });
    }

    function addCategory() {
        selectedNode = null;
        resetForm();
        $('#categoryModalLabel').html('<i class="fas fa-folder-plus text-primary me-2"></i>เพิ่มหมวดหมู่');
        $('#parentInfo').hide();
        $('#categoryModal').modal('show');
    }

    function addSubCategory() {
        if (!selectedNode) return;

        resetForm();
        $('#categoryModalLabel').html('<i class="fas fa-folder-plus text-primary me-2"></i>เพิ่มหมวดหมู่ย่อย');
        $('#parentId').val(selectedNode.key);
        $('#parentName').text(selectedNode.title);
        $('#parentInfo').show();
        $('#contextMenu').hide();
        $('#categoryModal').modal('show');
    }

    function editCategory() {
        if (!selectedNode) return;

        const data = selectedNode.data;
        resetForm();

        $('#categoryModalLabel').html('<i class="fas fa-edit text-warning me-2"></i>แก้ไขหมวดหมู่');
        $('#nodeId').val(data.node_id);
        $('#categoryName').val(data.name);
        $('#categoryStatus').val(data.status);
        $('#categorySort').val(data.sort);
        $('#parentId').val(data.parent_id || '');

        if (data.parent_id && data.parent_id != 0) {
            const parentNode = categoryTree.getNodeByKey(data.parent_id);
            if (parentNode) {
                $('#parentName').text(parentNode.title);
                $('#parentInfo').show();
            }
        } else {
            $('#parentInfo').hide();
        }

        $('#contextMenu').hide();
        $('#categoryModal').modal('show');
    }

    function deleteCategory() {
        if (!selectedNode) return;

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบหมวดหมู่ "${selectedNode.title}" ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i>ลบ',
            cancelButtonText: '<i class="fas fa-times me-1"></i>ยกเลิก',
            customClass: {
                popup: 'border-0 shadow-lg',
                confirmButton: 'btn-lg',
                cancelButton: 'btn-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}category/delete/${selectedNode.data.uuid}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            selectedNode.remove();
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                            }).then(() => {
                                refreshTree();
                            });
                        } else {
                            Swal.fire('ข้อผิดพลาด!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการลบหมวดหมู่', 'error');
                    }
                });
            }
        });

        $('#contextMenu').hide();
    }

    function saveCategory() {
        const formData = new FormData($('#categoryForm')[0]);
        const nodeId = $('#nodeId').val();
        const url = nodeId ? `${base_url}category/update/${nodeId}` : `${base_url}category/create`;

        // Show loading
        const submitBtn = $('#categoryForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    $('#categoryModal').modal('hide');
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        refreshTree();
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    }

    function moveCategory(nodeId, targetId) {
        $.ajax({
            url: `${base_url}category/move`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                nodeId: nodeId,
                targetId: targetId,
                position: 'child'
            }),
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: res.message,
                        icon: 'success'
                    }).then(function() {
                        refreshTree();
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: 'เกิดข้อผิดพลาดในการย้ายหมวดหมู่',
                    icon: 'error',
                });
            }
        });
    }

    function refreshTree() {
        $.ajax({
            url: `${base_url}category/tree-data`,
            type: 'GET',
            success: function(res) {
                if (res && Array.isArray(res)) {
                    treeData = res;

                    if (categoryTree) {
                        $("#category-tree").fancytree("destroy");
                    }

                    initializeTree();

                    console.log('Tree refreshed successfully');
                } else {
                    console.error('Failed to refresh tree: invalid response format');
                    location.reload();
                }
            },
            error: function() {
                console.error('Error refreshing tree');
                location.reload();
            }
        });
    }

    function toggleCategoryTree() {
        if (!categoryTree) return;

        const btn = $('#collapseExpandBtn');
        const icon = btn.find('i');
        const text = btn.find('span');

        // ปิดการใช้งานปุ่มชั่วคราว
        btn.prop('disabled', true);

        if (isTreeCollapsed) {
            // แสดงสถานะกำลังขยาย
            icon.removeClass('fa-expand-alt').addClass('fa-spinner fa-spin');
            text.text('กำลังขยาย...');
            btn.attr('title', 'กำลังขยายทั้งหมด');

            setTimeout(() => {
                categoryTree.visit(function(node) {
                    node.setExpanded(true);
                });

                // อัปเดตสถานะเมื่อเสร็จ
                icon.removeClass('fa-spinner fa-spin').addClass('fa-compress-alt');
                text.text('ยุบ');
                btn.attr('title', 'ยุบทั้งหมด');
                btn.prop('disabled', false);
                isTreeCollapsed = false;
            }, 100);
        } else {
            // แสดงสถานะกำลังยุบ
            icon.removeClass('fa-compress-alt').addClass('fa-spinner fa-spin');
            text.text('กำลังยุบ...');
            btn.attr('title', 'กำลังยุบทั้งหมด');

            setTimeout(() => {
                categoryTree.visit(function(node) {
                    node.setExpanded(false);
                });

                // อัปเดตสถานะเมื่อเสร็จ
                icon.removeClass('fa-spinner fa-spin').addClass('fa-expand-alt');
                text.text('ขยาย');
                btn.attr('title', 'ขยายทั้งหมด');
                btn.prop('disabled', false);
                isTreeCollapsed = true;
            }, 100);
        }
    }

    function resetForm() {
        $('#categoryForm')[0].reset();
        $('#nodeId').val('');
        $('#parentId').val('');
        $('#categoryStatus').val('active');
    }

    // Brand Management Functions
    let currentCategoryId = null;
    let availableBrandsData = [];
    let categoryBrandsData = [];

    function loadBrandData() {
        if (!currentCategoryId) return;

        $.ajax({
            url: `${base_url}category/${currentCategoryId}/brands`,
            type: 'GET',
            success: function(res) {
                if (res.status === 200) {
                    availableBrandsData = res.data.available_brands;
                    categoryBrandsData = res.data.brands_in_category;
                    renderAvailableBrands();
                    renderCategoryBrands();
                }
            },
            error: function(xhr, status, error) {
                $('#brandManagementModal').modal('hide');
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: xhr.responseJSON.message,
                    icon: 'error',
                }).then(() => {
                    $('#brandManagementModal').modal('show');
                });
            }
        });
    }

    function renderAvailableBrands() {
        const container = $('#availableBrandsContainer');
        const searchTerm = $('#searchAvailableBrands').val().toLowerCase();

        let html = '';
        const filteredBrands = availableBrandsData.filter(brand =>
            brand.name.toLowerCase().includes(searchTerm) ||
            brand.slug.toLowerCase().includes(searchTerm)
        );

        if (filteredBrands.length === 0) {
            html = '<div class="text-center text-muted py-3">ไม่มีแบรนด์ที่สามารถเพิ่มได้</div>';
        } else {
            filteredBrands.forEach(brand => {
                html += `
                <div class="brand-item border rounded p-2 mb-2 bg-white" data-brand-id="${brand.id}">
                    <div class="form-check">
                        <input class="form-check-input available-brand-checkbox" type="checkbox" 
                               value="${brand.id}" id="available_${brand.id}">
                        <label class="form-check-label w-100" for="available_${brand.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${brand.name}</strong>
                                    <div class="text-muted small">สถานะ: ${brand.status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน'}</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
            });
        }

        container.html(html);
    }

    function renderCategoryBrands() {
        const container = $('#categoryBrandsContainer');
        const searchTerm = $('#searchCategoryBrands').val().toLowerCase();

        let html = '';
        const filteredBrands = categoryBrandsData.filter(brand =>
            brand.name.toLowerCase().includes(searchTerm) ||
            brand.slug.toLowerCase().includes(searchTerm)
        );

        if (filteredBrands.length === 0) {
            html = '<div class="text-center text-muted py-3">ยังไม่มีแบรนด์ในหมวดหมู่นี้</div>';
        } else {
            filteredBrands.forEach(brand => {
                html += `
                <div class="brand-item border rounded p-2 mb-2 bg-white" data-brand-id="${brand.id}">
                    <div class="form-check">
                        <input class="form-check-input category-brand-checkbox" type="checkbox" 
                               value="${brand.id}" id="category_${brand.id}">
                        <label class="form-check-label w-100" for="category_${brand.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${brand.name}</strong>
                                    <div class="text-muted small">สถานะ: ${brand.status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน'}</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
            });
        }

        container.html(html);
    }

    function addSelectedBrands() {
        $('#brandManagementModal').modal('hide');
        const selectedBrandIds = [];
        $('.available-brand-checkbox:checked').each(function() {
            selectedBrandIds.push($(this).val());
        });

        if (selectedBrandIds.length === 0) {
            Swal.fire({
                title: 'แจ้งเตือน',
                text: 'กรุณาเลือกแบรนด์ที่ต้องการเพิ่ม',
                icon: 'warning'
            }).then(() => {
                $('#brandManagementModal').modal('show');
            });
            return;
        }

        $.ajax({
            url: `${base_url}category/add-brands`,
            type: 'POST',
            data: {
                node_id: currentCategoryId,
                brand_ids: selectedBrandIds
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        loadBrandData();
                        $('#brandManagementModal').modal('show');
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: xhr.responseJSON.message,
                    icon: 'error',
                }).then(() => {
                    $('#brandManagementModal').modal('show');
                });
            }
        });
    }

    function removeSelectedBrands() {
        $('#brandManagementModal').modal('hide');
        const selectedBrandIds = [];
        $('.category-brand-checkbox:checked').each(function() {
            selectedBrandIds.push($(this).val());
        });

        if (selectedBrandIds.length === 0) {
            Swal.fire({
                title: 'แจ้งเตือน',
                text: 'กรุณาเลือกแบรนด์ที่ต้องการลบ',
                icon: 'warning'
            }).then(() => {
                $('#brandManagementModal').modal('show');
            });
            return;
        }


        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบแบรนด์ ${selectedBrandIds.length} แบรนด์ออกจากหมวดหมู่นี้ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}category/remove-brands`,
                    type: 'POST',
                    data: {
                        node_id: currentCategoryId,
                        brand_ids: selectedBrandIds
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                loadBrandData();
                                $('#brandManagementModal').modal('show');
                            });
                        } else {
                            Swal.fire('ข้อผิดพลาด!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการลบแบรนด์', 'error');
                    }
                });
            }
        });
    }

    $(document).on('input', '#searchAvailableBrands', function() {
        renderAvailableBrands();
    });

    $(document).on('input', '#searchCategoryBrands', function() {
        renderCategoryBrands();
    });

    $('#importCategoryFile').on('change', function() {
        const file = this.files[0];

        if (!file) {
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'กำลังนำเข้าข้อมูล...',
            text: 'กรุณารอสักครู่',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `${base_url}backend/importCategory`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                Swal.close();
                if (res.status == 'success') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: res.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showErrorTable(xhr.responseJSON);
                } else if (xhr.responseJSON && xhr.responseJSON.failed_details) {
                    showInsertErrorTable(xhr.responseJSON);
                } else {
                    Swal.fire({
                        title: 'ข้อผิดพลาด!',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล',
                        icon: 'warning',
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });

        $(this).val('');
    });

    function showErrorTable(response) {
        let errorTableHtml = `
            <div class="error-summary mb-3">
                <h5 class="text-danger">พบข้อผิดพลาดในไฟล์ Excel</h5>
                <p class="mb-2">
                    <strong>จำนวนแถวที่ผิด:</strong> ${response.total_errors} แถว<br>
                    <strong>จำนวนแถวทั้งหมด:</strong> ${response.total_rows} แถว
                </p>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm">
                    <thead class="table-danger">
                        <tr>
                            <th>แถวที่</th>
                            <th>ID</th>
                            <th>Parent ID</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>ระดับ</th>
                            <th>ลำดับ</th>
                            <th>สถานะ</th>
                            <th>ข้อผิดพลาด</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        response.errors.forEach(error => {
            const errorList = error.errors.map(err => `<span class="badge bg-danger me-1 mb-1">${err}</span>`).join('');
            errorTableHtml += `
                <tr>
                    <td class="text-center"><strong>${error.row}</strong></td>
                    <td>${error.data.id}</td>
                    <td>${error.data.parent_id}</td>
                    <td>${error.data.name}</td>
                    <td>${error.data.level}</td>
                    <td>${error.data.sort}</td>
                    <td>${error.data.status}</td>
                    <td class="error-cell">${errorList}</td>
                </tr>
            `;
        });

        errorTableHtml += `
                    </tbody>
                </table>
            </div>

        `;

        Swal.fire({
            title: 'พบข้อผิดพลาดในไฟล์ Excel',
            html: errorTableHtml,
            icon: 'error',
            width: '90%',
            confirmButtonText: 'ปิด',
            confirmButtonColor: '#d33',
            customClass: {
                container: 'error-table-modal'
            }
        });
    }

    function showInsertErrorTable(response) {
        let errorTableHtml = `
            <div class="error-summary mb-3">
                <h5 class="text-danger">พบข้อผิดพลาดในการบันทึกข้อมูล</h5>
                <p class="mb-2">
                    <strong>บันทึกสำเร็จ:</strong> ${response.success_count} รายการ<br>
                    <strong>บันทึกไม่สำเร็จ:</strong> ${response.failed_count} รายการ<br>
                    <strong>รายการทั้งหมด:</strong> ${response.total_rows} รายการ
                </p>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm">
                    <thead class="table-danger">
                        <tr>
                            <th width="10%">ลำดับ</th>
                            <th>ข้อผิดพลาด</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        response.failed_details.forEach((error, index) => {
            errorTableHtml += `
                <tr>
                    <td class="text-center"><strong>${index + 1}</strong></td>
                    <td>${error}</td>
                </tr>
            `;
        });

        errorTableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        Swal.fire({
            title: 'พบข้อผิดพลาดในการบันทึกข้อมูล',
            html: errorTableHtml,
            icon: 'warning',
            width: '70%',
            confirmButtonText: 'ปิด',
            confirmButtonColor: '#d33',
            showDenyButton: true,
            denyButtonText: 'รีเฟรชหน้า',
            denyButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isDenied) {
                refreshTree();
            }
        });
    }
</script>

<style>
    .stats-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.2rem;
    }

    .stats-number {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        color: #1a202c;
    }

    .stats-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
    }

    .empty-state-icon {
        font-size: 3rem;
        opacity: 0.5;
    }

    .help-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .help-item:last-child {
        margin-bottom: 0;
    }

    .help-icon {
        width: 24px;
        flex-shrink: 0;
        margin-right: 0.75rem;
        margin-top: 0.125rem;
    }

    .help-text {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .fancytree-container {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
    }

    .fancytree-node {
        padding: 0.5rem 0 !important;
        border-radius: 6px;
        margin: 2px 0;
        transition: all 0.15s ease;
    }

    .fancytree-node:hover {
        background-color: #f8fafc !important;
    }

    .fancytree-title {
        font-size: 0.925rem;
        color: #2d3748;
        font-weight: 500;
    }

    #contextMenu {
        border-radius: 8px !important;
        padding: 0.5rem 0;
        min-width: 180px;
    }

    .dropdown-header {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.15s ease;
    }

    .dropdown-item:hover {
        background-color: #f8fafc;
        transform: translateX(2px);
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        padding: 1.5rem 1.5rem 1rem;
    }

    .modal-body {
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-radius: 0 0 12px 12px;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0066cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.15);
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .stats-number {
            font-size: 1.5rem;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .help-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .help-icon {
            margin-bottom: 0.25rem;
            margin-right: 0;
        }
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .brand-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem;
    }

    .brand-item {
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .brand-item:hover {
        background-color: #f8fafc !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .brand-item .form-check {
        margin: 0;
    }

    .brand-item .form-check-label {
        cursor: pointer;
        padding: 0.25rem;
    }

    .brand-item:has(.form-check-input:checked) {
        background-color: #e6f3ff !important;
        border-color: #0066cc !important;
    }

    .brand-container::-webkit-scrollbar {
        width: 6px;
    }

    .brand-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .brand-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .brand-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .modal-xl .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }

    .brand-item img {
        border-radius: 4px;
    }

    .error-table-modal .swal2-popup {
        padding: 1.5rem;
    }

    .error-summary {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        border-left: 4px solid #dc3545;
    }

    .error-cell {
        max-width: 300px;
        word-wrap: break-word;
    }

    .error-cell .badge {
        font-size: 0.75rem;
        display: inline-block;
        max-width: 100%;
        white-space: normal;
        word-wrap: break-word;
    }

    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    .table-danger th {
        background-color: #f5c2c7 !important;
        border-color: #f1aeb5 !important;
        color: #842029 !important;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .table-sm td,
    .table-sm th {
        padding: 0.5rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table tbody tr:nth-of-type(odd) {
        background-color: rgba(248, 249, 250, 0.5);
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.075);
    }

    #collapseExpandBtn {
        transition: all 0.2s ease;
        min-width: 80px;
    }

    #collapseExpandBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.2);
    }

    #collapseExpandBtn i {
        transition: transform 0.2s ease;
    }

    #collapseExpandBtn:hover i {
        transform: scale(1.1);
    }
</style>


<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>