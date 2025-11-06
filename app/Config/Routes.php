<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$baseSubURL = env('app.baseDIR');

// Basic routes
$routes->get($baseSubURL, 'BackendController::index');

// Authentication routes
$routes->get($baseSubURL . '/backend/login', 'BackendController::login');
$routes->post($baseSubURL . '/backend/adminAuth', 'BackendController::admin_Auth'); // แก้ไขให้ตรงกับ method ใน Controller
$routes->get($baseSubURL . '/backend/logout', 'BackendController::logout');

// Dashboard routes
$routes->get($baseSubURL . '/backend', 'DashboardController::goto_dashboard', ['filter' => 'permission:view_dashboard']);
$routes->get($baseSubURL . '/backend/dashboard', 'DashboardController::goto_dashboard', ['filter' => 'permission:view_dashboard']);
$routes->get($baseSubURL . '/dashboard/getDashboardData', 'DashboardController::getDashboardData', ['filter' => 'permission:view_dashboard']);


// Profile routes
$routes->get($baseSubURL . '/backend/profile', 'BackendController::profile', ['filter' => 'permission:view_profile']);
$routes->get($baseSubURL . '/backend/profile/edit', 'BackendController::edit_profile', ['filter' => 'permission:view_profile']);
$routes->post($baseSubURL . '/backend/profile/update', 'BackendController::update_profile', ['filter' => 'permission:view_profile']);
$routes->get($baseSubURL . '/backend/profile/image/(:any)', 'BackendController::get_images_profile/$1', ['filter' => 'permission:view_profile']);


    // Record routes
$routes->get($baseSubURL . '/backend/record', 'RecordController::index', ['filter' => 'permission:view_record']);
$routes->post($baseSubURL . '/record/list', 'RecordController::list_transactions', ['filter' => 'permission:view_record']);
$routes->get($baseSubURL . '/record/get/(:any)', 'RecordController::get_transaction/$1', ['filter' => 'permission:view_record']);
$routes->post($baseSubURL . '/record/create', 'RecordController::create', ['filter' => 'permission:view_record']);
$routes->post($baseSubURL . '/record/update/(:any)', 'RecordController::update/$1', ['filter' => 'permission:view_record']);
$routes->post($baseSubURL . '/record/delete/(:any)', 'RecordController::delete/$1', ['filter' => 'permission:view_record']);
$routes->post($baseSubURL . '/record/delete-attachment/(:any)', 'RecordController::delete_attachment/$1', ['filter' => 'permission:view_record']);
$routes->get($baseSubURL . '/record/counterparties', 'RecordController::get_counterparties', ['filter' => 'permission:view_record']);
$routes->get($baseSubURL . '/record/image/(:any)', 'RecordController::get_images/$1', ['filter' => 'permission:view_record']);

// Payment routes
$routes->get($baseSubURL . '/backend/payment', 'PaymentController::index', ['filter' => 'permission:view_payment']);
$routes->post($baseSubURL . '/payment/list', 'PaymentController::list_payments', ['filter' => 'permission:view_payment']);
$routes->get($baseSubURL . '/payment/get/(:any)', 'PaymentController::get_payment/$1', ['filter' => 'permission:view_payment']);
$routes->post($baseSubURL . '/payment/create', 'PaymentController::create', ['filter' => 'permission:view_payment']);
$routes->post($baseSubURL . '/payment/update/(:any)', 'PaymentController::update/$1', ['filter' => 'permission:view_payment']);
$routes->post($baseSubURL . '/payment/delete/(:any)', 'PaymentController::delete/$1', ['filter' => 'permission:view_payment']);

// Organization routes
$routes->get($baseSubURL . '/backend/organization', 'OrganizationController::index', ['filter' => 'permission:view_organization']);
$routes->get($baseSubURL . '/organization/tree-data', 'OrganizationController::get_tree_data', ['filter' => 'permission:view_organization']);
$routes->post($baseSubURL . '/organization/create', 'OrganizationController::create', ['filter' => 'permission:view_organization']);
$routes->post($baseSubURL . '/organization/update/(:any)', 'OrganizationController::update/$1', ['filter' => 'permission:view_organization']);
$routes->post($baseSubURL . '/organization/delete/(:any)', 'OrganizationController::delete/$1', ['filter' => 'permission:view_organization']);
$routes->post($baseSubURL . '/organization/move', 'OrganizationController::move', ['filter' => 'permission:view_organization']);
$routes->post($baseSubURL . '/backend/importOrganization', 'OrganizationController::import_organization', ['filter' => 'permission:view_organization']);
$routes->get($baseSubURL . '/backend/exportOrganization', 'OrganizationController::export_organization', ['filter' => 'permission:view_organization']);
$routes->get($baseSubURL . '/backend/downloadTemplateOrganization', 'OrganizationController::download_template_organization', ['filter' => 'permission:view_organization']);

