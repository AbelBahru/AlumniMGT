<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $alumniID = $_POST['id'];

    // deletes the dependent records from the user table
    $sql = "DELETE FROM user WHERE alumniID = '$alumniID'";
    if ($conn->query($sql) === TRUE) {
        // deletes the alumni record
        $sql = "DELETE FROM alumni WHERE alumniID = '$alumniID'";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting alumni record: " . $conn->error;
        }
    } else {
        echo "Error deleting user records: " . $conn->error;
    }

    $conn->close();
}
?>