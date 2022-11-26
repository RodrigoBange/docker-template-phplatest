<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assignment 03</title>
    <link rel="stylesheet" href="css/stylesheet_management.css">
</head>
<body>
<h1>Post Management</h1>

<?php
require_once("dbconfig.php");

// Set up connection and retrieve messages
try {
    $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful.";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

function displayMessages($connection)
{
    // Get all messages from database
    try {
        $query = "SELECT * FROM posts";
        $result = $connection->query($query);

        // Display all messages
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" .$row['id'] ."</td>";
            echo "<td>" .$row['name'] ."</td>";
            echo "<td>" .$row['email'] ."</td>";
            echo "<td>" .$row['message'] ."</td>";
            echo "<td>" .$row['posted_at'] ."</td>";
            echo "<td>" .$row['ip_address'] ."</td>";
            echo "<td><button>Edit</button></td>";
            echo "<td><button>Delete</button></td>";
            echo "</tr>";
        }
    } catch (PDOException $e) {
        echo "Couldn't retrieve messages.";
    }
}

// If connection succeeds, display all messages
if ($connection) {
    // Set up table
    echo "<table>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Email</th>";
    echo "<th>Message</th>";
    echo "<th>Posted At</th>";
    echo "<th>IP Address</th>";
    echo "</tr>";
    displayMessages($connection);
    echo "</table>";
}
?>
</body>
</html>

