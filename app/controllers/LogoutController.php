<?php

declare(strict_types=1);

namespace app\controllers;

class LogoutController extends BaseController
{
    /**
     * Index
     *
     * @return void
     */
    public function index(): void
    {
        $this->session()->destroy();
        $this->cookie()->set(
            'meteodb_remember',
            '',
            -3600,
            '/',
            $this->app->get('meteodb.domain'),
            false,
            true
            );
        $this->redirect($this->getUrl('home'));
    }
}
