<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

if ($_POST) {
    if (isset($_POST["booknow"])) {
        $apponum = $_POST["apponum"];
        $scheduleid = $_POST["scheduleid"];
        $date = $_POST["date"];

        // Insert the appointment into the database
        $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) 
                 VALUES ($userid, $apponum, $scheduleid, '$date')";

        $result = $database->query($sql2);

        if ($result) {
            header("Location: appointment.php?action=booking-added&id=$apponum&titleget=none");
        } else {
            echo "Error booking appointment: " . $database->error;
        }
    }
}
?>
