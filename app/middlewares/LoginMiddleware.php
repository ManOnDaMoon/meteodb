<?php

declare(strict_types=1);

namespace app\middlewares;

use app\records\AuthTokenRecord;
use flight\Engine;
use Ghostff\Session\Session;

class LoginMiddleware {

    /** @var Engine */
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(): void
    {   
        $cookie = $this->app->cookie();
        /** @var Session $session */
        $session = $this->app->session();
        $remember = $cookie->get('meteodb_remember');
        
        
        if (($session->exist('user') === false)
            && ($remember !== null)) {

            // Session does not exist but cookie detected
            
            list($selector, $authenticator) = explode(':', $remember);
            $AuthTokenRecord = new AuthTokenRecord($this->app->db());
            $AuthTokenRecord->with('user')->eq('selector', $selector)->find();
            
            // Check AuthTokenExpiry
            //$AuthTokenRecord->expires = date('Y-m-d\TH:i:s', time() + 864000);

            
            if (hash_equals($AuthTokenRecord->token, hash('sha256', base64_decode($authenticator)))) {
                
                // Session retrieved. Regenerate.
                $session->set('user', $AuthTokenRecord->user->username);
                $session->set('user_id', $AuthTokenRecord->id);
                $session->commit();
                
                // Delete expired auth_tokens
                
                
                // Then regenerate login token as above
                $selector = base64_encode(random_bytes(9));
                $authenticator = random_bytes(33);
                $cookie->set(
                    'meteodb_remember',
                    $selector.':'.base64_encode($authenticator),
                    864000, // 10j
                    '/',
                    $this->app->get('meteodb.domain'),
                    false,
                    true
                    );
                
                $AuthTokenRecord->selector = $selector;
                $AuthTokenRecord->token = hash('sha256', $authenticator);
                $AuthTokenRecord->save();
            }
       }
       
       // Check session again. Redirect only if not on home page
       if ($session->exist('user') === false
           && $this->app->router()->executedRoute->alias != 'home') {
           $this->app->redirect($this->app->getUrl('login'));
       }
           
            
    }
}