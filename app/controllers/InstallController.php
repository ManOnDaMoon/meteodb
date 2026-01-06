<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\UserRecord;

class InstallController extends BaseController
{
    $sqlDataPointsTable = "
        CREATE TABLE IF NOT EXISTS `DataPoints` (
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
        CREATE TABLE IF NOT EXISTS `Stations` (
            `station_id` VARCHAR(30) NOT NULL PRIMARY KEY,
            `description` VARCHAR(256) ,
            `position` POINT ,
            `key` VARCHAR(60) NOT NULL,
            `last_update` DATETIME
        ) ENGINE = InnoDB;
        ";
    
    $sqlUsersTable = "
        CREATE TABLE IF NOT EXISTS `Users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(30) NOT NULL,
            `password` VARCHAR(60) NOT NULL,
            UNIQUE(`username`)
        ) ENGINE = InnoDB;
        ";
    
    public function index(): void
    {
        $UserRecord = new UserRecord($this->db());
        
        $UserRecord->find(1);
        
        if ($UserRecord->isHydrated()) {
            $result = "Installation déjà effectuée.";
        } else {
            try {
                $this->db()->query($sqlDataPointsTable);
                $this->db()->query($sqlStationsTable);
                $this->db()->query($sqlUsersTable);
            } catch (Exception $e) {
                $result = "Erreur lors de la création des tables : " . print_r($this->db()->errorInfo(),true);
            }
            
            $UserRecord->id = 1;
            $UserRecord->username = 'admin';
            $UserRecord->password = password_hash('password', PASSWORD_DEFAULT);
            $UserRecord->save();
            $result = "Installation terminée.";
        }
        
        $this->app->render('install.latte', ['result' => $result]);
    }
    
}
