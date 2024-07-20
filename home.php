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

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Update existing record
        $sql = "UPDATE alumni SET fName='$fName', email='$email', phone='$phone' WHERE alumniID='$alumniID'";
        if ($conn->query($sql) === TRUE) {
            $error = "Record updated successfully";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
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

// Handle Degree Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['major'])) {
    $degreeID = isset($_POST['degreeID']) ? $_POST['degreeID'] : '';
    $major = $_POST['major'];
    $minor = $_POST['minor'];
    $graduationDT = $_POST['graduationDT'];
    $university = $_POST['university'];
    $city = $_POST['degreeCity'];
    $state = $_POST['degreeState'];

    // Sanitize input
    $major = $conn->real_escape_string($major);
    $minor = $conn->real_escape_string($minor);
    $graduationDT = $conn->real_escape_string($graduationDT);
    $university = $conn->real_escape_string($university);
    $city = $conn->real_escape_string($city);
    $state = $conn->real_escape_string($state);

    // Validate graduation date
    $currentDate = date('Y-m-d');
    if ($graduationDT > $currentDate) {
        $degree_error = "Graduation date cannot be in the future";
    } else {
        if ($degreeID) {
            // Update existing record
            $sql = "UPDATE degree SET major='$major', minor='$minor', graduationDT='$graduationDT', university='$university', city='$city', state='$state' WHERE degreeID='$degreeID' AND alumniID='$alumniID'";
            if ($conn->query($sql) === TRUE) {
                $degree_error = "Degree updated successfully";
            } else {
                $degree_error = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            // Insert new record
            $sql = "INSERT INTO degree (alumniID, major, minor, graduationDT, university, city, state) VALUES ('$alumniID', '$major', '$minor', '$graduationDT', '$university', '$city', '$state')";
            if ($conn->query($sql) === TRUE) {
                $degree_error = "New degree added successfully";
            } else {
                $degree_error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}


// Handle Employment Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['company'])) {
    $EID = isset($_POST['EID']) ? $_POST['EID'] : '';
    $company = $_POST['company'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $jobTitle = $_POST['jobTitle'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $currentYN = $_POST['currentYN'];

    // Sanitize input
    $company = $conn->real_escape_string($company);
    $city = $conn->real_escape_string($city);
    $state = $conn->real_escape_string($state);
    $zip = $conn->real_escape_string($zip);
    $jobTitle = $conn->real_escape_string($jobTitle);
    $startDate = $conn->real_escape_string($startDate);
    $endDate = $conn->real_escape_string($endDate);
    $currentYN = $conn->real_escape_string($currentYN);

    if ($EID) {
        // Update existing record
        $sql = "UPDATE employment SET company='$company', city='$city', state='$state', zip='$zip', jobTitle='$jobTitle', startDate='$startDate', endDate='$endDate', currentYN='$currentYN' WHERE EID='$EID' AND alumniID='$alumniID'";
        if ($conn->query($sql) === TRUE) {
            $employment_error = "Employment record updated successfully";
        } else {
            $employment_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO employment (alumniID, company, city, state, zip, jobTitle, startDate, endDate, currentYN) VALUES ('$alumniID', '$company', '$city', '$state', '$zip', '$jobTitle', '$startDate', '$endDate', '$currentYN')";
        if ($conn->query($sql) === TRUE) {
            $employment_error = "New employment record added successfully";
        } else {
            $employment_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Handle Skillset Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['skill'])) {
    $SID = isset($_POST['SID']) ? $_POST['SID'] : '';
    $skill = $_POST['skill'];
    $proficiency = $_POST['proficiency'];
    $description = $_POST['description'];

    // Sanitize input
    $skill = $conn->real_escape_string($skill);
    $proficiency = $conn->real_escape_string($proficiency);
    $description = $conn->real_escape_string($description);

    if ($SID) {
        // Update existing record
        $sql = "UPDATE skillset SET skill='$skill', proficiency='$proficiency', description='$description' WHERE SID='$SID' AND alumniID='$alumniID'";
        if ($conn->query($sql) === TRUE) {
            $skillset_error = "Skillset updated successfully";
        } else {
            $skillset_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO skillset (alumniID, skill, proficiency, description) VALUES ('$alumniID', '$skill', '$proficiency', '$description')";
        if ($conn->query($sql) === TRUE) {
            $skillset_error = "New skillset added successfully";
        } else {
            $skillset_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Handle Donation Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donationAmt'])) {
    $donationID = isset($_POST['donationID']) ? $_POST['donationID'] : '';
    $donationAmt = $_POST['donationAmt'];
    $donationDT = $_POST['donationDT'];
    $reason = $_POST['reason'];
    $description = $_POST['description'];

    // Sanitize input
    $donationAmt = $conn->real_escape_string($donationAmt);
    $donationDT = $conn->real_escape_string($donationDT);
    $reason = $conn->real_escape_string($reason);
    $description = $conn->real_escape_string($description);

    if ($donationID) {
        // Update existing record
        $sql = "UPDATE donations SET donationAmt='$donationAmt', donationDT='$donationDT', reason='$reason', description='$description' WHERE donationID='$donationID' AND alumniID='$alumniID'";
        if ($conn->query($sql) === TRUE) {
            $donation_error = "Donation record updated successfully";
        } else {
            $donation_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO donations (alumniID, donationAmt, donationDT, reason, description) VALUES ('$alumniID', '$donationAmt', '$donationDT', '$reason', '$description')";
        if ($conn->query($sql) === TRUE) {
            $donation_error = "New donation record added successfully";
        } else {
            $donation_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch all donation records
$donations = [];
$result = $conn->query("SELECT * FROM donations WHERE alumniID='$alumniID'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
}



// Fetch all skillset records
$skillsets = [];
$result = $conn->query("SELECT * FROM skillset WHERE alumniID='$alumniID'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $skillsets[] = $row;
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

// Fetch all degree records
$degrees = [];
$result = $conn->query("SELECT * FROM degree WHERE alumniID='$alumniID'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $degrees[] = $row;
    }
}

// Fetch all employment records
$employments = [];
$result = $conn->query("SELECT * FROM employment WHERE alumniID='$alumniID'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employments[] = $row;
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
            <li><a href="#">KSU Alumni Management</a></li>
            <li><a href="#alumni">Alumni</a></li>
            <li><a href="#address">Addresses</a></li>
            <li><a href="#degree">Degree</a></li>
            <li><a href="#employment">Employment</a></li>
            <li><a href="#skillset">Skillset</a></li>
            <li><a href="#donations">Donations</a></li>
            <li><a href="logout.php">Sign Out</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="main">
        <h2 id="alumni">Manage Alumni</h2>
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
                        
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2 id="address">Manage Address</h2>
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


            <h2 id="degree">Manage Degree</h2>
<?php if (!empty($degree_error)): ?>
    <div class="error"><?php echo $degree_error; ?></div>
<?php endif; ?>
<form id="degreeForm" method="POST" action="home.php">
    <input type="hidden" id="degreeID" name="degreeID">
    <div class="form-group">
        <label for="major">Major</label>
        <input type="text" id="major" name="major" class="form-control" required>
        <span class="invalid-feedback" id="majorError"></span>
    </div>
    <div class="form-group">
        <label for="minor">Minor</label>
        <input type="text" id="minor" name="minor" class="form-control">
        <span class="invalid-feedback" id="minorError"></span>
    </div>
    <div class="form-group">
        <label for="graduationDT">Graduation Date</label>
        <input type="date" id="graduationDT" name="graduationDT" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
        <span class="invalid-feedback" id="graduationDTError"></span>
    </div>
    <div class="form-group">
        <label for="university">University</label>
        <input type="text" id="university" name="university" class="form-control" required>
        <span class="invalid-feedback" id="universityError"></span>
    </div>
    <div class="form-group">
        <label for="degreeCity">City</label>
        <input type="text" id="degreeCity" name="degreeCity" class="form-control" required>
        <span class="invalid-feedback" id="degreeCityError"></span>
    </div>
    <div class="form-group">
        <label for="degreeState">State</label>
        <input type="text" id="degreeState" name="degreeState" class="form-control" required>
        <span class="invalid-feedback" id="degreeStateError"></span>
    </div>
    <div class="form-group">
        <button type="submit" class="btn">Add/Update Degree</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Major</th>
            <th>Minor</th>
            <th>Graduation Date</th>
            <th>University</th>
            <th>City</th>
            <th>State</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="degreeList">
        <?php foreach ($degrees as $degree): ?>
        <tr data-id="<?php echo $degree['degreeID']; ?>">
            <td><?php echo htmlspecialchars($degree['major']); ?></td>
            <td><?php echo htmlspecialchars($degree['minor']); ?></td>
            <td><?php echo htmlspecialchars($degree['graduationDT']); ?></td>
            <td><?php echo htmlspecialchars($degree['university']); ?></td>
            <td><?php echo htmlspecialchars($degree['city']); ?></td>
            <td><?php echo htmlspecialchars($degree['state']); ?></td>
            <td>
                <button class="btn btn-warning" onclick="editDegree(this)">Edit</button>
                <button class="btn btn-danger" onclick="deleteDegree(this)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h2 id="employment">Manage Employment</h2>>
    <?php if (!empty($employment_error)): ?>
    <div class="error"><?php echo $employment_error; ?></div>
    <?php endif; ?>
    <form id="employmentForm" method="POST" action="home.php">
    <input type="hidden" id="EID" name="EID">
    <div class="form-group">
        <label for="company">Company</label>
        <input type="text" id="company" name="company" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="city">City</label>
        <input type="text" id="city" name="city" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="state">State</label>
        <input type="text" id="state" name="state" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="zip">Zip</label>
        <input type="text" id="zip" name="zip" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="jobTitle">Job Title</label>
        <input type="text" id="jobTitle" name="jobTitle" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="startDate">Start Date</label>
        <input type="date" id="startDate" name="startDate" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="endDate">End Date</label>
        <input type="date" id="endDate" name="endDate" class="form-control">
    </div>
    <div class="form-group">
        <label for="currentYN">Current Employment</label>
        <select id="currentYN" name="currentYN" class="form-control" required>
            <option value="Y">Yes</option>
            <option value="N">No</option>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn">Add/Update Employment</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Company</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Job Title</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Current</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="employmentList">
        <?php foreach ($employments as $employment): ?>
        <tr data-id="<?php echo $employment['EID']; ?>">
            <td><?php echo htmlspecialchars($employment['company']); ?></td>
            <td><?php echo htmlspecialchars($employment['city']); ?></td>
            <td><?php echo htmlspecialchars($employment['state']); ?></td>
            <td><?php echo htmlspecialchars($employment['zip']); ?></td>
            <td><?php echo htmlspecialchars($employment['jobTitle']); ?></td>
            <td><?php echo htmlspecialchars($employment['startDate']); ?></td>
            <td><?php echo htmlspecialchars($employment['endDate']); ?></td>
            <td><?php echo htmlspecialchars($employment['currentYN']); ?></td>
            <td>
                <button class="btn btn-warning" onclick="editEmployment(this)">Edit</button>
                <button class="btn btn-danger" onclick="deleteEmployment(this)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 id="skillset">Manage Skillset</h2>
<?php if (!empty($skillset_error)): ?>
    <div class="error"><?php echo $skillset_error; ?></div>
<?php endif; ?>
<form id="skillsetForm" method="POST" action="home.php">
    <input type="hidden" id="SID" name="SID">
    <div class="form-group">
        <label for="skill">Skill</label>
        <input type="text" id="skill" name="skill" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="proficiency">Proficiency</label>
        <input type="text" id="proficiency" name="proficiency" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" required></textarea>
    </div>
    <div class="form-group">
        <button type="submit" class="btn">Add/Update Skill</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Skill</th>
            <th>Proficiency</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="skillsetList">
        <?php foreach ($skillsets as $skillset): ?>
        <tr data-id="<?php echo $skillset['SID']; ?>">
            <td><?php echo htmlspecialchars($skillset['skill']); ?></td>
            <td><?php echo htmlspecialchars($skillset['proficiency']); ?></td>
            <td><?php echo htmlspecialchars($skillset['description']); ?></td>
            <td>
                <button class="btn btn-warning" onclick="editSkillset(this)">Edit</button>
                <button class="btn btn-danger" onclick="deleteSkillset(this)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 id="donations">Manage Donations</h2>
<?php if (!empty($donation_error)): ?>
    <div class="error"><?php echo $donation_error; ?></div>
<?php endif; ?>
<form id="donationForm" method="POST" action="home.php">
    <input type="hidden" id="donationID" name="donationID">
    <div class="form-group">
        <label for="donationAmt">Donation Amount</label>
        <input type="text" id="donationAmt" name="donationAmt" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="donationDT">Donation Date</label>
        <input type="date" id="donationDT" name="donationDT" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="reason">Reason</label>
        <input type="text" id="reason" name="reason" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <input type="text" id="description" name="description" class="form-control">
    </div>
    <div class="form-group">
        <button type="submit" class="btn">Add/Update Donation</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Donation Amount</th>
            <th>Donation Date</th>
            <th>Reason</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="donationList">
        <?php foreach ($donations as $donation): ?>
        <tr data-id="<?php echo $donation['donationID']; ?>">
            <td><?php echo htmlspecialchars($donation['donationAmt']); ?></td>
            <td><?php echo htmlspecialchars($donation['donationDT']); ?></td>
            <td><?php echo htmlspecialchars($donation['reason']); ?></td>
            <td><?php echo htmlspecialchars($donation['description']); ?></td>
            <td>
                <button class="btn btn-warning" onclick="editDonation(this)">Edit</button>
                <button class="btn btn-danger" onclick="deleteDonation(this)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


        </div>
    </div>

    <script>
        // Alumni edit and delete functions
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
// Address edit and delete functions
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
 // Degree edit and delete functions
function editDegree(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');
    const major = row.cells[0].textContent;
    const minor = row.cells[1].textContent;
    const graduationT = row.cells[2].textContent;
    const university = row.cells[3].textContent;
    const city = row.cells[4].textContent;
    const state = row.cells[5].textContent;

    document.getElementById('degreeID').value = id;
    document.getElementById('major').value = major;
    document.getElementById('minor').value = minor;
    document.getElementById('graduationDT').value = graduationDT;
    document.getElementById('university').value = university;
    document.getElementById('degreeCity').value = city;
    document.getElementById('degreeState').value = state;

    // Remove the row from the table to prevent duplicates
    row.remove();
}

function deleteDegree(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the degree record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_degree.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText === "Record deleted successfully") {
                row.remove();
            } else {
                console.error('Failed to delete degree: ' + xhr.responseText);
            }
        } else {
            console.error('Failed to delete degree.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}

// Employment edit and delete functions
function editEmployment(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');
    const company = row.cells[0].textContent;
    const city = row.cells[1].textContent;
    const state = row.cells[2].textContent;
    const zip = row.cells[3].textContent;
    const jobTitle = row.cells[4].textContent;
    const startDate = row.cells[5].textContent;
    const endDate = row.cells[6].textContent;
    const currentYN = row.cells[7].textContent;

    document.getElementById('EID').value = id;
    document.getElementById('company').value = company;
    document.getElementById('city').value = city;
    document.getElementById('state').value = state;
    document.getElementById('zip').value = zip;
    document.getElementById('jobTitle').value = jobTitle;
    document.getElementById('startDate').value = startDate;
    document.getElementById('endDate').value = endDate;
    document.getElementById('currentYN').value = currentYN;

    // Remove the row from the table to prevent duplicates
    row.remove();
}

function deleteEmployment(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the employment record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_employment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "Record deleted successfully") {
                row.remove();
            } else {
                console.error('Failed to delete employment: ' + xhr.responseText);
            }
        } else {
            console.error('Failed to delete employment.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}

// Skillset edit and delete functions
function editSkillset(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');
    const skill = row.cells[0].textContent;
    const proficiency = row.cells[1].textContent;
    const description = row.cells[2].textContent;

    document.getElementById('SID').value = id;
    document.getElementById('skill').value = skill;
    document.getElementById('proficiency').value = proficiency;
    document.getElementById('description').value = description;

    // Remove the row from the table to prevent duplicates
    row.remove();
}

function deleteSkillset(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the skillset record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_skillset.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "Record deleted successfully") {
                row.remove();
            } else {
                console.error('Failed to delete skillset: ' + xhr.responseText);
            }
        } else {
            console.error('Failed to delete skillset.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}

// Donation edit and delete functions
function editDonation(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');
    const donationAmt = row.cells[0].textContent;
    const donationDT = row.cells[1].textContent;
    const reason = row.cells[2].textContent;
    const description = row.cells[3].textContent;

    document.getElementById('donationID').value = id;
    document.getElementById('donationAmt').value = donationAmt;
    document.getElementById('donationDT').value = donationDT;
    document.getElementById('reason').value = reason;
    document.getElementById('description').value = description;

    // Remove the row from the table to prevent duplicates
    row.remove();
}

function deleteDonation(button) {
    const row = button.parentNode.parentNode;
    const id = row.getAttribute('data-id');

    // Make an AJAX request to delete the donation record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_donation.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            row.remove();
        } else {
            console.error('Failed to delete donation.');
        }
    };
    xhr.send('id=' + encodeURIComponent(id));
}

    </script>
</body>
</html>