<?php
class StudentData {

    protected $_skill_code, $_skill_name, $_sfia_level;

    public function __construct($dbRow) {
        $this->_skill_code = $dbRow['skill_code'];
        $this->_skill_name = $dbRow['skill_name'];
        $this->_sfia_level = $dbRow['sfia_level'];
    }

    public function getSkillCode() {
        return $this->_skill_code;
    }

    public function getSkillName() {
        return $this->_skill_name;
    }

    public function getSfiaLevel() {
        return $this->_sfia_level;
    }
}