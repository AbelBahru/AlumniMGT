<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Sanitize input
    $id = $conn->real_escape_string($id);

    // Delete the degree record
    $sql = "DELETE FROM degree WHERE degreeID='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>