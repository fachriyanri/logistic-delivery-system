<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Authentication Routes
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::authenticate');
$routes->get('/logout', 'AuthController::logout');

// Profile and Password Routes (Protected)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile', 'ProfileController::update');
    $routes->get('change-password', 'PasswordController::index');
    $routes->post('change-password', 'PasswordController::update');
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings', 'SettingsController::update');
    $routes->get('laporan', 'LaporanController::index');
});

// Dashboard Routes (Protected)
$routes->group('dashboard', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
});

// Master Data Routes (Protected)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Kategori Routes
    $routes->get('kategori', 'KategoriController::index');
    $routes->get('kategori/manage', 'KategoriController::manage');
    $routes->get('kategori/manage/(:segment)', 'KategoriController::manage/$1');
    $routes->post('kategori/save', 'KategoriController::save');
    $routes->get('kategori/delete/(:segment)', 'KategoriController::delete/$1');
    $routes->post('kategori/generate-code', 'KategoriController::generateCode');
    $routes->post('kategori/check-name', 'KategoriController::checkName');
    $routes->get('kategori/get-for-select', 'KategoriController::getForSelect');

    // Barang Routes
    $routes->get('barang', 'BarangController::index');
    $routes->get('barang/manage', 'BarangController::manage');
    $routes->get('barang/manage/(:segment)', 'BarangController::manage/$1');
    $routes->post('barang/save', 'BarangController::save');
    $routes->get('barang/delete/(:segment)', 'BarangController::delete/$1');
    $routes->post('barang/generate-code', 'BarangController::generateCode');
    $routes->post('barang/check-name', 'BarangController::checkName');
    $routes->get('barang/get-for-select', 'BarangController::getForSelect');
    $routes->get('barang/search', 'BarangController::search');
    $routes->get('barang/get-by-category', 'BarangController::getByCategory');

    // Kurir Routes
    $routes->get('kurir', 'KurirController::index');
    $routes->get('kurir/manage', 'KurirController::manage');
    $routes->get('kurir/manage/(:segment)', 'KurirController::manage/$1');
    $routes->post('kurir/save', 'KurirController::save');
    $routes->get('kurir/delete/(:segment)', 'KurirController::delete/$1');
    $routes->post('kurir/generate-code', 'KurirController::generateCode');
    $routes->post('kurir/check-phone', 'KurirController::checkPhone');
    $routes->get('kurir/get-for-select', 'KurirController::getForSelect');
    $routes->get('kurir/search', 'KurirController::search');
    $routes->post('kurir/update-password', 'KurirController::updatePassword');
    $routes->get('kurir/statistics', 'KurirController::statistics');

    // Pelanggan Routes
    $routes->get('pelanggan', 'PelangganController::index');
    $routes->get('pelanggan/manage', 'PelangganController::manage');
    $routes->get('pelanggan/manage/(:segment)', 'PelangganController::manage/$1');
    $routes->post('pelanggan/save', 'PelangganController::save');
    $routes->get('pelanggan/delete/(:segment)', 'PelangganController::delete/$1');
    $routes->post('pelanggan/generate-code', 'PelangganController::generateCode');
    $routes->post('pelanggan/check-name', 'PelangganController::checkName');
    $routes->post('pelanggan/check-phone', 'PelangganController::checkPhone');
    $routes->get('pelanggan/get-for-select', 'PelangganController::getForSelect');
    $routes->get('pelanggan/search', 'PelangganController::search');
    $routes->get('pelanggan/get-contact', 'PelangganController::getContact');
    $routes->get('pelanggan/statistics', 'PelangganController::statistics');
    $routes->get('pelanggan/get-by-type', 'PelangganController::getByType');
    $routes->post('pelanggan/validate-phone', 'PelangganController::validatePhone');

    // User Routes (Admin only)
    $routes->get('user', 'UserController::index', ['filter' => 'role:1']);
    $routes->get('user/create', 'UserController::create', ['filter' => 'role:1']);
    $routes->post('user', 'UserController::store', ['filter' => 'role:1']);
    $routes->get('user/(:segment)/edit', 'UserController::edit/$1', ['filter' => 'role:1']);
    $routes->put('user/(:segment)', 'UserController::update/$1', ['filter' => 'role:1']);
    $routes->delete('user/(:segment)', 'UserController::delete/$1', ['filter' => 'role:1']);
});

