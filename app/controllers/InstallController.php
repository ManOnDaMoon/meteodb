<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\UserRecord;
use Latte\Bridges\Tracy\TracyExtension;

class InstallController extends BaseController
{
       
    public function index(): void
    {
        $sqlDataPointsTable = "
        CREATE TABLE IF NOT EXISTS `datapoints` (
        	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        	`station_id` VARCHAR(30) NOT NULL ,
        	`action` VARCHAR(30) ,
        	`realtime` INT ,
        	`rtfreq` INT ,
        	`dateutc` DATETIME NOT NULL ,
        	`baromin` FLOAT ,
        	`tempf` FLOAT  ,
        	`dewptf` FLOAT ,
        	`humidity` INT ,
        	`windspeedmph` FLOAT,
        	`windgustmph` FLOAT,
        	`winddir` INT ,
        	`rainin` FLOAT ,
        	`dailyrainin` FLOAT,
        	`solarradiation` FLOAT,
        	`UV` FLOAT ,
        	`indoortempf` FLOAT ,
        	`indoorhumidity` INT ,
            INDEX (`station_id`, `dateutc`)
        ) ENGINE = InnoDB;";
        
        $sqlStationsTable = "
        CREATE TABLE IF NOT EXISTS `stations` (
            `station_id` VARCHAR(30) NOT NULL PRIMARY KEY,
            `description` VARCHAR(256) ,
            `position` POINT ,
            `key` VARCHAR(60) NOT NULL,
            `last_update` DATETIME,
            `last_datapoint` INT UNSIGNED NOT NULL
        ) ENGINE = InnoDB;
        ";
        
        $sqlUsersTable = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(30) NOT NULL,
            `password` VARCHAR(60) NOT NULL,
            UNIQUE(`username`)
        ) ENGINE = InnoDB;
        ";
        
        $sqlAuthTokens = "
        CREATE TABLE IF NOT EXISTS `auth_tokens` (
            `id` int(11) UNSIGNED not null AUTO_INCREMENT,
            `selector` char(12),
            `token` char(64),
            `userid` int(11) UNSIGNED not null,
            `expires` datetime,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB;
        ";
        
        $sqlTestInstall = "SELECT * FROM users";
        $notInstalled = false;
        try {
            $result = $this->db()->query($sqlTestInstall);
        } catch (\Exception $e)  {
            $notInstalled = ($this->db()->errorCode() == '42S02');
        }
        
        $this->db()->query($sqlDataPointsTable);
        $this->db()->query($sqlStationsTable);
        $this->db()->query($sqlUsersTable);
        $this->db()->query($sqlAuthTokens);
        
        if ($notInstalled) {
            $UserRecord = new UserRecord($this->db());
            $UserRecord->id = 1;
            $UserRecord->username = 'admin';
            $UserRecord->password = password_hash('password', PASSWORD_DEFAULT);
            $UserRecord->save();
            $result = "Installation effectuée.";
        } else {
            $result = "Installation mise à jour.";
        }
        
        $this->app->render('install.latte', ['result' => $result]);
    }
    
}
