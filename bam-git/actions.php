<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "crimes_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// SQL query to fetch suspects or incidents based on the search term
if (!empty($search_term)) {
    $search_term = $conn->real_escape_string($search_term);
    $search_query = "SELECT * FROM suspect WHERE name LIKE '%$search_term%' OR race LIKE '%$search_term%' OR gender LIKE '%$search_term%'";
} else {
    $search_query = "SELECT * FROM suspect";
}

$result = $conn->query($search_query);

// Output the search results (you can modify this part to display your results properly)
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['race']) . " - " . htmlspecialchars($row['gender']) . "</p>";
    }
} else {
    echo "<p>No results found</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $person_id = $_POST['person_id'];

    if ($action === 'edit') {
        // Redirect to an edit page (you need to create edit.php)
        header("Location: edit.php?person_id=$person_id");
        exit();
    } elseif ($action === 'archive') {
        // Mark the record as archived (update database)
        $sql = "UPDATE suspect SET archived = 1 WHERE person_id = $person_id"; // Ensure you have an "archived" column
        if ($conn->query($sql) === TRUE) {
            echo "Person with ID $person_id archived successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
