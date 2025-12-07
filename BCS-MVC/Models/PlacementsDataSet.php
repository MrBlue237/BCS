<?php

require_once('Database.php');
require_once('PlacementsData.php');

class PlacementsDataSet {
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    /**
     * Fetches all placement records belonging to a specific employer (My Posts).
     * @param int $employerId The ID of the logged-in employer.
     * @return array An array of PlacementsData objects.
     */
    public function fetchPostsByEmployerID($employerId) {
        $sqlQuery = 'SELECT * FROM placement_opportunity WHERE employer_id = :employerId;';

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':employerId', $employerId, PDO::PARAM_INT); // Bind the employer ID
        $statement->execute();

        $dataSet = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $dataSet[] = new PlacementsData($row);
        }
        return $dataSet;
    }

    /**
     * Fetches all pet records from the database, ordered by the date reported (newest first).
     *
     * @return array An array of PetsData objects.
     */
    public function fetchAllPosts() {
        $sqlQuery = 'SELECT * FROM placement_opportunity;';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        // loop through and read the results of the query and cast
        // them into a matching object
        while ($row = $statement->fetch()) {
            $dataSet[] = new PlacementsData($row);
        }
        return $dataSet;
    }
    /**
     * Inserts a new pet record into the database, setting the initial status to 'lost'.
     *
     * @param string $name The name of the pet.
     * @param string $species The species (e.g., 'dog', 'cat').
     * @param string $breed The pet's breed.
     * @param string $color The pet's color.
     * @param string $image_url The filename of the pet's photo.
     * @param string $description The pet's description.
     * @param string $date_reported The date the pet was reported (Y-m-d format).
     * @param int $user_id The ID of the user who reported the pet.
     * @return void
     */
    public function insertPlacement($employer_id, $title, $description,$salary, $location, $start_date, $end_date, $deadline, $date_posted) {

        // SQL query updated to include 'deadline' and 'date_posted'
        $sqlQuery = 'INSERT into placement_opportunity(employer_id, title, description, salary, location, start_date, end_date, deadline, date_posted, status) VALUES(?,?,?,?,?,?,?,?,?,?);';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement

        $status = 'Active'; // Hardcode default status for new posts

        $statement->bindParam(1, $employer_id);
        $statement->bindParam(2, $title);
        $statement->bindParam(3, $description);
        $statement->bindParam(4, $salary);
        $statement->bindParam(5, $location);
        $statement->bindParam(6, $start_date);
        $statement->bindParam(7, $end_date);
        $statement->bindParam(8, $deadline);
        $statement->bindParam(9, $date_posted);
        $statement->bindParam(10, $status);

        if ($statement->execute()) {
            return $this->_dbHandle->lastInsertId();
        }
        return false;
    }

    public function insertRequiredSkills($placementId, $skills) {
        // Corrected SQL query uses the correct table and column names
        $insertQuery = 'INSERT INTO placement_skills (placement_ID, skill_code, skill_level) VALUES (:pid, :code, :level);';
        $insertStmt = $this->_dbHandle->prepare($insertQuery);

        $success = true;
        foreach ($skills as $code => $level) {
            $level = intval($level);
            if ($level >= 1 && $level <= 7) {
                // Ensure the parameter name matches the SQL query (:pid, :code, :level)
                $insertStmt->bindParam(':pid', $placementId, PDO::PARAM_INT);
                $insertStmt->bindParam(':code', $code);
                $insertStmt->bindParam(':level', $level, PDO::PARAM_INT);
                if (!$insertStmt->execute()) {
                    $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * Fetches all available SFIA skills for display in the form.
     */
    public function fetchAvailableSkills() {
        $sqlQuery = 'SELECT skill_code, skill_name FROM SFIA_skills ORDER BY skill_name;';

        try {
            $statement = $this->_dbHandle->prepare($sqlQuery);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return empty array to prevent 500 error if DB is empty/misconfigured
            return [];
        }
    }

    /**
     * Deletes a pet record from the database based on its ID.
     *
     * @param int $pet_id The ID of the pet to delete.
     * @return void
     */
    // Function to delete a pet by ID
    public function deletePlacement($placement_id) {
        $sqlQuery = 'DELETE FROM placement_opportunity WHERE placement_id = ?';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        // Bind the ID for security
        $statement->bindParam(1, $placement_id);

        $statement->execute();
    }

    public function fetchPostById($post_id) {
        $sqlQuery = 'SELECT * FROM placement_opportunity WHERE placement_id = ?;';
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $post_id);
        $statement->execute();
        $dataSet = [];
        // loop through and read the results of the query and cast
        // them into a matching object
        while ($row = $statement->fetch()) {
            $dataSet[] = new PlacementsData($row);
        }
        return $dataSet;
    }

    public function updateData($post_id, $title, $salary, $location, $start,$end, $description) {
        $sqlQuery = 'UPDATE placement_opportunity 
                SET title = ?, salary = ?, location = ?, start_date = ?, end_date = ?, description = ?
                WHERE placement_id = ?';

        $statement = $this->_dbHandle->prepare($sqlQuery);

        $statement->bindParam(1, $title);
        $statement->bindParam(2, $salary);
        $statement->bindParam(3, $location);
        $statement->bindParam(4, $start);
        $statement->bindParam(5, $end);
        $statement->bindParam(6, $description);
        $statement->bindParam(7, $post_id);

        $statement->execute();
    }

    /**
     * Changes the status of a pet between 'lost' and 'found'.
     *
     * @param string $status The current status of the pet ('lost' or 'found').
     * @param int $pet_id The ID of the pet to update.
     * @return void
     */
    public function makeStatusActive($post_id) {

        $sqlQuery = "UPDATE placement_opportunity SET status = 'active' WHERE placement_id = ?";

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $post_id);//bind to prevent SQL injection
        $statement->execute(); // execute the PDO statement
    }

    /**
     * Changes the status of a pet between 'lost' and 'found'.
     *
     * @param string $status The current status of the pet ('lost' or 'found').
     * @param int $pet_id The ID of the pet to update.
     * @return void
     */
    public function makeStatusInactive($post_id) {

        $sqlQuery = "UPDATE placement_opportunity SET status = 'inactive' WHERE placement_id = ?";

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $post_id);//bind to prevent SQL injection
        $statement->execute(); // execute the PDO statement
    }
}