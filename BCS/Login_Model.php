<?php
require_once("Database.php");
require_once("qe.php");

class Login_Model
{
    protected $_dbHandle, $_dbInstance;
    private $logged_in = false;
    private $username;
    private $password;
    private $user_id;

    public function __construct()
    {
        try {
            $this->_dbInstance = Database::getInstance();
            $this->_dbHandle = $this->_dbInstance->getdbConnection();
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public function login_2($username, $password)
    {
        $this->username = trim($username);
        $this->password = trim($password);

        $stmt = $this->_dbHandle->prepare("SELECT * FROM users WHERE username = :username LIMIT 1;");
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $dataSet = [];

        if ($row && password_verify($this->password, $row['password_hash'])) {
            $dataSet[] = new qe($row);
            $this->logged_in = true;
            $this->user_id = $row['id'];
        } else {
            $this->logged_in = false;
        }

        return $dataSet;
    }

    public function getLoggedInUser()
    {
        return $this->logged_in;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
?>
