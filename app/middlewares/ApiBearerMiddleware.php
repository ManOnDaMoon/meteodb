<?php

declare(strict_types=1);

namespace app\middlewares;

use app\records\ApiTokenRecord;
use flight\Engine;

class ApiBearerMiddleware
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(): void
    {
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($authorization === '' && method_exists($this->app->request(), 'getHeader')) {
            $authorization = (string) $this->app->request()->getHeader('Authorization');
        }

        if (!preg_match('/^Bearer ([A-Za-z0-9_-]+)$/', trim($authorization), $matches)) {
            $this->reject();
        }

        $record = new ApiTokenRecord($this->app->db());
        $record->eq('token_hash', hash('sha256', $matches[1]))->find();
        if (!$record->isHydrated()) {
            $this->reject();
        }

        $this->app->set('api.user_id', (int) $record->userid);
    }

    private function reject(): void
    {
        $this->app->json(['error' => ['code' => 'unauthorized', 'message' => 'A valid bearer token is required.']], 401);
        exit;
    }
}