// Report routes
$routes->get($baseSubURL . '/backend/totalIncome', 'ReportController::totalIncome', ['filter' => 'permission:view_totalIncome']);
$routes->get($baseSubURL . '/backend/totalExpenses', 'ReportController::totalExpenses', ['filter' => 'permission:view_totalExpenses']);
$routes->get($baseSubURL . '/report/income-data', 'ReportController::getIncomeData', ['filter' => 'permission:view_totalIncome']);
$routes->get($baseSubURL . '/report/expenses-data', 'ReportController::getExpensesData', ['filter' => 'permission:view_totalExpenses']);
$routes->get($baseSubURL . '/report/categories', 'ReportController::getCategories', ['filter' => 'permission:view_totalIncome']);
$routes->get($baseSubURL . '/report/export-income', 'ReportController::exportIncome', ['filter' => 'permission:view_totalIncome']);
$routes->get($baseSubURL . '/report/export-expenses', 'ReportController::exportExpenses', ['filter' => 'permission:view_totalExpenses']);

// Category routes
$routes->get($baseSubURL . '/backend/category', 'CategoryController::index', ['filter' => 'permission:view_category']);
$routes->get($baseSubURL . '/category/tree-data', 'CategoryController::get_tree_data', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/create', 'CategoryController::create', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/update/(:any)', 'CategoryController::update/$1', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/delete/(:any)', 'CategoryController::delete/$1', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/move', 'CategoryController::move', ['filter' => 'permission:view_category']);
$routes->get($baseSubURL . '/category/(:any)/brands', 'CategoryController::manage_category_brands/$1', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/add-brands', 'CategoryController::add_brands_category', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/category/remove-brands', 'CategoryController::remove_brands_from_category', ['filter' => 'permission:view_category']);
$routes->get($baseSubURL . '/category/(:any)/brands-data', 'CategoryController::get_category_brands/$1', ['filter' => 'permission:view_category']);
$routes->post($baseSubURL . '/backend/importCategory', 'CategoryController::import_category', ['filter' => 'permission:view_category']);
$routes->get($baseSubURL . '/backend/exportCategory', 'CategoryController::export_category', ['filter' => 'permission:view_category']);
$routes->get($baseSubURL . '/backend/downloadTemplateCategory', 'CategoryController::download_template_category', ['filter' => 'permission:view_category']);

// Admin management routes
$routes->get($baseSubURL . '/backend/admin', 'AdminController::goto_admin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/editAdmin', 'AdminController::edit_admin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/listAdmin', 'AdminController::list_admin', ['filter' => 'permission:view_admin']);

// Role management routes
$routes->post($baseSubURL . '/backend/roleStatus', 'AdminController::role_status', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/addRole', 'AdminController::add_role', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/deleteRole', 'AdminController::delete_role', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/editRoleModal', 'AdminController::edit_role_modal', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/editRole', 'AdminController::edit_role', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/assignModal', 'AdminController::assign_role_modal', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/assignRole', 'AdminController::assign_role', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/listUserRole', 'AdminController::list_user_role', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/backend/deleteUserRole', 'AdminController::delete_user_role', ['filter' => 'permission:view_admin']);

// API-style admin management routes
$routes->post($baseSubURL . '/admin/createAdmin', 'AdminController::createAdmin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/updateAdmin', 'AdminController::updateAdmin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/deleteAdmin', 'AdminController::deleteAdmin', ['filter' => 'permission:view_admin']);
$routes->get($baseSubURL . '/admin/getAdminRoles', 'AdminController::getAdminRoles', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/addRoleToAdmin', 'AdminController::addRoleToAdmin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/removeRoleFromAdmin', 'AdminController::removeRoleFromAdmin', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/bulkAssignRoles', 'AdminController::bulkAssignRoles', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/createRole', 'AdminController::createRole', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/updateRole', 'AdminController::updateRole', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/deleteRole', 'AdminController::deleteRole', ['filter' => 'permission:view_admin']);

// Permission management routes
$routes->get($baseSubURL . '/admin/getAllPermissions', 'AdminController::getAllPermissions', ['filter' => 'permission:view_admin']);
$routes->get($baseSubURL . '/admin/getRolePermissions', 'AdminController::getRolePermissions', ['filter' => 'permission:view_admin']);
$routes->post($baseSubURL . '/admin/saveRolePermissions', 'AdminController::saveRolePermissions', ['filter' => 'permission:view_admin']);
$routes->get($baseSubURL . '/admin/getAdminProfile', 'AdminController::getAdminProfile', ['filter' => 'permission:view_admin']);