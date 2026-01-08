<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class PWAController extends BaseController
{
    /**
     * Progressive Web App manifest JSON file
     *
     * @return void
     */
    public function pwa(): void
    {        
        $manifest = [
            "name" => $this->app->get('pwa.app_name'),
            "short_name" => $this->app->get('pwa.app_short_name'),
            "display" => "standalone",
            "scope" => "/",
            "start_url" => "/"
        ];
        
        $this->app->json($manifest);
    }
}
