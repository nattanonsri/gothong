<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>
<div class="main-content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="fs-3 fw-5"><i class="fa-solid fa-building me-2"></i>จัดการองค์กร</div>
    </div>

    <div class="d-flex justify-content-end align-items-center mb-4 gap-2">
        <button type="button" class="btn btn-gradient btn-sm px-4" onclick="addOrganization()">
            <i class="fas fa-plus me-2"></i>เพิ่มองค์กร
        </button>

        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle px-4" type="button" id="importExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-exchange-alt me-2"></i>นำเข้า/นำออก
            </button>
            <ul class="dropdown-menu" aria-labelledby="importExportDropdown">
                <li>
                    <a class="dropdown-item" role="button" onclick="document.getElementById('importOrganizationFile').click()">
                        <i class="fas fa-file-import me-2 text-primary"></i> นำเข้าองค์กร Excel
                    </a>
                    <input type="file" id="importOrganizationFile" style="display: none;" accept=".xlsx,.xls">
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('backend/exportOrganization') ?>">
                        <i class="fas fa-file-export me-2 text-success"></i> นำออกองค์กร Excel
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('backend/downloadTemplateOrganization') ?>">
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
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['total'] ?></div>
                            <div class="stats-label">จำนวนองค์กรทั้งหมด</div>
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
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['root_organizations'] ?></div>
                            <div class="stats-label">องค์กรหลัก</div>
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
                                <i class="fas fa-sitemap"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stats-number"><?= $stats['sub_organizations'] ?></div>
                            <div class="stats-label">องค์กรย่อย</div>
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
                            <div class="stats-label">องค์กรที่ใช้งาน</div>
                            <?php if ($stats['inactive'] > 0): ?>
                                <small class="text-muted">(ไม่ใช้งาน <?= $stats['inactive'] ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-2 mt-2">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            <i class="fas fa-sitemap text-muted me-2"></i>
                            <h6 class="mb-0 fw-semibold">โครงสร้างองค์กร</h6>
                        </div>

                        <div>
                            <button class="btn btn-gradient-outline btn-sm rounded-3" id="collapseExpandBtn" onclick="toggleOrganizationTree()" title="ยุบ/ขยายทั้งหมด">
                                <i class="fas fa-compress-alt"></i>
                                <span class="ms-1">ยุบ</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($organizations)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-building text-muted"></i>
                            </div>
                            <h6 class="text-muted">ยังไม่มีองค์กร</h6>
                            <p class="text-muted small mb-3">เริ่มต้นโดยการเพิ่มองค์กรใหม่</p>
                            <button class="btn btn-gradient-outline btn-sm rounded-3" onclick="addOrganization()">
                                <i class="fas fa-plus me-1"></i>เพิ่มองค์กรแรก
                            </button>
                        </div>
                    <?php else: ?>
                        <div id="organization-tree"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help & Tips -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-lightbulb text-muted me-2"></i>คำแนะนำ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-mouse-pointer text-primary"></i>
                        </div>
                        <div class="help-text">
                            คลิกขวาที่องค์กรเพื่อแสดงเมนูตัวเลือก
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-plus text-success"></i>
                        </div>
                        <div class="help-text">
                            เพิ่มองค์กรย่อยโดยคลิกขวาที่องค์กรหลัก
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-arrows-alt text-info"></i>
                        </div>
                        <div class="help-text">
                            ลากและวางเพื่อย้ายองค์กร
                        </div>
                    </div>

                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-edit text-warning"></i>
                        </div>
                        <div class="help-text">
                            คลิกขวาแล้วเลือก "แก้ไข" เพื่อแก้ไขข้อมูล
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Organization Modal -->
<div class="modal fade" id="organizationModal" tabindex="-1" aria-labelledby="organizationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="organizationModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="organizationForm">
                <div class="modal-body">
                    <input type="hidden" id="nodeId" name="node_id">
                    <input type="hidden" id="parentId" name="parend_id">

                    <div class="mb-3" id="parentInfo" style="display: none;">
                        <div class="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            องค์กรหลัก: <strong id="parentName"></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="organizationName" class="form-label">ชื่อองค์กร</label>
                        <input type="text" class="form-control" id="organizationName" name="name" required
                            placeholder="ระบุชื่อองค์กร">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organizationStatus" class="form-label">สถานะ</label>
                                <select class="form-select" id="organizationStatus" name="status">
                                    <option value="active">ใช้งาน</option>
                                    <option value="inactive">ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organizationSort" class="form-label">ลำดับ</label>
                                <input type="number" class="form-control" id="organizationSort" name="sort" min="1" placeholder="ลำดับการแสดง">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div id="contextMenu" class="dropdown-menu border-0 shadow-lg" style="position: absolute; display: none; z-index: 1000;">
    <h6 class="dropdown-header text-muted">
        <i class="fas fa-cogs me-1"></i>จัดการองค์กร
    </h6>
    <a class="dropdown-item" role="button" onclick="addSubOrganization()">
        <i class="fas fa-plus text-success me-2"></i>เพิ่มองค์กรย่อย
    </a>
    <a class="dropdown-item" role="button" onclick="editOrganization()">
        <i class="fas fa-edit text-warning me-2"></i>แก้ไข
    </a>

    <div class="dropdown-divider"></div>
    <a class="dropdown-item text-danger" role="button" onclick="deleteOrganization()">
        <i class="fas fa-trash me-2"></i>ลบ
    </a>
</div>


<script>
    let organizationTree;
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
        $("#organization-tree").fancytree({
            extensions: ["dnd"],
            source: treeData,
            activate: function(event, data) {
                selectedNode = data.node;
            },
            renderNode: function(event, data) {
                const node = data.node;
                const $nodeSpan = $(node.span);

                $nodeSpan.find('.fancytree-title .status-badge').remove();

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
                    moveOrganization(data.otherNode.key, node.key);
                }
            }
        });

        organizationTree = $("#organization-tree").fancytree("getTree");

        $("#organization-tree").on("contextmenu", ".fancytree-node", function(e) {
            e.preventDefault();
            selectedNode = $.ui.fancytree.getNode(e.target);
            if (selectedNode) {
                showContextMenu(e.pageX, e.pageY);
            }
        });

        $(document).on("click", function() {
            $("#contextMenu").hide();
        });
    }

    function setupEventHandlers() {
        $('#organizationForm').on('submit', function(e) {
            e.preventDefault();
            saveOrganization();
        });
    }

    function showContextMenu(x, y) {
        $("#contextMenu").css({
            left: x + "px",
            top: y + "px",
            display: "block"
        });
    }

    function addOrganization() {
        selectedNode = null;
        resetForm();
        $('#organizationModalLabel').html('<i class="fas fa-building text-primary me-2"></i>เพิ่มองค์กร');
        $('#parentInfo').hide();
        $('#organizationModal').modal('show');
    }

    function addSubOrganization() {
        if (!selectedNode) return;

        resetForm();
        $('#organizationModalLabel').html('<i class="fas fa-building text-primary me-2"></i>เพิ่มองค์กรย่อย');
        $('#parentId').val(selectedNode.key);
        $('#parentName').text(selectedNode.title);
        $('#parentInfo').show();
        $('#contextMenu').hide();
        $('#organizationModal').modal('show');
    }

    function editOrganization() {
        if (!selectedNode) return;

        const data = selectedNode.data;
        resetForm();

        $('#organizationModalLabel').html('<i class="fas fa-edit text-warning me-2"></i>แก้ไของค์กร');
        $('#nodeId').val(data.node_id);
        $('#organizationName').val(data.name);
        $('#organizationStatus').val(data.status);
        $('#organizationSort').val(data.sort);
        $('#parentId').val(data.parend_id || '');

        if (data.parend_id && data.parend_id != 0) {
            const parentNode = organizationTree.getNodeByKey(data.parend_id);
            if (parentNode) {
                $('#parentName').text(parentNode.title);
                $('#parentInfo').show();
            }
        } else {
            $('#parentInfo').hide();
        }

        $('#contextMenu').hide();
        $('#organizationModal').modal('show');
    }

    function deleteOrganization() {
        if (!selectedNode) return;

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบองค์กร "${selectedNode.title}" ใช่หรือไม่?`,
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
                    url: `${base_url}organization/delete/${selectedNode.data.uuid}`,
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
                        Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการลบองค์กร', 'error');
                    }
                });
            }
        });

        $('#contextMenu').hide();
    }

    function saveOrganization() {
        const formData = new FormData($('#organizationForm')[0]);
        const nodeId = $('#nodeId').val();
        const url = nodeId ? `${base_url}organization/update/${nodeId}` : `${base_url}organization/create`;

        const submitBtn = $('#organizationForm button[type="submit"]');
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
                    $('#organizationModal').modal('hide');
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

    function moveOrganization(nodeId, targetId) {
        $.ajax({
            url: `${base_url}organization/move`,
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
                    text: 'เกิดข้อผิดพลาดในการย้ายองค์กร',
                    icon: 'error',
                });
            }
        });
    }

    function refreshTree() {
        $.ajax({
            url: `${base_url}organization/tree-data`,
            type: 'GET',
            success: function(res) {
                if (res && Array.isArray(res)) {
                    treeData = res;

                    if (organizationTree) {
                        $("#organization-tree").fancytree("destroy");
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

    function toggleOrganizationTree() {
        if (!organizationTree) return;

        const btn = $('#collapseExpandBtn');
        const icon = btn.find('i');
        const text = btn.find('span');

        btn.prop('disabled', true);

        if (isTreeCollapsed) {
            icon.removeClass('fa-expand-alt').addClass('fa-spinner fa-spin');
            text.text('กำลังขยาย...');
            btn.attr('title', 'กำลังขยายทั้งหมด');

            setTimeout(() => {
                organizationTree.visit(function(node) {
                    node.setExpanded(true);
                });

                icon.removeClass('fa-spinner fa-spin').addClass('fa-compress-alt');
                text.text('ยุบ');
                btn.attr('title', 'ยุบทั้งหมด');
                btn.prop('disabled', false);
                isTreeCollapsed = false;
            }, 100);
        } else {
            icon.removeClass('fa-compress-alt').addClass('fa-spinner fa-spin');
            text.text('กำลังยุบ...');
            btn.attr('title', 'กำลังยุบทั้งหมด');

            setTimeout(() => {
                organizationTree.visit(function(node) {
                    node.setExpanded(false);
                });

                icon.removeClass('fa-spinner fa-spin').addClass('fa-expand-alt');
                text.text('ขยาย');
                btn.attr('title', 'ขยายทั้งหมด');
                btn.prop('disabled', false);
                isTreeCollapsed = true;
            }, 100);
        }
    }

    function resetForm() {
        $('#organizationForm')[0].reset();
        $('#nodeId').val('');
        $('#parentId').val('');
        $('#organizationStatus').val('active');
    }

    $('#importOrganizationFile').on('change', function() {
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
            url: `${base_url}backend/importOrganization`,
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
                            <th>ชื่อองค์กร</th>
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
                    <td>${error.data.node_id}</td>
                    <td>${error.data.parend_id}</td>
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
            <div class="mt-3">
                <p class="text-muted">
                    <small>
                        <strong>คำแนะนำ:</strong> กรุณาแก้ไขข้อผิดพลาดในไฟล์ Excel และลองนำเข้าอีกครั้ง<br>
                        <strong>รูปแบบข้อมูลที่ถูกต้อง:</strong><br>
                        • ID: ตัวเลข (ไม่ซ้ำ)<br>
                        • Parent ID: ตัวเลข (0 สำหรับองค์กรหลัก หรือ ID ที่มีอยู่ในระบบ/ไฟล์)<br>
                        • ชื่อองค์กร: ข้อความ ≤ 255 ตัวอักษร<br>
                        • ระดับ: ตัวเลข (1=หลัก, 2=ย่อย, ฯลฯ)<br>
                        • ลำดับ: ตัวเลข > 0<br>
                        • สถานะ: active หรือ inactive
                    </small>
                </p>
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

    #collapseExpandBtn {
        transition: all 0.2s ease;
        min-width: 80px;
    }

    #collapseExpandBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.2);
    }
</style>


<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>

