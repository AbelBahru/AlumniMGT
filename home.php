<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
include 'db_connect.php';

$error = "";
$address_error = "";

// Fetch the logged-in user's alumniID
$username = $_SESSION['username'];
$result = $conn->query("SELECT alumniID FROM user WHERE UID='$username'");
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $alumniID = $user['alumniID'];
} else {
    // Handle case where no alumniID is found
    echo "No alumni ID found for the logged-in user.";
    exit();
}

// Handle Alumni Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fName'])) {
    $fName = $_POST['fName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Sanitize input
    $fName = $conn->real_escape_string($fName);
    $email = $conn->real_escape_string($email);
    $phone = $conn->real_escape_string($phone);

    // Update existing record
    $sql = "UPDATE alumni SET fName='$fName', email='$email', phone='$phone' WHERE alumniID='$alumniID'";
    if ($conn->query($sql) === TRUE) {
        $error = "Record updated successfully";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Address Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['address'])) {
    $addressID = isset($_POST['addressID']) ? $_POST['addressID'] : '';
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zipCode'];

    // Sanitize input
    $address = $conn->real_escape_string($address);
    $city = $conn->real_escape_string($city);
    $state = $conn->real_escape_string($state);
    $zipCode = $conn->real_escape_string($zipCode);

    if ($addressID) {
        // Update existing record
        $sql = "UPDATE address SET address='$address', city='$city', state='$state', zipCode='$zipCode' WHERE addressID='$addressID' AND alumniID='$alumniID'";
        if ($conn->query($sql) === TRUE) {
            $address_error = "Address updated successfully";
        } else {
            $address_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO address (alumniID, address, city, state, zipCode) VALUES ('$alumniID', '$address', '$city', '$state', '$zipCode')";
        if ($conn->query($sql) === TRUE) {
            $address_error = "New address added successfully";
        } else {
            $address_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch all alumni records
$alumni = [];
$result = $conn->query("SELECT * FROM alumni");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $alumni[] = $row;
    }
}

// Fetch all address records
$addresses = [];
$result = $conn->query("SELECT * FROM address");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Alumni</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /*  CSS  */

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .navigation {
            background-color: #333;
            color: #fff;
            padding: 15px;
        }

        .navigation ul {
            list-style: none;
            padding: 0;
        }

        .navigation ul li {
            display: inline;
            margin-right: 10px;
        }

        .navigation ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
        }

        .navigation ul li a:hover {
            background-color: #575757;
        }

        .main {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 15px;
            background-color: #f2d813;
            color: #000; /* Change text color to black */
            font-weight: bold; /* Make text bold */
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #5830b4;
        }

        .btn-secondary {
            background-color: #f2d813;
            color: #000; /* Change text color to black */
            font-weight: bold; /* Make text bold */
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-warning {
            background-color: #f2d813;
            color: #000; /* Change text color to black */
            font-weight: bold; /* Make text bold */
        }

        .btn-danger {
            background-color: #f2d813;
            color: #000; /* Change text color to black */
            font-weight: bold; /* Make text bold */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .invalid-feedback {
            color: red;
            font-size: 0.9em;
        }

        .error {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="navigation">
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Alumni</a></li>
            <li><a href="#">Donations</a></li>
            <li><a href="logout.php">Sign Out</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="main">
            <h2>Manage Alumni</h2>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form id="alumniForm" method="POST" action="home.php">
                <input type="hidden" id="id" name="id">
                <div class="form-group">
                    <label for="fName">Name</label>
                    <input type="text" id="fName" name="fName" class="form-control" required>
                    <span class="invalid-feedback" id="nameError"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <span class="invalid-feedback" id="emailError"></span>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                    <span class="invalid-feedback" id="phoneError"></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Add/Update Alumni</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="alumniList">
                    <?php foreach ($alumni as $alumnus): ?>
                    <tr data-id="<?php echo $alumnus['alumniID']; ?>">
                        <td><?php echo htmlspecialchars($alumnus['fName']); ?></td>
                        <td><?php echo htmlspecialchars($alumnus['email']); ?></td>
                        <td><?php echo htmlspecialchars($alumnus['phone']); ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editAlumni(this)">Edit</button>
                            <button class="btn btn-danger" onclick="deleteAlumni(this)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Manage Address</h2>
            <?php if (!empty($address_error)): ?>
                <div class="error"><?php echo $address_error; ?></div>
            <?php endif; ?>
            <form id="addressForm" method="POST" action="home.php">
                <input type="hidden" id="addressID" name="addressID">
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                    <span class="invalid-feedback" id="addressError"></span>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                    <span class="invalid-feedback" id="cityError"></span>
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" class="form-control" required>
                    <span class="invalid-feedback" id="stateError"></span>
                </div>
                <div class="form-group">
                    <label for="zipCode">Zip Code</label>
                    <input type="text" id="zipCode" name="zipCode" class="form-control" required>
                    <span class="invalid-feedback" id="zipCodeError"></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Add/Update Address</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zip Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="addressList">
                    <?php foreach ($addresses as $address): ?>
                    <tr data-id="<?php echo $address['addressID']; ?>">
                        <td><?php echo htmlspecialchars($address['address']); ?></td>
                        <td><?php echo htmlspecialchars($address['city']); ?></td>
                        <td><?php echo htmlspecialchars($address['state']); ?></td>
                        <td><?php echo htmlspecialchars($address['zipCode']); ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="editAddress(this)">Edit</button>
                            <button class="btn btn-danger" onclick="deleteAddress(this)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editAlumni(button) {
            const row = button.parentNode.parentNode;
            const id = row.getAttribute('data-id');
            const fName = row.cells[0].textContent;
            const email = row.cells[1].textContent;
            const phone = row.cells[2].textContent;

            document.getElementById('id').value = id;
            document.getElementById('fName').value = fName;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;

            // Remove the row from the table to prevent duplicates
            row.remove();
        }

        function deleteAlumni(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the alumni record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_alumni.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "Record deleted successfully") {
                row.remove();
            } else {
                console.error('Failed to delete alumni: ' + xhr.responseText);
            }
        } else {
            console.error('Failed to delete alumni.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}

        function editAddress(button) {
            const row = button.parentNode.parentNode;
            const id = row.getAttribute('data-id');
            const address = row.cells[0].textContent;
            const city = row.cells[1].textContent;
            const state = row.cells[2].textContent;
            const zipCode = row.cells[3].textContent;

            document.getElementById('addressID').value = id;
            document.getElementById('address').value = address;
            document.getElementById('city').value = city;
            document.getElementById('state').value = state;
            document.getElementById('zipCode').value = zipCode;

            // Remove the row from the table to prevent duplicates
            row.remove();
        }

        function deleteAddress(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the address record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_address.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "Record deleted successfully") {
                row.remove();
            } else {
                console.error('Failed to delete address: ' + xhr.responseText);
            }
        } else {
            console.error('Failed to delete address.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}
    </script>
</body>
</html>
