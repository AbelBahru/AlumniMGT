<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Sanitize input
    $id = $conn->real_escape_string($id);

    // Delete record
    $sql = "DELETE FROM skillset WHERE SID='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>