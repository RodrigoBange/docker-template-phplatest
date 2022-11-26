<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assignment 02</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
    <h1>Guestbook</h1>
    <form action="index.php" method="post" id="messageform">
        <p>Post a message:</p>
        <label for="name">Name: </label><br>
        <input type="text" id="name" name="name" value=""><br>
        <label for="message">Message: </label><br>
        <textarea name="message" id="message"></textarea><br>
        <input type="submit" value="Send message" name="SubmitButton">
    </form>
<?php
require_once("dbconfig.php");

// Set up connection and retrieve messages
try {
    $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful.";
    displayMessages($connection);
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
            echo "<div id='messagebox'>";
            echo "<p class='name'>" .$row['name'] ."</p>";
            echo nl2br("<p class='message'>" .$row['message'] ."</p>");
            echo "<p class='date'>" .$row['posted_at'] ."</p>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Couldn't retrieve messages.";
    }
}

function displayNewestMessage($connection)
{
    // Get the newest message from the database
    try {
        $query = "SELECT * FROM `posts` ORDER BY id DESC LIMIT 1;";
        // Returns array of objects, but we only need one, so grab the single row
        $result = $connection->query($query);
        $row = $result->fetch(PDO::FETCH_ASSOC);

        // Display the new message
        echo "<div id='messagebox'>";
        echo "<p class='name'>" .$row['name'] ."</p>";
        echo nl2br("<p class='message'>" .$row['message'] ."</p>");
        echo "<p class='date'>" .$row['posted_at'] ."</p>";
        echo "</div>";

    } catch (PDOException $e) {
        echo "Couldn't retrieve the newest message.";
    }
}

if (isset($_POST['SubmitButton'])) {
    // Filter POST input (1st method of filtering)
    //$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    //$name = $_POST['name'];
    //$message = $_POST['message'];

    // If values have been entered
    if ($_POST['name'] != null && $_POST['message'] != null) {
        // (Second method of filtering)
        $name = htmlspecialchars($_POST['name']);
        $message = htmlspecialchars($_POST['message']);

        try {
            // Set up query
            $query = $connection->prepare("INSERT INTO posts
                                        (name, message, posted_at, ip_address)
                                        VALUES
                                        (:name, :message, now(), :ip_address)");

            // Bind values to parameters
            $query->bindParam(':name', $name);
            $query->bindParam('message', $message);
            // Gets IP of current user, we're just going to blank that for now...
            //$query->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
            $ipaddress = "0.0.0.0";
            $query->bindParam(':ip_address', $ipaddress);

            // Execute query
            $query->execute();

            // Refresh messages
            displayNewestMessage($connection);
        } catch (Exception $e) {
            echo "Failed to post message" . $e->getMessage();
        }
    }
}
?>
</body>
</html>
