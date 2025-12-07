<?php
class PlacementsData {

    protected $placement_id, $employer_id, $title, $description,$salary, $location, $start_date, $end_date, $status, $deadline, $date_posted, $about_company, $what_we_offer;

    public function __construct($dbRow) {
        $this->placement_id = $dbRow['placement_ID'];
        $this->employer_id = $dbRow['employer_ID'];
        $this->title = $dbRow['title'];
        $this->description = $dbRow['description'];
        $this->salary = $dbRow['salary'];
        $this->location = $dbRow['location'];
        $this->start_date = $dbRow['start_date'];
        $this->end_date = $dbRow['end_date'];
        $this->status = $dbRow['status'];
        $this->about_company = $dbRow['about_company'];
        $this->what_we_offer = $dbRow['what_we_offer'];
        $this->deadline = $dbRow['deadline'] ?? null;
        $this->date_posted = $dbRow['date_posted'] ?? null;
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

    public function getStatus() {
        return $this->status;
    }
    public function getDeadline() {
        return $this->deadline;
    }

    public function getDatePosted() {
        return $this->date_posted;
    }

    public function getAboutCompany() {
        return $this->about_company;
    }

    public function getWhatWeOffer() {
        return $this->what_we_offer;
    }

}