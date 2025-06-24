<?php
session_start();

// Ensure user is logged in as a doctor
if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'd') {
    header("Location: ../login.php");
    exit();
}

// Include the database connection
include("../connection.php");

// Fetching user data
$useremail = $_SESSION['user'];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$doctorid = $userfetch["docid"];
$username = $userfetch["docname"];

// Fetching feedback for the doctor
$feedbackQuery = "SELECT f.*, p.pname FROM feedback f JOIN patient p ON f.patient_id = p.pid WHERE f.doctor_id = ?";
$stmt = $database->prepare($feedbackQuery);
$stmt->bind_param("i", $doctorid);
$stmt->execute();
$feedbackResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>View Feedback</title>
    <style>
        .feedback-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .feedback-item {
            border-bottom: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
        }

        .feedback-item h3 {
            font-size: 18px;
            color: #333;
        }

        .feedback-item p {
            font-size: 16px;
            color: #555;
        }

        .feedback-item .rating {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
        }

        .message {
            font-size: 18px;
            color: green;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Sidebar (Menu) -->
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username, 0, 13); ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="view_feedback.php" class="non-style-link-menu"><div><p class="menu-text">View_feedback</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Feedback Display Content -->
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>View Your Feedback</h3>
                                        <h1><?php echo $username; ?>, here is the feedback.</h1>
                                        <p>Here are the feedbacks that have been submitted:</p>

                                        <!-- Feedback Display -->
                                        <div class="feedback-container">
                                            <?php
                                            if ($feedbackResult->num_rows > 0) {
                                                while ($feedback = $feedbackResult->fetch_assoc()) {
                                                    echo "<div class='feedback-item'>";
                                                    echo "<h3>Patient: " . $feedback['pname'] . "</h3>";
                                                    echo "<p class='rating'>Rating: " . $feedback['rating'] . "/5</p>";
                                                    echo "<p>" . $feedback['feedback_text'] . "</p>";
                                                    echo "</div>";
                                                }
                                            } else {
                                                echo "<p>No feedback available.</p>";
                                            }
                                            ?>
                                        </div>

                                        <!-- Message on submission -->
                                        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
