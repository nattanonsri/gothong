<?php
$uri = service('uri');
$requestURI = $uri->getSegment(3);
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
        <div class="dropdown">
            <!-- <button class="btn btn-link text-dark p-1 position-relative" data-bs-toggle="dropdown">
                <i class="fa-solid fa-envelope fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </button> -->
            <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                <h6 class="dropdown-header">Notifications</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item py-3">
                    <div class="d-flex">
<!--                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b780?w=40&h=40&fit=crop&crop=face"-->
<!--                            class="rounded-circle me-3" width="40" height="40" alt="User">-->
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fs-6"><?= USERNAME ?></h6>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center py-2">View all messages</a>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-dark p-0 d-flex align-items-center" data-bs-toggle="dropdown">
<!--                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"-->
<!--                    class="rounded-circle me-2" width="40" height="40" alt="User">-->
                <span class="d-none d-md-block"><?= USERNAME ?></span>
                <i class="fa-solid fa-chevron-down ms-2"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <h6 class="dropdown-header">
                    <div class="d-flex align-items-center">
<!--                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"-->
<!--                            class="rounded-circle me-2" width="40" height="40" alt="User">-->
                        <div>
                            <div class="fw-semibold"><?= USERNAME ?></div>
                        </div>
                    </div>
                </h6>
                <!-- <div class="dropdown-divider"></div> -->
                <!-- <div class="dropdown-item">
                    <i class="fa-solid fa-language me-2"></i>
                    <a href="<?= base_url('backend/change_language?locale=th&requestURI=' . $requestURI) ?>"
                        class="text-decoration-none <?= is_language('th') ? 'text-primary fw-bold' : 'text-dark' ?>">
                        <?= lang('backend.lang_thai') ?>
                    </a>
                    |
                    <a href="<?= base_url('backend/change_language?locale=en&requestURI=' . $requestURI) ?>"
                        class="text-decoration-none <?= is_language('en') ? 'text-primary fw-bold' : 'text-dark' ?>">
                        <?= lang('backend.lang_english') ?>
                    </a>
                </div> -->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= base_url('backend/logout') ?>">
                    <i class="fa-solid fa-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</header>