<?php
$uri = service('uri');
$requestURI = $uri->getSegment(3);

// ดึงข้อมูลผู้ใช้จาก session
$session = session();
$userModel = new \App\Models\UserModel();
$userId = $session->get('user_id');
$username = $session->get('username');
$user = $userModel->find($userId);
$userImage = $user['image_profile'] ?? null;
$userFullName = trim(($user['perfix_th'] ?? '') . ' ' . ($user['first_name_th'] ?? '') . ' ' . ($user['last_name_th'] ?? ''));
if (empty($userFullName)) {
    $userFullName = $username;
}
?>
<header class="main-header d-flex align-items-center justify-content-between px-4" id="mainHeader">
    <div class="d-flex align-items-center">
        <button class="btn btn-link text-dark p-0 me-3 d-lg-none" id="sidebarToggle">
            <i class="fa-solid fa-list fs-4"></i>
        </button>
        <button class="btn btn-link text-dark p-0 me-3 d-none d-lg-block" id="sidebarCollapseToggle">
            <i class="fa-solid fa-list fs-4"></i>
        </button>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-dark p-0 d-flex align-items-center" data-bs-toggle="dropdown">
                <?php if ($userImage && file_exists(WRITEPATH . 'uploads/profiles/' . $userImage)): ?>
                    <img src="<?= base_url('backend/profile/image/' . $userImage) ?>" 
                         class="rounded-circle me-2" width="40" height="40" alt="User Profile"
                         style="object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white" 
                         style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                        <?= strtoupper(substr($username, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <span class="d-none d-md-block fw-medium"><?= $userFullName ?></span>
                <i class="fa-solid fa-chevron-down ms-2"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                <h6 class="dropdown-header">
                    <div class="d-flex align-items-center">
                        <?php if ($userImage && file_exists(WRITEPATH . 'uploads/profiles/' . $userImage)): ?>
                            <img src="<?= base_url('backend/profile/image/' . $userImage) ?>" 
                                 class="rounded-circle me-3" width="50" height="50" alt="User Profile"
                                 style="object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                                 style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold"><?= $userFullName ?></div>
                            <small class="text-muted"><?= $user['email'] ?? $username ?></small>
                        </div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center py-2" href="<?= base_url('backend/profile') ?>">
                    <i class="fa-solid fa-user me-3 text-primary"></i>
                    <div>
                        <div class="fw-medium"><?= lang('backend.profile') ?></div>
                        <small class="text-muted">จัดการข้อมูลส่วนตัว</small>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center py-2" href="<?= base_url('backend/profile/edit') ?>">
                    <i class="fa-solid fa-user-edit me-3 text-info"></i>
                    <div>
                        <div class="fw-medium">แก้ไขโปรไฟล์</div>
                        <small class="text-muted">อัปเดตข้อมูลส่วนตัว</small>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="<?= base_url('backend/logout') ?>">
                    <i class="fa-solid fa-box-arrow-right me-3"></i>
                    <div>
                        <div class="fw-medium">ออกจากระบบ</div>
                        <small class="text-muted">Logout</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</header>