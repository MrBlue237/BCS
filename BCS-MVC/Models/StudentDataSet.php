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

    public function getSkillMatchCounts(int $studentId, int $placementId): array {

        // Calculate the matched count
        $matchedQuery = '
            SELECT 
                COUNT(ps.skill_code)
            FROM 
                placement_skills ps
            JOIN 
                student_skills ss ON ps.skill_code = ss.skill_code
            WHERE 
                ps.placement_ID = :pid
                AND ss.student_id = :sid
                AND ss.sfia_level >= ps.skill_level;
        ';

        // Calculate the total required skills
        $totalQuery = '
            SELECT 
                COUNT(skill_code)
            FROM 
                placement_skills
            WHERE 
                placement_ID = :pid;
        ';

        try {
            // Execute Matched Query
            $stmtMatched = $this->_dbHandle->prepare($matchedQuery);
            $stmtMatched->bindParam(':pid', $placementId, PDO::PARAM_INT);
            $stmtMatched->bindParam(':sid', $studentId, PDO::PARAM_INT);
            $stmtMatched->execute();
            $matchedCount = (int)$stmtMatched->fetchColumn();

            // Execute Total Query
            $stmtTotal = $this->_dbHandle->prepare($totalQuery);
            $stmtTotal->bindParam(':pid', $placementId, PDO::PARAM_INT);
            $stmtTotal->execute();
            $totalRequired = (int)$stmtTotal->fetchColumn();

            return [
                'matched_count' => $matchedCount,
                'total_required' => $totalRequired
            ];
        } catch (PDOException $e) {
            return [
                'matched_count' => 0,
                'total_required' => 0
            ];
        }
    }

    public function countMatchesForPlacement( $placementId)
    {
        // Get all students
        $studentsQuery = "SELECT user_id FROM users WHERE role = 'student'";
        $stmt = $this->_dbHandle->prepare($studentsQuery);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $matchCount = 0;

        foreach ($students as $sid) {
            $counts = $this->getSkillMatchCounts($sid, $placementId);

            if ($counts['total_required'] > 0) {
                $percentage = ($counts['matched_count'] / $counts['total_required']) * 100;
                if ($percentage >= 50) {
                    $matchCount++;
                }
            }
        }

        return $matchCount;
    }

    public function getMatchedStudentsForPlacement( $placementId)
    {
        $sql = "
        SELECT u.user_id, u.name, u.email, u.cv_file_path
        FROM matches_student_placement m
        JOIN users u ON m.user_id = u.user_id
        WHERE m.placement_id = :pid
    ";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(1, $placementId);
        $stmt->execute();

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($students as &$student) {
            $student['match_percentage'] =
                $this->getMatchPercentageForStudent($student['user_id'], $placementId);
        }
        return $students;

    }

    public function getMatchPercentageForStudent($studentId, $placementId)
    {
        $counts = $this->getSkillMatchCounts($studentId, $placementId);

        if ($counts['total_required'] == 0) {
            return 0;
        }

        return round(($counts['matched_count'] / $counts['total_required']) * 100);
    }

}