<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── PUBLIC ROUTES ────────────────────────────────────────────
// Landing page → Home controller → welcome_message view
$routes->get('/', 'Home::index');

// Login page (form + proses)
$routes->get('login',         'AuthController::index');
$routes->post('login/attempt','AuthController::authenticate');
$routes->get('logout',        'AuthController::logout');

$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // USER
    $routes->get('user', 'UserController::index');
    $routes->post('user/store', 'UserController::store');
    $routes->post('user/getData', 'UserController::getData');
    $routes->post('user/update', 'UserController::update');
    $routes->post('user/delete', 'UserController::delete');

    // PETA
    $routes->get('peta-persebaran',           'PetaPersebaranController::index');
    $routes->get('peta-persebaran/data',      'PetaPersebaranController::getData');
    $routes->get('peta-persebaran/statistik', 'PetaPersebaranController::getStatistik');
    $routes->get('peta-persebaran/golongan-aktif', 'PetaPersebaranController::getGolonganAktif');
    $routes->get('peta-persebaran/geojson',   'PetaPersebaranController::getGeoJson');

    // PENDONOR
    $routes->get('pendonor', 'PendonorController::index');
    $routes->post('pendonor/store', 'PendonorController::store');
    $routes->post('pendonor/getData', 'PendonorController::getData');
    $routes->post('pendonor/update', 'PendonorController::update');
    $routes->post('pendonor/delete', 'PendonorController::delete');
    $routes->post('pendonor/deleteAll', 'PendonorController::deleteAll');
    $routes->get('pendonor/search', 'PendonorController::search');
    $routes->get('pendonor/export', 'PendonorController::exportExcel');
    $routes->get('pendonor/template', 'PendonorController::downloadTemplate');
    $routes->post('pendonor/import', 'PendonorController::importExcel');

    // HISTORIS DONOR
    $routes->get('historis-donor', 'HistorisDonorController::index');
    $routes->post('historis-donor/store', 'HistorisDonorController::store');
    $routes->post('historis-donor/getData', 'HistorisDonorController::getData');
    $routes->post('historis-donor/update', 'HistorisDonorController::update');
    $routes->post('historis-donor/delete', 'HistorisDonorController::delete');
    $routes->post('historis-donor/deleteAll', 'HistorisDonorController::deleteAll');
    $routes->get('historis-donor/export', 'HistorisDonorController::exportExcel');
    $routes->get('historis-donor/template', 'HistorisDonorController::downloadTemplate');
    $routes->post('historis-donor/import', 'HistorisDonorController::importExcel');

    // ─── MODEL PREDIKSI ──────────────────────────────────────
    $routes->get('model-prediksi',                      'ModelPrediksiController::index');
    $routes->post('model-prediksi/store',               'ModelPrediksiController::store');
    $routes->post('model-prediksi/getData',             'ModelPrediksiController::getData');
    $routes->post('model-prediksi/update',              'ModelPrediksiController::update');
    $routes->post('model-prediksi/training',            'ModelPrediksiController::training');
    $routes->post('model-prediksi/aktifkan',            'ModelPrediksiController::aktifkan');
    $routes->post('model-prediksi/delete',              'ModelPrediksiController::delete');
    $routes->post('model-prediksi/cekStatusPython',     'ModelPrediksiController::cekStatusPython');
    $routes->post('model-prediksi/trainingProgress', 'ModelPrediksiController::trainingProgress');

    // ─── PREDIKSI PENDONOR POTENSIAL ─────────────────────────
    $routes->group('', ['filter' => 'roleUdd'], function ($routes) {
        $routes->get('prediksi',                   'PrediksiController::index');
        $routes->post('prediksi/jalankan',         'PrediksiController::jalankan');
        $routes->get('prediksi/histori',           'PrediksiController::histori');
        $routes->get('prediksi/detail/(:segment)', 'PrediksiController::detail/$1');
        $routes->post('prediksi/hapusHistori',     'PrediksiController::hapusHistori');
        $routes->get('prediksi/export/(:segment)', 'PrediksiController::exportHasil/$1');
    });
});
