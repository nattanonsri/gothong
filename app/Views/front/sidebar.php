<?php

$uri = service('uri');
$requestURI = $uri->getSegment(3);

$menuItems = [
    'dashboard' => [
        'icon' => 'fa-solid fa-house me-2',
        'title' => lang('app.dashboard'),
        'url' => base_url('backend/dashboard'),
        'badge' => null,
        'permission' => 'view_dashboard'
    ],
    'record' => [
        'icon' => 'fa-solid fa-file-pen me-2',
        'title' => lang('app.record'),
        'url' => base_url('backend/record'),
        'badge' => null,
        'permission' => 'view_record'
    ],
    // 'category' => [
    //     'icon' => 'fa-solid fa-layer-group me-2',
    //     'title' => lang('app.category'),
    //     'url' => base_url('backend/category'),
    //     'badge' => null,
    //     'permission' => 'view_category'
    // ],
    // 'organization' => [
    //     'icon' => 'fa-solid fa-building me-2',
    //     'title' => lang('app.organization'),
    //     'url' => base_url('backend/organization'),
    //     'badge' => null,
    //     'permission' => 'view_organization'
    // ],
];

$reportItems = [
    'totalIncome' => [
        'icon' => 'fa-solid fa-money-bill me-2',
        'title' => lang('app.totalIncome'),
        'url' => base_url('backend/totalIncome'),
        'badge' => null,
        'permission' => 'view_totalIncome'
    ],
    'totalExpenses' => [
        'icon' => 'fa-solid fa-money-bill-wave me-2',
        'title' => lang('app.totalExpenses'),
        'url' => base_url('backend/totalExpenses'),
        'badge' => null,
        'permission' => 'view_totalExpenses'
    ],
];

$settingsItems = [
    'admin' => [
        'icon' => 'fa-solid fa-user-shield me-2',
        'title' => lang('app.admin'),
        'url' => base_url('backend/admin'),
        'permission' => 'view_admin'
    ],
    // 'payment' => [
    //     'icon' => 'fa-solid fa-money-bill-wave me-2',
    //     'title' => lang('app.payment'),
    //     'url' => base_url('backend/payment'),
    //     'permission' => 'view_payment'
    // ],
];


?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="navbar-brand text-white">
        <span class="fw-bolder fs-2 mb-3 mt-3 bg-icon">Gothong</span>
    </div>

    <div class="nav flex-column">
        <?php foreach ($menuItems as $key => $item): ?>
            <?php if (has_permission($item['permission'])): ?>
                <a href="<?= $item['url'] ?>" class="nav-link <?= $requestURI == $key ? 'active' : '' ?>" <?= $item['permission'] ? 'data-permission="' . $item['permission'] . '"' : '' ?>>
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= $item['title'] ?></span>
                    <?php if ($item['badge']): ?>
                        <span class="badge"><?= ucfirst($item['badge']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Reports Section -->
        <?php if (has_permission('view_reports')): ?>
            <div class="nav-item">
                <a class="nav-link collapse-toggle" role="button" data-bs-toggle="collapse" data-bs-target="#reportsCollapse" aria-expanded="false" aria-controls="reportsCollapse">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    <span><?= lang('app.reports') ?></span>
                    <i class="fa-solid fa-chevron-down collapse-arrow ms-auto"></i>
                </a>
                <div class="collapse" id="reportsCollapse">
                    <div class="collapse-content">
                        <?php foreach ($reportItems as $key => $item): ?>
                            <?php if (has_permission($item['permission'])): ?>
                                <a class="nav-link nav-sub-link <?= $requestURI == $key ? 'active' : '' ?>" href="<?= $item['url'] ?>">
                                    <i class="<?= $item['icon'] ?>"></i>
                                    <span><?= $item['title'] ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Settings Section -->
        <?php if (has_permission('view_settings')): ?>
            <div class="nav-item">
                <a class="nav-link collapse-toggle" role="button" data-bs-toggle="collapse" data-bs-target="#settingsCollapse" aria-expanded="false" aria-controls="settingsCollapse">
                    <i class="fa-solid fa-cogs me-2"></i>
                    <span><?= lang('app.settings') ?></span>
                    <i class="fa-solid fa-chevron-down collapse-arrow ms-auto"></i>
                </a>
                <div class="collapse" id="settingsCollapse">
                    <div class="collapse-content">
                        <?php foreach ($settingsItems as $key => $item): ?>
                            <?php if (has_permission($item['permission'])): ?>
                                <a class="nav-link nav-sub-link <?= $requestURI == $key ? 'active' : '' ?>" href="<?= $item['url'] ?>">
                                    <i class="<?= $item['icon'] ?>"></i>
                                    <span><?= $item['title'] ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>