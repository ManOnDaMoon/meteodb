<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\UserRecord;

class LoginController extends BaseController
{
    /**
     * Index
     *
     * @return void
     */
    public function index(): void
    {
        $this->render('login/index.latte', [ 'page_title' => 'Authentification' ]);
    }
    
    /**
     * Authenticate
     *
     * @return void
     */
    public function authenticate(): void
    {
        $postData = $this->request()->data;
        $UserRecord = new UserRecord($this->app->db());
        $user = $UserRecord->eq('username', $postData->username)->find();
        if (($user->isHydrated()) && (password_verify($postData->password, $user->password) == 1)) {
            $this->session()->set('user', $user->username);
            $this->session()->set('user_id', $user->id);
            $this->session()->commit();
            $this->redirect($this->getUrl('station'));
        } else {
            $this->redirect($this->getUrl('login'));
        }
    }
}
