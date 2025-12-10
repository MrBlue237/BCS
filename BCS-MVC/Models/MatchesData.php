<?php
class MatchesData {

    protected $matches_student_placement, $user_ID, $placement_ID;

    public function __construct($dbRow) {
        $this->matches_student_placement = $dbRow['$matches_student_placement'];
        $this->user_ID = $dbRow['user_ID'];
        $this->placement_ID = $dbRow['placement_ID'];
    }
    public function getMatches() {
        return $this->matches_student_placement;
    }
    public function getPlacement() {
        return $this->placement_ID;
    }

}