<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================
// HALAMAN AWAL & LOGIN
// =====================
$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('login/attempt', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// =====================
// PROTECTED ROUTES
// =====================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // DASHBOARD
    $routes->get('dashboard', 'DashboardController::index');

    // =====================
    // USER MANAGEMENT
    // =====================
    $routes->get('user', 'UserController::index');
    $routes->post('user/store', 'UserController::store');
    $routes->post('user/getData', 'UserController::getData');
    $routes->post('user/update', 'UserController::update');
    $routes->post('user/delete', 'UserController::delete');

    // Pendonor CRUD
    $routes->get('pendonor', 'PendonorController::index');
    $routes->post('pendonor/store', 'PendonorController::store');
    $routes->post('pendonor/getData', 'PendonorController::getData');
    $routes->post('pendonor/update', 'PendonorController::update');
    $routes->post('pendonor/delete', 'PendonorController::delete');
    $routes->get('pendonor/search', 'PendonorController::search');
    $routes->get('pendonor/export', 'PendonorController::exportExcel');
    $routes->get('pendonor/template', 'PendonorController::downloadTemplate');
    $routes->post('pendonor/import', 'PendonorController::importExcel');

    // ================= HISTORIS DONOR =================
    $routes->get('historis-donor', 'HistorisDonorController::index');
    $routes->post('historis-donor/store', 'HistorisDonorController::store');
    $routes->post('historis-donor/getData', 'HistorisDonorController::getData');
    $routes->post('historis-donor/update', 'HistorisDonorController::update');
    $routes->post('historis-donor/delete', 'HistorisDonorController::delete');
    $routes->get('historis-donor/export', 'HistorisDonorController::exportExcel');
    $routes->get('historis-donor/template', 'HistorisDonorController::downloadTemplate');
    $routes->post('historis-donor/import', 'HistorisDonorController::importExcel');

});

