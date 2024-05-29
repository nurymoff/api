<?php

/**
* Třída DatabaseModel slouží k práci s databází.
*/
class DatabaseModel {
    private $pdo;

    /**
     * Konstruktor třídy DatabaseModel.
     *
     * @param string $host     Adresa hostitele databáze.
     * @param string $dbname   Název databáze.
     * @param string $username Uživatelské jméno pro přihlášení do databáze.
     * @param string $password Heslo pro přihlášení do databáze.
     */
    public function __construct($host, $dbname, $username, $password) {
        // Vytvoření instance PDO pro připojení k databázi
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Nastavení režimu chybového hlášení na výjimky
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Nastavení kódování pro komunikaci s databází
        $this->pdo->exec("set names utf8");
    }

    /**
     * Metoda pro získání veřejných údajů z databáze.
     *
     * @return array Asociativní pole obsahující veřejné údaje úkolů.
     */
    public function getPublicData() {
        // Provedení dotazu na databázi pro veřejná data
        $stmt = $this->pdo->query('SELECT id, name, description, status FROM tasks ORDER BY id');
        // Získání výsledků jako asociativní pole
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    
    /**
     * Metoda pro získání rozšířených informací o datech z databáze.
     *
     * @return array Asociativní pole obsahující rozšířené údaje úkolů.
     */
    public function getDataWithExtendedInfo() {
        // Provedení dotazu na databázi pro rozšířená data
        $stmt = $this->pdo->query('SELECT t.id, t.name, t.description, t.status, t.created_at, t.updated_at, u.username FROM tasks t JOIN users u ON t.users_id = u.id ORDER BY t.id');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

     /**
     * Metoda pro získání citlivých údajů z databáze.
     *
     * @return array Asociativní pole obsahující citlivé údaje úkolů a uživatelů.
     */
    public function getDataWithSensitiveInfo() {
        // Provedení dotazu na databázi pro citlivá data
        $stmt = $this->pdo->query('SELECT t.*, u.username, u.password, u.email FROM tasks t JOIN users u ON t.users_id = u.id ORDER BY t.id');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Metoda pro smazání úkolu z databáze.
     *
     * @param int $taskId ID úkolu, který se má smazat.
     * @return void
     */
    public function deleteTask($taskId) {
        // Provedení dotazu na smazání úkolu
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$taskId]);
    }

    /**
     * Metoda pro aktualizaci statusu úkolu v databázi.
     *
     * @param int $taskId ID úkolu, kterému se má aktualizovat status.
     * @param string $status Nový status úkolu.
     * @return void
     */
    public function updateTaskStatus($taskId, $status) {
        // Provedení dotazu na aktualizaci statusu úkolu
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $taskId]);
    }

    /**
     * Funkce pro ověření API tokenu.
     *
     * @param string $token API token
     * @return bool True, pokud je token platný, jinak false
     */
    public function verifyApiToken($token) {
        return $token === 'valid_api_token';
    }

    /**
     * Funkce pro ověření uživatelského jména a hesla.
     *
     * @param string $username Uživatelské jméno
     * @param string $password Heslo
     * @return string|false Uživatelské jméno, pokud je ověření úspěšné, jinak false
     */
    public function verifyCredentials($username, $password) {
        $credentials = [
            'testuser' => 'testuser',
            'admin' => 'admin'
        ];
        return isset($credentials[$username]) && $credentials[$username] === $password ? $username : false;
    }

    /**
     * Funkce pro získání typu a dat autorizace.
     *
     * @param string|null $authHeader Autorizační hlavička
     * @return array Asociativní pole obsahující typ a data autorizace
     */
    public function getAuthData($authHeader) {
        if ($authHeader) {
            if (strpos($authHeader, 'Bearer ') === 0) {
                return ['type' => 'token', 'data' => substr($authHeader, 7)];
            } elseif (strpos($authHeader, 'Basic ') === 0) {
                return ['type' => 'basic', 'data' => base64_decode(substr($authHeader, 6))];
            }
        }
        return ['type' => 'public', 'data' => null];
    }

    /**
     * Funkce pro získání dat na základě autorizace.
     *
     * @param string $authType Typ autorizace (token, basic, public)
     * @param string|null $authData Data autorizace
     * @return array Data na základě autorizace
     */
    public function getDataBasedOnAuth($authType, $authData) {
        if ($authType === 'token' && $this->verifyApiToken($authData)) {
            return $this->getDataWithExtendedInfo();
        } elseif ($authType === 'basic') {
            list($username, $password) = explode(':', $authData, 2);
            $userRole = $this->verifyCredentials($username, $password);
            if ($userRole === 'testuser' || $userRole === 'admin') {
                return $this->getDataWithSensitiveInfo();
            }
        }
        return $this->getPublicData();
    }

    /**
     * Funkce pro ověření administrátorských oprávnění.
     *
     * @param string|null $authHeader Autorizační hlavička
     * @return bool True, pokud je uživatel administrátor, jinak false
     */
    public function isAdmin($authHeader) {
        $auth = $this->getAuthData($authHeader);
        if ($auth['type'] === 'basic') {
            list($username, $password) = explode(':', $auth['data'], 2);
            return $this->verifyCredentials($username, $password) === 'admin';
        }
        return false;
    }

    /**
     * Funkce pro zpracování odpovědí.
     *
     * @param string $status Status odpovědi (např. success, error)
     * @param string $message Zpráva odpovědi
     */
    public function respond($status, $message) {
        echo json_encode(['status' => $status, 'message' => $message], JSON_PRETTY_PRINT);
    }
}

