<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\UserRecord;
use Tracy\Debugger;

class UserController extends BaseController
{
    
    public function index(): void
    {
        $UserRecord = new UserRecord($this->db());
        $users = $UserRecord->findAll();
        $this->app->render('user/index.latte', [ 'page_title' => 'Liste des utilisateurs', 'users' => $users]);
    }
    
    /**
     * Edit
     *
     * @param string $is The ID of the user
     * @return void
     */
    public function edit(string $id): void
    {
        $UserRecord = new UserRecord($this->db());
        $user = $UserRecord->find($id);
        $this->app->render('user/edit.latte', [ 'page_title' => 'Modifier l\'utilisateur', 'user' => $user]);
    }
    
    /**
     * Update
     *
     * @param string $is The ID of the user
     * @return void
     */
    public function update(string $id): void
    {
        $userData = $this->app->request()->data;
        //Debugger::dump($userData);
        $UserRecord = new UserRecord($this->db());
        $UserRecord->find($id);
        $UserRecord->username = $userData->username;
        if (isset($userData->password, $userData->password_check) && ($userData->password == $userData->password_check)) {
            $UserRecord->password = password_hash($userData->password, PASSWORD_DEFAULT);
        }
        $UserRecord->save();
        $this->app->redirect('/user');
    }
    
    /**
     * Destroy
     *
     * @param string $is The ID of the user
     * @return void
     */
    public function destroy(string $id): void
    {
        $UserRecord = new UserRecord($this->app->db());
        $user = $UserRecord->find($id);
        $user->delete();
        $this->app->redirect('/user');
    }
    
    /**
     * Create
     *
     * @return void
     */
    public function create(): void
    {
        $this->app->render('user/create.latte', [ 'page_title' => 'Ajouter un utilisateur']);
    }
    
    /**
     * Store
     *
     * @return void
     */
    public function store(): void
    {
        $userData = $this->app->request()->data;
        $UserRecord = new UserRecord($this->db());
        $UserRecord->username = $userData->username;
        if (isset($userData->password, $userData->password_check) && ($userData->password == $userData->password_check)) {
            $UserRecord->password = password_hash($userData->password, PASSWORD_DEFAULT);
        } // Else do not modify password
        $UserRecord->save();
        $this->app->redirect('/user');
    }
}
