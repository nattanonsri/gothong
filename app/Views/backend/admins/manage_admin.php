<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h2 class="fw-600 text-dark mb-1">
                <i class="fas fa-users-cog me-2 text-primary"></i>
                <?= lang('app.admin')?>
            </h2>
            <p class="text-muted mb-0">จัดการข้อมูลผู้ใช้และสิทธิ์การเข้าถึงระบบ</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-gradient rounded-2 btn-sm px-4" onclick="openCreateAdminModal()">
                <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้งานใหม่
            </button>
            <button type="button" class="btn btn-gradient-outline rounded-2 btn-sm px-4" onclick="openRoleManagementModal()">
                <i class="fas fa-shield-alt me-2"></i>จัดการสิทธิ์การเข้าถึง
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <!-- <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchAdmin" placeholder="ค้นหา Admin...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterRole">
                        <option value="">ทุก Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Admin List Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-600">
                    <i class="fas fa-list me-2 text-primary"></i>
                    <?= lang('app.admin')?>
                </h5>
                <!-- <div class="d-flex gap-2">
                    <button type="button" class="btn btn-gradient-outline btn-sm" onclick="bulkAssignRoles()">
                        <i class="fas fa-users me-1"></i>มอบหมาย Role แบบกลุ่ม
                    </button>
                </div> -->
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="adminTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">

                            </th>
                            <th>Username</th>
                            <th>Roles</th>
                            <!-- <th>วันที่สร้าง</th> -->
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($admins)):
                            $i = 1;
                            foreach ($admins as $i => $admin): ?>
                                <tr data-admin-id="<?= $admin['id'] ?>" data-username="<?= $admin['username'] ?>" data-roles="<?= $admin['role_ids'] ?? '' ?>">
                                    <td>
                                        <?= $i + 1 ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">

                                            <div>
                                                <h6 class="mb-0 fw-600"><?= $admin['username'] ?></h6>
                                                <small class="text-muted">ID: <?= $admin['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if (!empty($admin['role_names'])): ?>
                                                <?php
                                                $roleNames = explode(', ', $admin['role_names']);
                                                $roleIds = explode(',', $admin['role_ids']);
                                                for ($i = 0; $i < count($roleNames); $i++):
                                                ?>
                                                    <span class="badge bg-primary"><?= trim($roleNames[$i]) ?></span>
                                                <?php endfor; ?>
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มี Role</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></small>
                                    </td> -->
                                    <td>
                                        <!-- <div class="btn-group" role="group"> -->
                                        <!-- <button type="button" class="btn btn-outline-primary btn-sm" onclick="openRoleAssignmentModal(<?= $admin['id'] ?>)" title="จัดการ Role">
                                                <i class="fas fa-shield-alt"></i>
                                            </button> -->
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewAdminProfile(<?= $admin['id'] ?>)" title="ดู Profile">
                                            <i class="fas fa-user"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editAdmin(<?= $admin['id'] ?>)" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAdmin(<?= $admin['id'] ?>)" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <!-- </div> -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p class="mb-0">ไม่พบข้อมูลผู้ดูแลระบบ</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Toast notifications will be added here -->
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAdminModalLabel">
                    <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้งานใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAdminForm">
                    <input type="hidden" id="adminId" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-500">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-500">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="checkPassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500">Roles</label>
                            <div class="row g-2">
                                <?php foreach ($roles as $role): ?>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>">
                                            <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                                <?= $role['name'] ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-gradient" id="btnSave" onclick="saveAdmin()">
                    <i class="fas fa-save me-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Role Management Modal -->
<div class="modal fade" id="roleManagementModal" tabindex="-1" aria-labelledby="roleManagementModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleManagementModalLabel">
                    <i class="fas fa-shield-alt me-2"></i>จัดการสิทธิ์การเข้าถึง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">รายการสิทธิ์การเข้าถึงทั้งหมด</h6>
                    <button type="button" class="btn btn-success btn-sm" onclick="openAddRoleModal()">
                        <i class="fas fa-plus me-1"></i>เพิ่ม Role ใหม่
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อสิทธิ์การเข้าถึง</th>
                                <th>จำนวนผู้ใช้งาน</th>
                                <th width="120">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="roleTableBody">
                            <?php foreach ($roles as $role): ?>
                                <tr data-role-id="<?= $role['id'] ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <span class="fw-500"><?= $role['name'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary" id="adminCount_<?= $role['id'] ?>">0</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="manageRolePermissions(<?= $role['id'] ?>, '<?= $role['name'] ?>')" title="จัดการ Permission">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="editRole(<?= $role['id'] ?>, '<?= $role['name'] ?>')" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRole(<?= $role['id'] ?>, '<?= $role['name'] ?>')" title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Assignment Modal -->
<div class="modal fade" id="roleAssignmentModal" tabindex="-1" aria-labelledby="roleAssignmentModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleAssignmentModalLabel">
                    <i class="fas fa-user-shield me-2"></i>จัดการสิทธิ์การเข้าถึงสำหรับผู้ใช้งาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-600 mb-3">ผู้ใช้งาน: <span id="selectedAdminUsername" class="text-primary"></span></h6>
                        <div class="mb-3">
                            <label class="form-label fw-500">สิทธิ์การเข้าถึงปัจจุบัน:</label>
                            <div id="currentRoles" class="d-flex flex-wrap gap-2">
                                <!-- Current roles will be displayed here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="newRoleSelect" class="form-label fw-500">เพิ่มสิทธิ์การเข้าถึงใหม่:</label>
                            <div class="input-group">
                                <select class="form-select" id="newRoleSelect">
                                    <option value="">เลือกสิทธิ์การเข้าถึง</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-success" type="button" onclick="addRoleToAdmin()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Profile Modal -->
<div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminProfileModalLabel">
                    <i class="fas fa-user me-2"></i>Profile ผู้ใช้งาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="profileLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">กำลังโหลดข้อมูล...</p>
                </div>
                <div id="profileContent" style="display: none;">
                    <div class="row g-4">
                        <!-- Profile Header -->
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4" id="profileAvatar">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1" id="profileUsername">-</h4>
                                    <p class="text-muted mb-0">ID: <span id="profileAdminId">-</span></p>
                                    <!-- <p class="text-muted mb-0">UUID: <span id="profileUuid">-</span></p> -->
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="col-12">
                            <h6 class="fw-600 mb-3 border-bottom pb-2">
                                <i class="fas fa-id-card me-2 text-primary"></i>ข้อมูลส่วนตัว
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">ชื่อภาษาไทย</label>
                                    <p class="mb-0" id="profileNameTh">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">ชื่อภาษาอังกฤษ</label>
                                    <p class="mb-0" id="profileNameEn">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">อีเมล</label>
                                    <p class="mb-0" id="profileEmail">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">เบอร์โทรศัพท์</label>
                                    <p class="mb-0" id="profilePhone">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">เพศ</label>
                                    <p class="mb-0" id="profileGender">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">วันเกิด</label>
                                    <p class="mb-0" id="profileBirthDate">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">เลขบัตรประชาชน</label>
                                    <p class="mb-0" id="profileCardId">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">Customer ID</label>
                                    <p class="mb-0" id="profileCustomerId">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Roles Information -->
                        <div class="col-12">
                            <h6 class="fw-600 mb-3 border-bottom pb-2">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>สิทธิ์การเข้าถึง
                            </h6>
                            <div id="profileRoles" class="d-flex flex-wrap gap-2">
                                <!-- Roles will be displayed here -->
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="col-12">
                            <h6 class="fw-600 mb-3 border-bottom pb-2">
                                <i class="fas fa-info-circle me-2 text-primary"></i>ข้อมูลระบบ
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">วันที่สร้าง</label>
                                    <p class="mb-0" id="profileCreatedAt">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-muted">อัปเดตล่าสุด</label>
                                    <p class="mb-0" id="profileUpdatedAt">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Role Permission Management Modal -->
<div class="modal fade" id="rolePermissionModal" tabindex="-1" aria-labelledby="rolePermissionModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rolePermissionModalLabel">
                    <i class="fas fa-key me-2"></i>จัดการสิทธิ์การเข้าถึงสำหรับผู้ใช้งาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-600 mb-3">สิทธิ์การเข้าถึง: <span id="selectedRoleName" class="text-primary"></span></h6>
                        
                        <!-- Search Permission -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchPermission" placeholder="ค้นหาสิทธิ์การเข้าถึง...">
                            </div>
                        </div>

                        <!-- Permission List -->
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">รายการสิทธิ์การเข้าถึง</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="selectAllPermissions()">
                                        <i class="fas fa-check-double me-1"></i>เลือกทั้งหมด
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAllPermissions()">
                                        <i class="fas fa-times me-1"></i>ยกเลิกทั้งหมด
                                    </button>
                                </div>
                            </div>
                            
                            <div id="permissionList" class="row g-2">
                                <!-- Permissions will be loaded here -->
                            </div>
                        </div>

                        <!-- Selected Permissions Summary -->
                        <div class="mt-3" id="selectedPermissionsSummary" style="display: none;">
                            <h6 class="fw-600 mb-2">
                                <i class="fas fa-list me-2"></i>สิทธิ์การเข้าถึงที่เลือก
                            </h6>
                            <div class="border rounded p-3 bg-light">
                                <div id="selectedPermissionsList" class="d-flex flex-wrap gap-2">
                                    <!-- Selected permissions will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" id="btnSavePermissions" onclick="saveRolePermissions()">
                    <i class="fas fa-save me-2"></i>บันทึกสิทธิ์การเข้าถึง
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Role Assignment Modal -->
<div class="modal fade" id="bulkRoleAssignmentModal" tabindex="-1" aria-labelledby="bulkRoleAssignmentModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkRoleAssignmentModalLabel">
                    <i class="fas fa-users me-2"></i>มอบหมายสิทธิ์การเข้าถึงแบบกลุ่ม
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="modalSearchAdmin" placeholder="ค้นหาผู้ใช้งาน...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="modalFilterRole">
                            <option value="">ทุกสิทธิ์การเข้าถึง</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Selection Controls -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllModal">
                            <label class="form-check-label fw-500" for="selectAllModal">
                                เลือกทั้งหมด
                            </label>
                        </div>
                        <span class="badge bg-primary" id="selectedCount">เลือกแล้ว: 0 คน</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllSelections()">
                            <i class="fas fa-times me-1"></i>ล้างการเลือก
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectAdminsWithNoRoles()">
                            <i class="fas fa-user-plus me-1"></i>เลือกคนที่ไม่มี Role
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-600 mb-3">
                            <i class="fas fa-users me-2"></i>เลือกผู้ใช้งาน
                        </h6>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="modalAdminTable">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" class="form-check-input" id="selectAllModalTable">
                                            </th>
                                            <th>ชื่อผู้ใช้งาน</th>
                                            <th>สิทธิ์การเข้าถึง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($admins as $admin): ?>
                                            <tr data-admin-id="<?= $admin['id'] ?>" data-username="<?= $admin['username'] ?>" data-roles="<?= $admin['role_ids'] ?? '' ?>">
                                                <td>
                                                    <input type="checkbox" class="form-check-input modal-admin-checkbox" value="<?= $admin['id'] ?>">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-600"><?= $admin['username'] ?></h6>
                                                            <small class="text-muted">ID: <?= $admin['id'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php if (!empty($admin['role_names'])): ?>
                                                            <?php
                                                            $roleNames = explode(', ', $admin['role_names']);
                                                            for ($i = 0; $i < count($roleNames); $i++):
                                                            ?>
                                                                <span class="badge bg-primary"><?= trim($roleNames[$i]) ?></span>
                                                            <?php endfor; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">ไม่มีสิทธิ์การเข้าถึง</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Role Selection Panel -->
                    <div class="col-md-4">
                        <h6 class="fw-600 mb-3">
                            <i class="fas fa-shield-alt me-2"></i>เลือกสิทธิ์การเข้าถึงที่ต้องการมอบหมาย
                        </h6>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($roles as $role): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="bulkRoles[]" value="<?= $role['id'] ?>" id="bulkRole_<?= $role['id'] ?>">
                                        <label class="form-check-label d-flex align-items-center" for="bulkRole_<?= $role['id'] ?>">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <div>
                                                <div class="fw-500"><?= $role['name'] ?></div>
                                                <small class="text-muted">รหัสสิทธิ์การเข้าถึง: <?= $role['id'] ?></small>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Admins Summary -->
                <div class="mt-4" id="selectedAdminsSummary" style="display: none;">
                    <h6 class="fw-600 mb-3">
                        <i class="fas fa-list me-2"></i>สรุปผู้ใช้งานที่เลือก
                    </h6>
                    <div class="border rounded p-3 bg-light">
                        <div id="selectedAdminsList" class="d-flex flex-wrap gap-2">
                            <!-- Selected admins will be displayed here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" id="btnBulkAssign" onclick="executeBulkRoleAssignment()" disabled>
                    <i class="fas fa-users me-2"></i>มอบหมายสิทธิ์การเข้าถึง
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAdminId = null;

    // Global variables
    let allAdmins = <?= json_encode($admins) ?>;
    let allRoles = <?= json_encode($roles) ?>;
    let allPermissions = [];
    let currentRoleId = null;

    // Initialize page
    $(document).ready(function() {
        initializePage();
        setupEventListeners();
        updateRoleAdminCounts();
        loadAllPermissions();
    });

    function initializePage() {
        // Add avatar styles
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .avatar-sm { width: 40px; height: 40px; }
                .avatar-lg { width: 60px; height: 60px; }
                .avatar-xl { width: 80px; height: 80px; }
            `)
            .appendTo('head');
    }

    // View Admin Profile
    function viewAdminProfile(adminId) {
        // Show loading, hide content
        $('#profileLoading').show();
        $('#profileContent').hide();
        
        // Reset modal
        $('#adminProfileModal').modal('show');
        
        // Load profile data
        $.ajax({
            url: `${base_url}admin/getAdminProfile`,
            type: 'GET',
            data: { admin_id: adminId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.admin) {
                    displayAdminProfile(res.admin, res.roles || []);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message || 'ไม่สามารถโหลดข้อมูลได้'
                    });
                    $('#adminProfileModal').modal('hide');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
                $('#adminProfileModal').modal('hide');
            }
        });
    }

    function displayAdminProfile(admin, roles) {
        // Hide loading, show content
        $('#profileLoading').hide();
        $('#profileContent').show();
        
        // Set basic info
        $('#profileAdminId').text(admin.id || '-');
        $('#profileUsername').text(admin.username || '-');
        $('#profileUuid').text(admin.uuid || '-');
        
        // Set profile image if exists
        if (admin.image_profile) {
            $('#profileAvatar').html(`<img src="${base_url}backend/profile/image/${admin.image_profile}" alt="Profile" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">`);
        } else {
            $('#profileAvatar').html('<i class="fas fa-user fa-3x"></i>');
        }
        
        // Set personal information
        const nameTh = [admin.perfix_th, admin.first_name_th, admin.last_name_th].filter(Boolean).join(' ') || '-';
        const nameEn = [admin.perfix_en, admin.first_name_en, admin.last_name_en].filter(Boolean).join(' ') || '-';
        $('#profileNameTh').text(nameTh);
        $('#profileNameEn').text(nameEn);
        $('#profileEmail').text(admin.email || '-');
        $('#profilePhone').text(admin.phone || '-');
        
        // Set gender
        let genderText = '-';
        if (admin.gender) {
            genderText = admin.gender === 'M' || admin.gender === 'male' ? 'ชาย' : 
                        admin.gender === 'F' || admin.gender === 'female' ? 'หญิง' : 
                        admin.gender;
        }
        $('#profileGender').text(genderText);
        
        // Set birth date
        if (admin.birth_date) {
            const birthDate = new Date(admin.birth_date);
            $('#profileBirthDate').text(birthDate.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }));
        } else {
            $('#profileBirthDate').text('-');
        }
        
        // Set card ID and customer ID
        $('#profileCardId').text(admin.card_id || '-');
        $('#profileCustomerId').text(admin.customer_id || '-');
        
        // Set roles
        const rolesHtml = roles.length > 0 
            ? roles.map(role => `<span class="badge bg-primary">${role.name}</span>`).join('')
            : '<span class="text-muted">ไม่มี Role</span>';
        $('#profileRoles').html(rolesHtml);
        
        // Set system information
        if (admin.created_at) {
            const createdDate = new Date(admin.created_at);
            $('#profileCreatedAt').text(createdDate.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }));
        } else {
            $('#profileCreatedAt').text('-');
        }
        
        if (admin.updated_at) {
            const updatedDate = new Date(admin.updated_at);
            $('#profileUpdatedAt').text(updatedDate.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }));
        } else {
            $('#profileUpdatedAt').text('-');
        }
    }

    function setupEventListeners() {
        // Search functionality with debounce
        $('#searchAdmin').on('keyup', debounce(filterAdmins, 300));
        // Filter functionality
        $('#filterRole').on('change', filterAdmins);

        // Select all checkbox
        $('#selectAll').on('change', () => {
            $('.admin-checkbox').prop('checked', this.checked);
        });

        // Individual checkbox change
        $(document).on('change', '.admin-checkbox', function() {
            updateSelectAllState();
        });
    }

    function filterAdmins() {
        const searchTerm = ($('#searchAdmin').val() || '').toLowerCase();
        const roleFilter = $('#filterRole').val() || '';


        $('#adminTable tbody tr').each(function() {
            const $row = $(this);
            const username = ($row.find('td:eq(1) h6').text() || '').toLowerCase();
            const rolesAttr = ($row.data('roles') ?? '').toString();
            const roleIds = rolesAttr ? rolesAttr.split(',').map(s => s.trim()) : [];

            let showRow = true;

            if (searchTerm && !username.includes(searchTerm)) {
                showRow = false;
            }
            if (showRow && roleFilter && !roleIds.includes(roleFilter)) {
                showRow = false;
            }

            $row.toggle(showRow);
        });
    }

    function updateSelectAllState() {
        const totalCheckboxes = $('.admin-checkbox').length;
        const checkedCheckboxes = $('.admin-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    function updateRoleAdminCounts() {
        allRoles.forEach(role => {
            let count = 0;
            allAdmins.forEach(admin => {
                if (admin.role_ids && admin.role_ids.includes(role.id.toString())) {
                    count++;
                }
            });
            $(`#adminCount_${role.id}`).text(count);
        });
    }

    // Modal Functions
    function openCreateAdminModal() {
        $('#createAdminForm')[0].reset();
        $('#adminId').val('');
        $('#createAdminModal').modal('show');
    }

    function openRoleManagementModal() {
        updateRoleAdminCounts();
        $('#roleManagementModal').modal('show');
    }

    function openAddRoleModal() {
        Swal.fire({
            title: 'เพิ่ม Role ใหม่',
            input: 'text',
            inputLabel: 'ชื่อ Role',
            inputPlaceholder: 'กรอกชื่อ Role',
            showCancelButton: true,
            confirmButtonText: 'เพิ่ม',
            cancelButtonText: 'ยกเลิก',
            inputValidator: (value) => {
                if (!value) {
                    return 'กรุณากรอกชื่อ Role';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                createNewRole(result.value);
            }
        });
    }

    function editRole(roleId, roleName) {
        Swal.fire({
            title: 'แก้ไข Role',
            input: 'text',
            inputLabel: 'ชื่อ Role',
            inputValue: roleName,
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            inputValidator: (value) => {
                if (!value) {
                    return 'กรุณากรอกชื่อ Role';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateRole(roleId, result.value);
            }
        });
    }

    function deleteRole(roleId, roleName) {
        Swal.fire({
            title: 'ยืนยันการลบ Role',
            text: `คุณต้องการลบ Role "${roleName}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                performDeleteRole(roleId);
            }
        });
    }

    function editAdmin(adminId) {
        const admin = allAdmins.find(a => a.id == adminId);
        if (!admin) return;

        $('#adminId').val(admin.id);
        $('#username').val(admin.username);

        $('input[name="roles[]"]').prop('checked', false);

        if (admin.role_ids) {
            const roleIds = admin.role_ids.split(',').map(id => id.trim());
            roleIds.forEach(roleId => {
                $(`#role_${roleId}`).prop('checked', true);
            });
        }

        $('#createAdminModal').modal('show');
    }

    function bulkAssignRoles() {
        // Clear all selections and filters
        clearModalSelections();
        resetModalFilters();

        // Show modal
        $('#bulkRoleAssignmentModal').modal('show');
        setupModalEventListeners();
    }

    function setupModalEventListeners() {
        // Modal search functionality
        const debouncedModalFilter = debounce(filterModalAdmins, 300);
        $('#modalSearchAdmin').off('keyup').on('keyup', debouncedModalFilter);

        // Modal filter functionality
        $('#modalFilterRole').off('change').on('change', function() {
            filterModalAdmins();
        });

        // Modal select all checkbox
        $('#selectAllModal, #selectAllModalTable').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.modal-admin-checkbox').prop('checked', isChecked);
            updateModalSelectionState();
        });

        // Individual modal checkbox change
        $(document).off('change', '.modal-admin-checkbox').on('change', '.modal-admin-checkbox', function() {
            updateModalSelectionState();
        });

        // Role selection change
        $('input[name="bulkRoles[]"]').off('change').on('change', function() {
            updateBulkAssignButton();
        });
    }

    function filterModalAdmins() {
        const searchTerm = $('#modalSearchAdmin').val().toLowerCase();
        const roleFilter = $('#modalFilterRole').val();

        $('#modalAdminTable tbody tr').each(function() {
            const $row = $(this);
            const username = $row.find('td:eq(1) h6').text().toLowerCase();
            const rolesAttr = $row.data('roles') || '';
            const roleIds = rolesAttr ? rolesAttr.split(',').map(s => s.trim()) : [];

            let showRow = true;

            // Search filter
            if (searchTerm && !username.includes(searchTerm)) {
                showRow = false;
            }

            // Role filter
            if (roleFilter && !roleIds.includes(roleFilter)) {
                showRow = false;
            }

            $row.toggle(showRow);
        });
    }

    function updateModalSelectionState() {
        const totalCheckboxes = $('.modal-admin-checkbox:visible').length;
        const checkedCheckboxes = $('.modal-admin-checkbox:checked').length;

        $('#selectAllModal, #selectAllModalTable').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        $('#selectedCount').text(`เลือกแล้ว: ${checkedCheckboxes} คน`);

        // Show/hide selected admins summary
        if (checkedCheckboxes > 0) {
            $('#selectedAdminsSummary').show();
            updateSelectedAdminsList();
        } else {
            $('#selectedAdminsSummary').hide();
        }

        updateBulkAssignButton();
    }

    function updateSelectedAdminsList() {
        const selectedAdmins = $('.modal-admin-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        $('#selectedAdminsList').empty();
        selectedAdmins.forEach(adminId => {
            const admin = allAdmins.find(a => a.id == adminId);
            if (admin) {
                $('#selectedAdminsList').append(`
                    <div class="badge bg-primary d-flex align-items-center gap-1">
                        <i class="fas fa-user"></i>
                        ${admin.username}
                        <button type="button" class="btn-close btn-close-white btn-sm" 
                                onclick="deselectAdmin(${adminId})"></button>
                    </div>
                `);
            }
        });
    }

    function updateBulkAssignButton() {
        const selectedAdmins = $('.modal-admin-checkbox:checked').length;
        const selectedRoles = $('input[name="bulkRoles[]"]:checked').length;

        const canAssign = selectedAdmins > 0 && selectedRoles > 0;
        $('#btnBulkAssign').prop('disabled', !canAssign);

        if (canAssign) {
            $('#btnBulkAssign').html(`<i class="fas fa-users me-2"></i>มอบหมาย Role (${selectedAdmins} คน, ${selectedRoles} Role)`);
        } else {
            $('#btnBulkAssign').html('<i class="fas fa-users me-2"></i>มอบหมาย Role');
        }
    }

    function clearAllSelections() {
        $('.modal-admin-checkbox').prop('checked', false);
        $('input[name="bulkRoles[]"]').prop('checked', false);
        updateModalSelectionState();
    }

    function selectAdminsWithNoRoles() {
        $('.modal-admin-checkbox').prop('checked', false);
        $('.modal-admin-checkbox').each(function() {
            const $row = $(this).closest('tr');
            const roles = $row.data('roles') || '';
            const rolesString = String(roles);
            if (!roles || rolesString.trim() === '') {
                $(this).prop('checked', true);
            }
        });
        updateModalSelectionState();
    }

    function deselectAdmin(adminId) {
        $(`.modal-admin-checkbox[value="${adminId}"]`).prop('checked', false);
        updateModalSelectionState();
    }

    function clearModalSelections() {
        $('.modal-admin-checkbox').prop('checked', false);
        $('input[name="bulkRoles[]"]').prop('checked', false);
        $('#selectedAdminsSummary').hide();
        $('#selectedCount').text('เลือกแล้ว: 0 คน');
        $('#btnBulkAssign').prop('disabled', true).html('<i class="fas fa-users me-2"></i>มอบหมาย Role');
    }

    function resetModalFilters() {
        $('#modalSearchAdmin').val('');
        $('#modalFilterRole').val('');
        $('#modalAdminTable tbody tr').show();
    }

    function executeBulkRoleAssignment() {
        const selectedAdmins = $('.modal-admin-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        const selectedRoles = $('input[name="bulkRoles[]"]:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        if (selectedAdmins.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือก Admin',
                text: 'โปรดเลือก Admin ที่ต้องการมอบหมาย Role'
            });
            return;
        }

        if (selectedRoles.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือก Role',
                text: 'โปรดเลือก Role ที่ต้องการมอบหมาย'
            });
            return;
        }

        // Show confirmation dialog
        const adminNames = selectedAdmins.map(adminId => {
            const admin = allAdmins.find(a => a.id == adminId);
            return admin ? admin.username : `ID: ${adminId}`;
        }).join(', ');

        const roleNames = selectedRoles.map(roleId => {
            const role = allRoles.find(r => r.id == roleId);
            return role ? role.name : `ID: ${roleId}`;
        }).join(', ');

        Swal.fire({
            title: 'ยืนยันการมอบหมาย Role',
            html: `
                <div class="text-start">
                    <p><strong>Admin ที่เลือก (${selectedAdmins.length} คน):</strong></p>
                    <p class="text-muted mb-3">${adminNames}</p>
                    <p><strong>Role ที่จะมอบหมาย (${selectedRoles.length} Role):</strong></p>
                    <p class="text-muted">${roleNames}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ยืนยันการมอบหมาย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}admin/bulkAssignRoles`,
                    type: 'POST',
                    data: {
                        admin_ids: selectedAdmins,
                        role_ids: selectedRoles
                    },
                    dataType: 'json',
                    headers: {
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        buttonLoading('#btnBulkAssign');
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#bulkRoleAssignmentModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr) {
                        buttonReset('#btnBulkAssign');
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                        });
                    },
                    complete: function() {
                        buttonReset('#btnBulkAssign');
                    }
                });
            }
        });
    }

    // CRUD Functions
    function saveAdmin() {
        const id = $('#adminId').val();

        const formData = new FormData();
        formData.append('id', id);
        formData.append('username', $('#username').val());
        formData.append('password', $('#password').val());
        $('input[name="roles[]"]:checked').each(function() {
            formData.append('roles[]', $(this).val());
        });



        const url = id ? `${base_url}admin/updateAdmin` : `${base_url}admin/createAdmin`;

        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSave');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSave');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            },
            complete: function() {
                $('#btnSave').prop('disabled', false).html('<i class="fas fa-save me-2"></i>บันทึก');
            }
        });
    }

    function createNewRole(roleName) {
        $.ajax({
            url: `${base_url}admin/createRole`,
            type: 'POST',
            data: {
                name: roleName
            },
            dataType: 'json',
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSave');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSave');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            }
        });
    }

    function updateRole(roleId, roleName) {
        $.ajax({
            url: `${base_url}admin/updateRole`,
            type: 'POST',
            data: {
                id: roleId,
                name: roleName
            },
            dataType: 'json',
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSave');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSave');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            }
        });
    }

    function performDeleteRole(roleId) {
        $.ajax({
            url: `${base_url}admin/deleteRole`,
            type: 'POST',
            data: {
                id: roleId
            },
            dataType: 'json',
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSave');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSave');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            }
        });
    }

    function getRoleNameById(roleId) {
        const role = allRoles.find(r => r.id == roleId);
        return role ? role.name : '';
    }

    // Add role to admin
    function addRoleToAdmin() {
        const roleId = $('#newRoleSelect').val();
        if (!roleId) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือก Role',
                text: 'โปรดเลือก Role ที่ต้องการเพิ่ม'
            });
            return;
        }

        $.ajax({
            url: `${base_url}admin/addRoleToAdmin`,
            method: 'POST',
            data: JSON.stringify({
                admin_id: currentAdminId,
                role_id: parseInt(roleId)
            }),
            contentType: 'application/json',
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSave');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSave');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            }
        });
    }

    // Remove role from admin
    function removeRoleFromAdmin(adminId, roleId) {
        Swal.fire({
            title: 'ยืนยันการลบ Role',
            text: 'คุณต้องการลบ Role นี้จากผู้ดูแลระบบหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}admin/removeRoleFromAdmin`,
                    method: 'POST',
                    data: JSON.stringify({
                        admin_id: adminId,
                        role_id: roleId
                    }),
                    contentType: 'application/json',
                    headers: {
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        buttonLoading('#btnSave');
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                        });
                    }
                });
            }
        });
    }

    // Delete admin
    function deleteAdmin(adminId) {
        const admin = allAdmins.find(a => a.id == adminId);
        if (!admin) return;

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบผู้ดูแลระบบ "${admin.username}" หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}admin/deleteAdmin`,
                    type: 'POST',
                    data: {
                        id: adminId
                    },
                    dataType: 'json',
                    headers: {
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        buttonLoading('#btnSave');
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                        });
                    }
                });
            }
        });
    }

    // Form validation helper
    function validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const toast = $(`
            <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);

        $('.toast-container').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();

        // Remove toast element after it's hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Format date helper
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Copy to clipboard helper
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('คัดลอกสำเร็จ', 'success');
        }, function(err) {
            console.error('Could not copy text: ', err);
            showToast('ไม่สามารถคัดลอกได้', 'error');
        });
    }

    // Permission Management Functions
    function loadAllPermissions() {
        $.ajax({
            url: `${base_url}admin/getAllPermissions`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    allPermissions = res.permissions;
                } else {
                    console.error('Failed to load permissions:', res.message);
                }
            },
            error: function(xhr) {
                console.error('Error loading permissions:', xhr.responseJSON?.message || 'Unknown error');
            }
        });
    }

    function manageRolePermissions(roleId, roleName) {
        currentRoleId = roleId;
        $('#selectedRoleName').text(roleName);
        
        // Load current role permissions
        loadRolePermissions(roleId);
        
        // Show modal
        $('#rolePermissionModal').modal('show');
    }

    function loadRolePermissions(roleId) {
        $.ajax({
            url: `${base_url}admin/getRolePermissions`,
            type: 'GET',
            data: { role_id: roleId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    displayPermissions(res.permissions);
                } else {
                    console.error('Failed to load role permissions:', res.message);
                    displayPermissions([]);
                }
            },
            error: function(xhr) {
                console.error('Error loading role permissions:', xhr.responseJSON?.message || 'Unknown error');
                displayPermissions([]);
            }
        });
    }

    function displayPermissions(rolePermissions) {
        const permissionIds = rolePermissions.map(p => p.permission_id);
        
        $('#permissionList').empty();
        
        if (allPermissions.length === 0) {
            $('#permissionList').html('<div class="col-12 text-center text-muted py-3">ไม่พบ Permission</div>');
            return;
        }

        allPermissions.forEach(permission => {
            const isChecked = permissionIds.includes(permission.id);
            const permissionHtml = `
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input permission-checkbox" type="checkbox" 
                               value="${permission.id}" id="permission_${permission.id}" 
                               ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="permission_${permission.id}">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <div class="fw-500">${permission.permission_name}</div>
                                    <small class="text-muted">ID: ${permission.id}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
            $('#permissionList').append(permissionHtml);
        });

        // Setup permission checkbox event listeners
        $('.permission-checkbox').off('change').on('change', function() {
            updateSelectedPermissionsSummary();
        });

        // Setup search functionality
        $('#searchPermission').off('keyup').on('keyup', function() {
            filterPermissions();
        });

        updateSelectedPermissionsSummary();
    }

    function filterPermissions() {
        const searchTerm = $('#searchPermission').val().toLowerCase();
        
        $('.permission-checkbox').each(function() {
            const $checkbox = $(this);
            const $label = $checkbox.next('label');
            const permissionName = $label.find('.fw-500').text().toLowerCase();
            
            if (permissionName.includes(searchTerm)) {
                $checkbox.closest('.col-md-6').show();
            } else {
                $checkbox.closest('.col-md-6').hide();
            }
        });
    }

    function selectAllPermissions() {
        $('.permission-checkbox:visible').prop('checked', true);
        updateSelectedPermissionsSummary();
    }

    function deselectAllPermissions() {
        $('.permission-checkbox:visible').prop('checked', false);
        updateSelectedPermissionsSummary();
    }

    function updateSelectedPermissionsSummary() {
        const selectedPermissions = $('.permission-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        if (selectedPermissions.length > 0) {
            $('#selectedPermissionsSummary').show();
            updateSelectedPermissionsList(selectedPermissions);
        } else {
            $('#selectedPermissionsSummary').hide();
        }
    }

    function updateSelectedPermissionsList(selectedPermissionIds) {
        $('#selectedPermissionsList').empty();
        
        selectedPermissionIds.forEach(permissionId => {
            const permission = allPermissions.find(p => p.id == permissionId);
            if (permission) {
                $('#selectedPermissionsList').append(`
                    <div class="badge bg-success d-flex align-items-center gap-1">
                        <i class="fas fa-key"></i>
                        ${permission.permission_name}
                        <button type="button" class="btn-close btn-close-white btn-sm" 
                                onclick="deselectPermission(${permissionId})"></button>
                    </div>
                `);
            }
        });
    }

    function deselectPermission(permissionId) {
        $(`#permission_${permissionId}`).prop('checked', false);
        updateSelectedPermissionsSummary();
    }

    function saveRolePermissions() {
        if (!currentRoleId) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่พบ Role ID',
                text: 'กรุณาเลือก Role ใหม่'
            });
            return;
        }

        const selectedPermissions = $('.permission-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        $.ajax({
            url: `${base_url}admin/saveRolePermissions`,
            type: 'POST',
            data: {
                role_id: currentRoleId,
                permission_ids: selectedPermissions
            },
            dataType: 'json',
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnSavePermissions');
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#rolePermissionModal').modal('hide');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                buttonReset('#btnSavePermissions');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
                });
            },
            complete: function() {
                buttonReset('#btnSavePermissions');
            }
        });
    }
</script>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>
<?= $this->endSection() ?>