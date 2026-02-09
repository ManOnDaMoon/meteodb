<?php

declare(strict_types=1);

namespace app\middlewares;

use app\records\AuthTokenRecord;
use flight\Engine;
use Ghostff\Session\Session;
use Overclokk\Cookie\Cookie;

class LoginMiddleware {

    /** @var Engine */
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(): void
    {   
        /** @var Cookie $cookie */
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

            
            if ($AuthTokenRecord->isHydrated()
                && hash_equals($AuthTokenRecord->token, hash('sha256', base64_decode($authenticator)))) {
                
                // Session retrieved. Regenerate.
                $session->set('user', $AuthTokenRecord->user->username);
                $session->set('user_id', $AuthTokenRecord->user->id);
                $session->commit();
                
                // Then regenerate new selector and update existing token
                $selector = base64_encode(random_bytes(9));
                $authenticator = random_bytes(33);
                $cookie->set(
                    'meteodb_remember',
                    $selector.':'.base64_encode($authenticator),
                    864000, // 10j
                    '/', // Path
                    '', // Domain
                    false, // Secure
                    true // HTTP-Only
                    );
                
                $AuthTokenRecord->selector = $selector;
                $AuthTokenRecord->token = hash('sha256', $authenticator);
                $AuthTokenRecord->save(); // Update existing
                
                // Take some time to delete old expired tokens
                $OldAuthTokens = new AuthTokenRecord($this->app->db());
                $OldTokens = $OldAuthTokens
                    ->lt('expires', date('Y-m-d H:i:s', time()))
                    ->findAll();
                foreach ($OldTokens as $OldToken) {
                    $OldToken->delete();
                }
            }
       }
       
       // Check session again. Redirect only if not on home page
       if ($session->exist('user') === false
           && $this->app->router()->executedRoute->alias != 'home') {
           $this->app->redirect($this->app->getUrl('login'), 302);
       }
           
            
    }
}