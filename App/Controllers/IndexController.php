<?php

namespace App\Controllers;

use Core\BaseController;

class IndexController extends BaseController
{
    /**
     * Home / Project overview and health
     */
    public function index()
    {
        $this->view('health')->send();
    }

    /**
     * Site health check
     */
    public function health()
    {
        $this->json([
            'success' => true,
            'message' => 'Site is healthy!',
        ])->send();
    }
}
