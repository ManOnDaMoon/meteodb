<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\UserRecord;

class InstallController extends BaseController
{
    public function index(): void
    {
        $UserRecord = new UserRecord($this->db());
        
        $UserRecord->find(1);
        
        if ($UserRecord->isHydrated()) {
            $result = "Installation déjà effectuée.";
        } else {
            $UserRecord->id = 1;
            $UserRecord->username = 'admin';
            $UserRecord->password = password_hash('password', PASSWORD_DEFAULT);
            $UserRecord->save();
            $result = "Installation terminée.";
        }
        
        $this->app->render('install.latte', ['result' => $result]);
    }
    
}
