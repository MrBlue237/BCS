<?php
/**
 * Controller for managing and displaying an employer's placement records.
 */

// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// load required classes
require_once('Models/PlacementsDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'My Posts';

// Access Control: Must be logged in AND must be an employer
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: index.php');
    exit;
}

$employerId = $_SESSION['user_id'];

$placementsDataSet = new PlacementsDataSet();

// Fetch ONLY posts belonging to the logged-in employer
$view->placementsDataSet = $placementsDataSet->fetchPostsByEmployerID($employerId);

// Send a results count to the view
if (count($view->placementsDataSet) == 0)
{
    $view->dbMessage = "No results";
}
else
{
    $view->dbMessage = count($view->placementsDataSet) . " result(s)";
}

// Check for success message from add_post.php
$view->success = $_SESSION['placements_success'] ?? null;
unset($_SESSION['placements_success']);


/**
 * Handles the Delete operation from the modal.
 * Triggered by the 'Delete' button in the modal footer.
 */
if (isset($_POST['delete_placement'])) {
    /**
     * @param int $pet_id The id of the pet to delete.
     */
    $placement_id = $_POST['delete_placement'];
    //sent to model to delete record
    $placementsDataSet->deletePlacement($placement_id);
    // Reload page to show updated list
    header('Location: my_posts.php');
    exit;
}

/**
 * Handles the change of a post's status (active/de-active).
 * Triggered by the 'activate/de-activate_status' button from the pet card.
 */
if(isset($_POST['activate_status'])){

    /**
     * @param string $status The current status of the pet ('lost' or 'found').
     * @param int $pet_id The unique identifier for the pet.
     */

    //collect pet id to only change their status
    $post_id = $_POST['post_id'];
    //this will change it to the opposite of what it is currently
    $placementsDataSet->makeStatusActive($post_id);

    //provide appropriate success when completed
    $_SESSION['post_success'] = 'Success: Status Changed';
    //Reload page to show update
    header('Location: my_posts.php');
    exit;
}
/**
 * Handles the change of a post's status (active/de-active).
 * Triggered by the 'activate/de-activate_status' button from the pet card.
 */
if(isset($_POST['de-activate_status'])){

    /**
     * @param string $status The current status of the pet ('lost' or 'found').
     * @param int $pet_id The unique identifier for the pet.
     */

    //collect pet id to only change their status
    $post_id = $_POST['post_id'];
    //this will change it to the opposite of what it is currently
    $placementsDataSet->makeStatusInactive($post_id);

    //provide appropriate success when completed
    $_SESSION['post_success'] = 'Success: Status Changed';
    //Reload page to show update
    header('Location: my_posts.php');
    exit;
}

// include the view
require_once('Views/my_posts.phtml');