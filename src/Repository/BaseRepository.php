<?php

require_once (__DIR__.'/../Constants/AppConstants.php');

abstract class BaseRepository
{
    public $connectionManager;

    abstract public function insertData(array $data);

    public function __construct()
    {
        try {
            $this->connectionManager = new \PDO(sprintf("mysql:host=%s;dbname=%s;", AppConstants::DB_HOST, AppConstants::DB_NAME), AppConstants::DB_USERNAME, AppConstants::DB_PASSWORD);
            $this->connectionManager->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

}