<?php
session_start();

// Ensure user is logged in as a patient
if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'p') {
    header("Location: ../login.php");
    exit();
}

// Include the database connection
include("../connection.php");

// Fetching user data
$useremail = $_SESSION['user'];
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Process feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $feedback_text = $_POST['feedback'];

    // Insert feedback into the database
    $query = "INSERT INTO feedback (patient_id, doctor_id, feedback_text, rating) VALUES (?, ?, ?, ?)";
    $stmt = $database->prepare($query);
    $stmt->bind_param("iisi", $userid, $doctor_id, $feedback_text, $rating);

    if ($stmt->execute()) {
        $message = "Feedback submitted successfully!";
    } else {
        $message = "Error submitting feedback. Please try again.";
    }

    $stmt->close();
}

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
    <title>Feedback</title>
    <style>
        /* Ensures the form is centered and aligned */
        .feedback-container {
            max-width: 600px;
            margin: 0 auto;
            animation: transitionIn-Y-over 0.5s;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        input, textarea, select {
            padding: 12px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box; /* Ensure padding is included in width */
        }

        textarea {
            resize: vertical; /* Allow resizing vertically */
        }

        .login-btn {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        .submit-btn-container {
            display: flex;
            justify-content: center;
        }

        .message {
            margin-top: 20px;
            font-size: 18px;
            color: green;
            text-align: center;
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
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="feedback.php" class="non-style-link-menu"><div><p class="menu-text">Feedback</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Feedback Form Content -->
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Provide Your Feedback</h3>
                                        <h1><?php echo $username; ?>, share your feedback.</h1>
                                        <p>Help us improve by providing your experience with your doctor.<br>Fill in the details below:</p>

                                        <!-- Feedback Form -->
                                        <form action="feedback.php" method="POST">
                                            <div class="feedback-container">
                                                <label for="doctor">Select Doctor:</label>
                                                <select name="doctor_id" id="doctor" required>
                                                    <!-- Populate doctors dynamically from the database -->
                                                    <?php
                                                    $doctorList = $database->query("SELECT * FROM doctor");
                                                    while ($doctor = $doctorList->fetch_assoc()) {
                                                        echo "<option value='" . $doctor['docid'] . "'>" . $doctor['docname'] . "</option>";
                                                    }
                                                    ?>
                                                </select>

                                                <label for="rating">Rating (1-5):</label>
                                                <input type="number" name="rating" id="rating" min="1" max="5" required>

                                                <label for="feedback">Your Feedback:</label>
                                                <textarea name="feedback" id="feedback" rows="4" required></textarea>

                                                <!-- Submit Button -->
                                                <div class="submit-btn-container">
                                                    <input type="submit" value="Submit Feedback" class="login-btn btn-primary btn">
                                                </div>
                                            </div>
                                        </form>

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
