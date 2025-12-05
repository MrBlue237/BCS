<?php
class UsersData {

    protected $_user_id,$_role, $_email, $_password_hash, $_name, $_phone_number, $_postal_address, $_cv_file_path;

    public function __construct($dbRow) {
        $this->_user_id = $dbRow['user_id'];
        $this->_role = $dbRow['role'];
        $this->_email = $dbRow['email'];
        $this->_password_hash = $dbRow['password_hash'];
        $this->_name = $dbRow['name'];
        $this->_phone_number = $dbRow['phone_number'];
        $this->_postal_address = $dbRow['postal_address'];
        $this->_cv_file_path = $dbRow['cv_file_path'];
    }

    /**
     * @return int The unique ID of the user.
     */
    public function getUserID() {
        return $this->_user_id;
    }

    /**
     * @return string The user's role.
     */
    public function getRole() {
        return $this->_role;
    }

    /**
     * @return string The user's email address.
     */
    public function getEmail() {
        return $this->_email;
    }

    /**
     * @return string The HASHED password string.
     */
    public function getPasswordHash() {
        return $this->_password_hash;
    }

    /**
     * @return string The user's name.
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return string The user's phone number.
     */
    public function getPhoneNumber() {
        return $this->_phone_number;
    }

    /**
     * @return string The user's postal address.
     */
    public function getPostalAddress() {
        return $this->_postal_address;
    }

    /**
     * @return string The user's cv file path.
     */
    public function getCvFilePath() {
        return $this->_cv_file_path;
    }

}