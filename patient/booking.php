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

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

?>

<div class="container">
    <!-- User Menu Section -->
    <div class="menu">
        <!-- Your menu content here... -->
    </div>

    <div class="dash-body">
        <form action="booking-complete.php" method="post">
            <?php
            if ($_GET) {
                if (isset($_GET["id"])) {
                    $id = $_GET["id"];
                    $sqlmain = "SELECT * FROM schedule 
                                INNER JOIN doctor ON schedule.docid = doctor.docid 
                                WHERE schedule.scheduleid = $id 
                                ORDER BY schedule.scheduledate DESC";
                    $result = $database->query($sqlmain);
                    $row = $result->fetch_assoc();
                    $scheduleid = $row["scheduleid"];
                    $title = $row["title"];
                    $docname = $row["docname"];
                    $docemail = $row["docemail"];
                    $scheduledate = $row["scheduledate"];
                    $scheduletime = $row["scheduletime"];

                    // Get the next available appointment number
                    $sql2 = "SELECT * FROM appointment WHERE scheduleid = $id";
                    $result12 = $database->query($sql2);
                    $apponum = ($result12->num_rows) + 1;
                    ?>

                    <!-- Hidden Fields for Appointment Data -->
                    <input type="hidden" name="scheduleid" value="<?php echo $scheduleid; ?>">
                    <input type="hidden" name="apponum" value="<?php echo $apponum; ?>">
                    <input type="hidden" name="date" value="<?php echo $today; ?>">

                    <div class="dashboard-items search-items">
                        <div class="h1-search">Session Details</div>
                        <div class="h3-search">Doctor name: <b><?php echo $docname; ?></b></div>
                        <div class="h3-search">Doctor Email: <b><?php echo $docemail; ?></b></div>
                        <div class="h3-search">
                            Session Title: <?php echo $title; ?><br>
                            Scheduled Date: <?php echo $scheduledate; ?><br>
                            Starts: <b>@<?php echo substr($scheduletime, 0, 5); ?></b> (24h)
                        </div>
                        <div class="h3-search">Channeling fee: <b>LKR.2,000.00</b></div>
                    </div>

                    <div class="dashboard-items search-items">
                        <div class="h1-search" style="font-size:20px; text-align:center;">Your Appointment Number</div>
                        <center>
                            <div class="dashboard-icons" style="font-size:70px; font-weight:800;"><?php echo $apponum; ?></div>
                        </center>
                    </div>

                    <div class="dashboard-items search-items">
                        <input type="submit" class="login-btn btn-primary btn btn-book" value="Book now" name="booknow" style="width:95%; text-align: center;">
                    </div>
                    <?php
                }
            }
            ?>
        </form>
    </div>
</div>
