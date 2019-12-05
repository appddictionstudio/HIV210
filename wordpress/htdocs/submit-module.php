<?php
if (file_exists(dirname(__FILE__) . '/wp-load.php')) {
    define('DOING_AJAX', true); //Stop cron running.
    define('WP_USE_THEMES', false);
    include (dirname(__FILE__) . '/wp-load.php');
    $host = "localhost:3306";		// normally localhost, but not necessarily.
    $usr  = "bn_wordpress";       	// your db userid
    $pwd  = "12885fb618";    // your db password
    $db   = "bitnami_wordpress";	// your database
    $con = mysqli_connect($host,$usr,$pwd,$db); 
}
if(isset($_REQUEST['case'])){
    $userid = get_current_user_id();
    $case = $_REQUEST['case'];
    $questions = $_REQUEST['questions'];

    $query = "INSERT INTO `quiz_summery`(`userid`, `modulename`, `questionnumber`, `submitdate`) "
            . "VALUES ('$userid','$case','$questions',NOW())";
    mysqli_query($con, $query);
    echo mysqli_error($con); 
    $insert_id = mysqli_insert_id($con);
    if($insert_id > 0) echo 'success'; else echo 'failed';
}

?>
