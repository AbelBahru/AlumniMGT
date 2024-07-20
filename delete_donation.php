<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationID = $_POST['id'];

    // Sanitize input
    $donationID = $conn->real_escape_string($donationID);

    // Delete record
    $sql = "DELETE FROM donations WHERE donationID='$donationID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>