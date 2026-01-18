<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    /**
     * Halaman Dashboard
     */
    public function index()
    {
        $data = [
            'title'    => 'Dashboard',
            'username' => session()->get('username'),
            'role'     => session()->get('role')
        ];

        return view('pages/dashboard/index',  [
        'title' => 'Dashboard',
        'loadDemoJs' => true,
        $data
        ]);

    }
}
