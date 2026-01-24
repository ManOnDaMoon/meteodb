<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\UserRecord;
use app\records\AuthTokenRecord;

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
        $session = $this->session();
        $UserRecord = new UserRecord($this->app->db());
        $user = $UserRecord->eq('username', $postData->username)->find();
        if (!($user->isHydrated()
            && (password_verify($postData->password, $user->password) == 1))) {
            $session->setFlash('error_message', "Nom d'utilisateur ou mot de passe non valides.");
            $session->commit();
            $this->redirect($this->getUrl('login'));
            return;
        } 
        
        if ($postData->rememberme == 'on') {
            $selector = base64_encode(random_bytes(9));
            $authenticator = random_bytes(33);
            
            $cookie = $this->app->cookie();
            $cookie->set(
                'meteodb_remember',
                $selector.':'.base64_encode($authenticator),
                864000, // 10j
                '/',
                $this->app->get('meteodb.domain'),
                true, // HTTPS Only
                true
                );
            
            $AuthTokenRecord = new AuthTokenRecord($this->app->db());
            $AuthTokenRecord->selector = $selector;
            $AuthTokenRecord->token = hash('sha256', $authenticator);
            $AuthTokenRecord->userid = $UserRecord->id;
            $AuthTokenRecord->expires = date('Y-m-d\TH:i:s', time() + 864000);
            $AuthTokenRecord->save();
        }
        
        $session->set('user', $user->username);
        $session->set('user_id', $user->id);
        $session->commit();
        
        $this->redirect($this->getUrl('station'));
    }
}
