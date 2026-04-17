<?php

namespace App\Controllers;

use Core\BaseController;

class IndexController extends BaseController
{
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
