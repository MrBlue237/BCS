<?php

require_once('Database.php');
require_once ('UsersData.php');

class UsersDataSet {
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllUsers()
    {
        $sqlQuery = 'SELECT * FROM users;';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new UsersData($row);
        }
        return $dataSet;
    }

    public function fetchUserByID($userId) {
        $sqlQuery = 'SELECT * FROM users WHERE user_id = ?;';

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(1, $userId);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new UsersData($row);
        }

        return false;
    }

    public function findUserAndVerifyPassword($email, $password) {
        $sqlQuery = 'SELECT * FROM users WHERE email = :email;';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':email', $email);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $userData = new UsersData($row);
            if (password_verify($password, $userData->getPasswordHash())) {
                return $userData;
            }
        }
        return false;
    }

    public function checkIfEmailExists($email) {
        $sqlQuery = 'SELECT COUNT(user_id) FROM users WHERE email = :email;';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':email', $email);
        $statement->execute();
        return (bool)$statement->fetchColumn();
    }

    public function registerUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sqlQuery = 'INSERT INTO users (role, email, password_hash, name, phone_number, postal_address, cv_file_path) 
                     VALUES (:role, :email, :pass, :name, :phone, :address, :cv_path);';

        $statement = $this->_dbHandle->prepare($sqlQuery);

        $statement->bindParam(':role', $data['role']);
        $statement->bindParam(':email', $data['email']);
        $statement->bindParam(':pass', $hashedPassword);
        $statement->bindParam(':name', $data['name']);
        $statement->bindParam(':phone', $data['phone_number']);
        $statement->bindParam(':address', $data['postal_address']);

        $cvPath = $data['cv_file_path'];
        if (is_null($cvPath) || empty($cvPath)) {
            $statement->bindValue(':cv_path', null, PDO::PARAM_NULL);
        } else {
            $statement->bindValue(':cv_path', $cvPath, PDO::PARAM_STR);
        }
        // ---------------------------------

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateUsersData($name, $number, $address, $cv, $email, $user_id) {
        $sqlQuery = 'UPDATE users 
                SET name = ?, phone_number = ?, postal_address = ?, cv_file_path = ?, email = ?
                WHERE user_id = ?';

        $statement = $this->_dbHandle->prepare($sqlQuery);

        $statement->bindParam(1, $name);
        $statement->bindParam(2, $number);
        $statement->bindParam(3, $address);
        $statement->bindParam(4, $cv);
        $statement->bindParam(5, $email);
        $statement->bindParam(6, $user_id);




        $statement->execute();
    }
}