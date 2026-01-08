<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\UserRecord;
use Tracy\Debugger;
use Ghostff;
use Ghostff\Session\Session;

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
            if ($postData->rememberme == 'on') {
                $session = new Session([
                    Session::CONFIG_START_OPTIONS => [
                        'cookie_lifetime' => 30 * 86400 // Does not work??
                    ]
                ]);
            } else {
                $session = $this->session();
            }
            $session->set('user', $user->username);
            $session->set('user_id', $user->id);
            $session->commit();

            $this->redirect($this->getUrl('station'));
        } else {
            $this->redirect($this->getUrl('login'));
        }
    }
}
