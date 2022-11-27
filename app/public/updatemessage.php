<?php
    session_start();
    // Check privileges
    if ($_SESSION['usertype'] != 1) {
        echo "You are not privileged to enter this page.";
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <link rel="stylesheet" href="css/stylesheet_management.css">
    <title>Assignment 03</title>
</head>
<body>
<h1>Manage post</h1>

<?php
require_once("dbconfig.php");

// Retrieve data
if (isset($_GET['id'])) {
    // Filter value
    $id = htmlspecialchars($_GET['id']);

    // Set up connection and retrieve messages
    try {
        $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        ?>
        <p class="message_failure">Connection failed: <?php echo $e->getMessage(); ?></p>
        <?php
    }

    function createForm($connection, $id)
    {
        try {
            // Create query and get single row
            $statement = $connection->prepare("SELECT * FROM posts WHERE ID=:id");
            $statement->bindParam(':id', $id);
            $statement->execute();
            // Fetches one row
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            ?>
            <p class="message_failure">Could not retrieve information. <?php echo $e->getMessage(); ?></p>
            <?php
        }

        if ($result != null) {
            ?>
            <form method="post" id="postform">
                <p>Edit message</p>
                <label for="id">Poster Id: <?php echo $result['id']; ?></label><br>
                <label for="name">Poster Name: <?php echo $result['name']; ?></label><br>
                <label for="email">Poster Email: <?php echo $result['email']; ?></label><br>
                <label for="ipaddress">Poster Ip Address: <?php echo $result['ip_address']; ?></label><br>
                <label for="message">Message: </label><br>
                <textarea name="message" id="message" maxlength="255"><?php echo $result['message']; ?></textarea><br>
                <input type="submit" value="Edit Message" name="EditButton">
            </form>
            <?php
        } else {
            ?>
            <p class="message_failure">No record found.</p>
            <?php
        }
    }

    if (isset($_POST['EditButton'])) {
        if ($_POST['message'] != null) {
            $message = htmlspecialchars($_POST['message']);

            try {
                // Create query and update value
                $statement = $connection->prepare("UPDATE posts SET message=:message WHERE id=:id");
                $statement->bindParam(':id', $id);
                $statement->bindParam(':message', $message);
                $statement->execute();

                ?>
                <p class="message_success">Successfully updated post.</p>
                <?php
            } catch (Exception $e) {
                ?>
                <p class="message_failure">An error occurred updating the post. <?php echo $e->getMessage(); ?></p>
                <?php
            }
        }
    }

    // If a connection is made, create the form
    if ($connection) {
        createForm($connection, $id);
    }
}
?>
<a href="management.php">Return</a>
</body>
</html>
