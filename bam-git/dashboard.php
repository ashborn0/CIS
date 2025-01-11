<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>Movies-io</title>
</head>
<body>
    <!-- Navbar Section -->
    <div class="navbar">
        
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo">CDO POLICE DEPARTMENT</h1>
            </div>
            <div class="menu-container">
    <ul class="menu-list">
        <li><a href="dashboard.php" class="link">Home</a></li>
        <li><a href="suspects.php" class="link">Suspects</a></li>
        <li><a href="sus-dash.php" class="link">Dashboard</a></li>
    </ul>
</div>



            <div class="profile-container">
                <img class="profile-picture" src="images/profiles.jpg" alt="">
                <div class="profile-text-container">
                    <span class="profile-text">Profile</span>
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="toggle">
                    <i class="fas fa-moon toggle-icon"></i>
                    <i class="fas fa-sun toggle-icon"></i>
                    <div class="toggle-ball"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <i class="left-menu-icon fas fa-search"></i>
        <i class="left-menu-icon fas fa-home"></i>
        <i class="left-menu-icon fas fa-users"></i>
    </div>

    <?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "crimes_db"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_person_name'])) {
        // Add new person to the database if provided
        $new_person_name = $conn->real_escape_string($_POST['new_person_name']);
        $gender = $_POST['gender'];
        $race = $_POST['race'];
        $age = $_POST['age']; // Age added here
        $sql_add_person = "INSERT INTO suspect (name, gender, race, age) VALUES ('$new_person_name', '$gender', '$race', '$age')";
        if ($conn->query($sql_add_person) === TRUE) {
            $person_id = $conn->insert_id; // Get the ID of the newly inserted person
        } else {
            die("Error adding new person: " . $conn->error);
        }
    } else {
        $person_id = $_POST['person_id']; // Use existing person ID
    }

    // Collect other incident details
    $date = $_POST['date'];
    $manner_of_death_id = $_POST['manner_of_death_id'];
    $armed_id = $_POST['armed_id'];
    $threat_level_id = $_POST['threat_level_id'];
    $flee_id = $_POST['flee_id'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $arms_category_id = $_POST['arms_category_id'];
    $year = $_POST['year'];

    $sql = "INSERT INTO incident 
            (person_id, date, manner_of_death_id, armed_id, threat_level_id, flee_id, city, state, arms_category_id, year) 
            VALUES 
            ('$person_id', '$date', '$manner_of_death_id', '$armed_id', '$threat_level_id', '$flee_id', '$city', '$state', '$arms_category_id', '$year')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Incident added successfully. <a href='sus-dash.php'>Go to Dashboard</a></p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch dropdown data
$persons = $conn->query("SELECT person_id, name FROM suspect");
$manner_of_death = $conn->query("SELECT manner_of_death_id, manner_of_death FROM manner_of_death");
$armed = $conn->query("SELECT armed_id, armed FROM armed");
$threat_level = $conn->query("SELECT threat_level_id, threat_level FROM threat_level");
$flee = $conn->query("SELECT flee_id, flee FROM flee");
$arms_category = $conn->query("SELECT arms_category_id, arms_category FROM arms_category");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Incident</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #444;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: black;
        }
        input, select, button {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .toggle-section {
            margin-bottom: 15px;
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
        function togglePersonInput() {
            const newPersonSection = document.getElementById('new-person-section');
            const existingPersonSection = document.getElementById('existing-person-section');
            const isAddingNew = document.getElementById('add-new-person').checked;

            newPersonSection.style.display = isAddingNew ? 'block' : 'none';
            existingPersonSection.style.display = isAddingNew ? 'none' : 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Add Suspect</h1>
        <form method="post">
            <div class="toggle-section">
                <label>
                    <input type="radio" name="person_option" id="select-person" value="existing" checked onclick="togglePersonInput()"> Select Existing Person
                </label>
                <label>
                    <input type="radio" name="person_option" id="add-new-person" value="new" onclick="togglePersonInput()"> Add New Person
                </label>
            </div>

            <div id="existing-person-section">
                <label for="person_id">Person</label>
                <select name="person_id" id="person_id">
                    <option value="">Select a Person</option>
                    <?php while ($row = $persons->fetch_assoc()): ?>
                        <option value="<?= $row['person_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div id="new-person-section" class="hidden">
                <label for="new_person_name">New Person Name</label>
                <input type="text" name="new_person_name" id="new_person_name">

                <label for="gender">Gender</label>
                <select name="gender" id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>

                <label for="race">Race</label>
                <select name="race" id="race" required>
                    <option value="">Select Race</option>
                    <option value="Asian">Asian</option>
                    <option value="Hispanic">Hispanic</option>
                    <option value="White">White</option>
                    <option value="Black">Black</option>
                </select>

                <label for="age">Age</label>
                <input type="number" name="age" id="age" required>
            </div>

            <label for="date">Date</label>
            <input type="date" name="date" id="date" required>

            <label for="manner_of_death_id">Manner of Death</label>
            <select name="manner_of_death_id" id="manner_of_death_id" required>
                <option value="">Select a Manner of Death</option>
                <?php while ($row = $manner_of_death->fetch_assoc()): ?>
                    <option value="<?= $row['manner_of_death_id'] ?>"><?= htmlspecialchars($row['manner_of_death']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="armed_id">Armed</label>
            <select name="armed_id" id="armed_id" required>
                <option value="">Select Armed Status</option>
                <?php while ($row = $armed->fetch_assoc()): ?>
                    <option value="<?= $row['armed_id'] ?>"><?= htmlspecialchars($row['armed']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="threat_level_id">Threat Level</label>
            <select name="threat_level_id" id="threat_level_id" required>
                <option value="">Select Threat Level</option>
                <?php while ($row = $threat_level->fetch_assoc()): ?>
                    <option value="<?= $row['threat_level_id'] ?>"><?= htmlspecialchars($row['threat_level']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="flee_id">Flee</label>
            <select name="flee_id" id="flee_id" required>
                <option value="">Select Flee Status</option>
                <?php while ($row = $flee->fetch_assoc()): ?>
                    <option value="<?= $row['flee_id'] ?>"><?= htmlspecialchars($row['flee']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="city">City</label>
            <input type="text" name="city" id="city" required>

            <label for="state">Province</label>
            <input type="text" name="state" id="state" required>

            <label for="arms_category_id">Arms Category</label>
            <select name="arms_category_id" id="arms_category_id" required>
                <option value="">Select Arms Category</option>
                <?php while ($row = $arms_category->fetch_assoc()): ?>
                    <option value="<?= $row['arms_category_id'] ?>"><?= htmlspecialchars($row['arms_category']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="year">Year</label>
            <input type="number" name="year" id="year" required>

            <button type="submit">Add Incident</button>
        </form>
    </div>
</body>
</html>

</html>

    <!-- Style Section -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
        }
        .navbar {
            width: 100%;
            height: 50px;
            background-color: rgb(148, 15, 15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 50px;
            color: white;
        }
        .logo-container .logo {
            font-size: 30px;
        }
        .menu-container {
            flex: 6;
        }
        .menu-list {
            margin-left: 50px;
            display: flex;
            list-style: none;
        }
        .menu-list-item {
            margin-right: 30px;
        }
        .profile-container {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .profile-text-container {
            margin: 0 20px;
        }
        .profile-picture {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }
        .form-container {
            width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input,
        .form-container select,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>

</body>
</html>