// Transaction Routes (Protected)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Pengiriman Routes
    $routes->get('pengiriman', 'PengirimanController::index');
    $routes->get('pengiriman/create', 'PengirimanController::create');
    $routes->post('pengiriman/store', 'PengirimanController::store');
    $routes->get('pengiriman/detail/(:segment)', 'PengirimanController::detail/$1');
    $routes->get('pengiriman/edit/(:segment)', 'PengirimanController::edit/$1');
    $routes->put('pengiriman/update/(:segment)', 'PengirimanController::update/$1');
    $routes->delete('pengiriman/delete/(:segment)', 'PengirimanController::delete/$1');
    $routes->get('pengiriman/delivery-note/(:segment)', 'PengirimanController::deliveryNote/$1');
    $routes->post('pengiriman/update-status/(:segment)', 'PengirimanController::updateStatus/$1');
    $routes->get('pengiriman/track/(:segment)', 'PengirimanController::track/$1');
    $routes->get('pengiriman/duplicate/(:segment)', 'PengirimanController::duplicate/$1');
    $routes->get('pengiriman/export', 'PengirimanController::export');
    $routes->post('pengiriman/generate-code', 'PengirimanController::generateCode');
    $routes->get('pengiriman/get-details', 'PengirimanController::getDetails');
    $routes->get('pengiriman/statistics', 'PengirimanController::statistics');
    
    // Legacy route support
    $routes->get('pengiriman/manage', 'PengirimanController::create');
    $routes->get('pengiriman/manage/(:segment)', 'PengirimanController::edit/$1');
    $routes->post('pengiriman/save', 'PengirimanController::store');
    $routes->get('pengiriman/surat-jalan/(:segment)', 'PengirimanController::deliveryNote/$1');
});

// Public Pengiriman Routes
$routes->group('pengiriman', static function ($routes) {
    $routes->get('qr/(:segment)', 'PengirimanController::generateQR/$1');
});

// Report Routes (Protected)
$routes->group('laporan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('pengiriman', 'LaporanController::pengiriman');
    $routes->post('laporan/export-excel', 'LaporanController::exportExcel');
    $routes->post('laporan/export-pdf', 'LaporanController::exportPdf');
});

// Settings Routes (Protected)
$routes->group('settings', ['filter' => 'auth'], static function ($routes) {
    $routes->get('password', 'PasswordController::index');
    $routes->post('password', 'PasswordController::update');
});

// API Routes
$routes->group('api', static function ($routes) {
    // QR Code API
    $routes->post('qr/validate', 'Api\QRController::validate');
    $routes->post('qr/generate', 'Api\QRController::generate', ['filter' => 'auth']);
    $routes->get('qr/scanner-config', 'Api\QRController::scannerConfig');
    $routes->get('qr/track/(:segment)', 'Api\QRController::track/$1');
    $routes->get('qr/cleanup', 'Api\QRController::cleanup', ['filter' => 'auth']);
});

// Mobile Routes
$routes->group('mobile', static function ($routes) {
    $routes->get('track/(:segment)', 'Api\QRController::mobileTrack/$1');
});

// Public tracking route
$routes->get('track/(:segment)', 'Api\QRController::mobileTrack/$1');

// Admin Routes (Protected)
$routes->group('admin', ['filter' => 'role:1'], static function ($routes) {
    // Data Migration Routes
    $routes->get('data-migration', 'Admin\DataMigrationController::index');
    $routes->post('data-migration/migrate-from-backup', 'Admin\DataMigrationController::migrateFromBackup');
    $routes->post('data-migration/migrate-from-old-database', 'Admin\DataMigrationController::migrateFromOldDatabase');
    $routes->post('data-migration/import-sql-dump', 'Admin\DataMigrationController::importSQLDump');
    $routes->post('data-migration/verify-integrity', 'Admin\DataMigrationController::verifyIntegrity');
    $routes->post('data-migration/get-data-status', 'Admin\DataMigrationController::getDataStatus');
    $routes->post('data-migration/validate-data', 'Admin\DataMigrationController::validateData');
    $routes->post('data-migration/cleanup-data', 'Admin\DataMigrationController::cleanupData');
    $routes->post('data-migration/generate-quality-report', 'Admin\DataMigrationController::generateQualityReport');
    $routes->post('data-migration/update-user-credentials', 'Admin\DataMigrationController::updateUserCredentials');
    $routes->post('data-migration/create-default-users', 'Admin\DataMigrationController::createDefaultUsers');
    $routes->post('data-migration/validate-user-credentials', 'Admin\DataMigrationController::validateUserCredentials');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}