<?php

require_once('Database.php');
require_once('StudentData.php');

class StudentDataSet {
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllSkillsWithStudentStatus($studentId) {
        $sqlQuery = '
            SELECT 
                s.skill_code,
                s.skill_name,
                ss.sfia_level 
            FROM 
                SFIA_skills s 
            LEFT JOIN 
                student_skills ss ON s.skill_code = ss.skill_code AND ss.student_id = :studentId;
            ORDER BY s.skill_name;
        ';

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $statement->execute();

        $dataSet = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $dataSet[] = new StudentData($row);
        }
        return $dataSet;
    }

    public function updateStudentSkills($studentId, $skills) {
        $this->_dbHandle->beginTransaction();

        try {
            $deleteQuery = 'DELETE FROM student_skills WHERE student_id = :studentId;';
            $deleteStmt = $this->_dbHandle->prepare($deleteQuery);
            $deleteStmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
            $deleteStmt->execute();

            $insertQuery = 'INSERT INTO student_skills (student_id, skill_code, sfia_level) VALUES (:sid, :code, :level);';
            $insertStmt = $this->_dbHandle->prepare($insertQuery);

            foreach ($skills as $code => $level) {
                $level = intval($level);
                if ($level >= 1 && $level <= 7) {
                    $insertStmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
                    $insertStmt->bindParam(':code', $code);
                    $insertStmt->bindParam(':level', $level, PDO::PARAM_INT);
                    $insertStmt->execute();
                }
            }

            $this->_dbHandle->commit();
            return true;

        } catch (Exception $e) {
            $this->_dbHandle->rollBack();
            return false;
        }
    }
}