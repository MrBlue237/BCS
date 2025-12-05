<?php
class PlacementsData {

    protected $placement_id, $employer_id, $title, $description,$salary, $location, $start_date, $end_date;

    public function __construct($dbRow) {
        $this->placement_id = $dbRow['placement_ID'];
        $this->employer_id = $dbRow['employer_ID'];
        $this->title = $dbRow['title'];
        $this->description = $dbRow['description'];
        $this->salary = $dbRow['salary'];
        $this->location = $dbRow['location'];
        $this->start_date = $dbRow['start_date'];
        $this->end_date = $dbRow['end_date'];
    }

    public function getPlacementID() {
        return $this->placement_id;
    }
    public function getEmployerID() {
        return $this->employer_id;
    }

    public function getTitle() {
        return $this->title;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getSalary() {
        return $this->salary;
    }
    public function getLocation() {
        return $this->location;
    }
    public function getStartDate() {
        return $this->start_date;
    }
    public function getEndDate() {
        return $this->end_date;
    }


}