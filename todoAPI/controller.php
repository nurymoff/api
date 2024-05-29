<?php
// controller.php

// Importování potřebné třídy modelu
require_once 'model.php';

// Definice třídy Controller
class Controller {
    private $model;

    // Konstruktor třídy Controller
    public function __construct($host, $dbname, $username, $password) {
        // Vytvoření instance modelu pro práci s databází pomocí poskytnutých údajů
        $this->model = new DatabaseModel($host, $dbname, $username, $password);
    }

    // Metoda pro zpracování HTTP požadavků
    public function handleRequest() {
        // Získání HTTP metody a akce z URL parametrů
        $method = $_SERVER['REQUEST_METHOD'] ?? ''; // Získání HTTP metody (GET, POST, PUT, DELETE)
        $action = $_GET['action'] ?? null; // Získání akce z URL parametrů, pokud je nastavena
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null; // Získání autorizační hlavičky, pokud je nastavena

        // Zpracování HTTP požadavků
        switch ($method) {
            case 'GET':
                if ($action === 'getData') {
                    // Pokud je akce 'getData', zpracujeme získání dat
                    $auth = $this->model->getAuthData($authHeader); // Získání typu a dat autorizace
                    $data = $this->model->getDataBasedOnAuth($auth['type'], $auth['data']); // Získání dat na základě autorizace
                    echo json_encode($data, JSON_PRETTY_PRINT); // Odeslání dat ve formátu JSON
                } else {
                    $this->model->respond('error', 'Invalid request'); // Neplatný požadavek
                }
                break;

            case 'DELETE':
                if ($action === 'deleteTask' && isset($_GET['task_id'])) {
                    // Pokud je akce 'deleteTask' a je nastaven 'task_id', zpracujeme smazání úkolu
                    if ($this->model->isAdmin($authHeader)) {
                        // Ověření, zda je uživatel administrátor
                        $this->model->deleteTask($_GET['task_id']); // Smazání úkolu z databáze
                        $this->model->respond('success', 'Task deleted'); // Odeslání úspěšné odpovědi
                    } else {
                        $this->model->respond('error', 'Unauthorized'); // Neautorizovaný přístup
                    }
                } else {
                    $this->model->respond('error', 'Invalid request'); // Neplatný požadavek
                }
                break;

            case 'PUT':
                if ($action === 'updateTaskStatus' && isset($_GET['task_id']) && isset($_GET['status'])) {
                    // Pokud je akce 'updateTaskStatus' a jsou nastaveny 'task_id' a 'status', zpracujeme aktualizaci statusu úkolu
                    if ($this->model->isAdmin($authHeader)) {
                        // Ověření, zda je uživatel administrátor
                        $this->model->updateTaskStatus($_GET['task_id'], $_GET['status']); // Aktualizace statusu úkolu v databázi
                        $this->model->respond('success', 'Task status updated'); // Odeslání úspěšné odpovědi
                    } else {
                        $this->model->respond('error', 'Unauthorized'); // Neautorizovaný přístup
                    }
                } else {
                    $this->model->respond('error', 'Invalid request'); // Neplatný požadavek
                }
                break;

            default:
                $this->model->respond('error', 'Invalid request'); // Neplatný požadavek pro jiné HTTP metody
                break;
        }
    }
}

// Zpracování HTTP požadavků pomocí Controller třídy
$config = require_once 'config.php'; // Načtení konfiguračních údajů
$controller = new Controller($config['host'], $config['dbname'], $config['username'], $config['password']); // Vytvoření instance Controller třídy s použitím konfiguračních údajů
$controller->handleRequest(); // Zpracování HTTP požadavků