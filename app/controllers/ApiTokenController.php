<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\ApiTokenRecord;

class ApiTokenController extends BaseController
{
    public function index(): void
    {
        $userId = (int) $this->session()->get('user_id');
        $record = new ApiTokenRecord($this->db());
        $tokens = $record->eq('userid', $userId)->order('created_at DESC')->findAll();

        $this->app->render('account/api_tokens.latte', [
            'page_title' => 'Jetons API',
            'tokens' => $tokens,
            'new_token' => null,
        ]);
    }

    public function create(): void
    {
        $userId = (int) $this->session()->get('user_id');
        $name = trim((string) $this->request()->data->name);
        if ($name === '') {
            $name = 'KoboWeather';
        }
        $name = function_exists('mb_substr') ? mb_substr($name, 0, 80) : substr($name, 0, 80);

        $token = 'mdb_' . bin2hex(random_bytes(32));
        $record = new ApiTokenRecord($this->db());
        $record->userid = $userId;
        $record->name = $name;
        $record->token_hash = hash('sha256', $token);
        $record->created_at = gmdate('Y-m-d H:i:s');
        $record->save();

        $tokens = (new ApiTokenRecord($this->db()))
            ->eq('userid', $userId)
            ->order('created_at DESC')
            ->findAll();
        $this->app->render('account/api_tokens.latte', [
            'page_title' => 'Jetons API',
            'tokens' => $tokens,
            'new_token' => $token,
        ]);
    }

    public function revoke(string $id): void
    {
        $userId = (int) $this->session()->get('user_id');
        $record = new ApiTokenRecord($this->db());
        $token = $record->eq('id', (int) $id)->eq('userid', $userId)->find();
        if ($token->isHydrated()) {
            $token->delete();
        }
        $this->app->redirect('/account/api-tokens');
    }
}
