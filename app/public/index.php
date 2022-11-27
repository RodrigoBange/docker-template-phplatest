<?php
    // Hide all errors for the user
    error_reporting(0);
    // Start session and open buffer
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <link rel="stylesheet" href="css/stylesheet_index.css">
    <title>Guestbook</title>
</head>
<body class="background">
<?php
if (isset($_SESSION['username'])) {
    ?>
    <div id="userinfo">
        <p>Hello, <?php echo $_SESSION['username'] ?></p>
        <?php
        if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 1) {
            ?>
            <a href="management.php">Management</a>
            <?php
        }
        ?>
        <a href="index.php">Guestbook</a>
        <a href="login.php?logout=1">Log out</a>
    </div>
<?php
} else {
    ?>
    <div id="userinfo">
        <p>Hello, guest</p>
        <a href="login.php">Log in</a>
    </div>
<?php
}
?>
    <h1>Guestbook</h1>
    <form action="index.php" method="post" id="messageForm">
        <p>Post a message</p>
        <label for="name">Name: </label><br>
        <input type="text" id="name" name="name" value="" maxlength="64"><br>
        <label for="message">Message: </label><br>
        <textarea name="message" id="message" maxlength="255"></textarea><br>
        <input type="submit" value="Send message" name="SubmitButton">
    </form>

<?php
require_once("dbconfig.php");

// Set up connection and retrieve messages
try {
    $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    ?>
    <p class="message_failure">Connection failed: <?php echo $e->getMessage(); ?></p>
    <?php
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
            // Gets IP of current user, we're just going to blank that out for now...
            //$query->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
            $ipaddress = "0.0.0.0";
            $query->bindParam(':ip_address', $ipaddress);

            // Execute query
            $query->execute();

            ?>
                <p class="message_success">Message successfully posted!</p>
            <?php
        } catch (Exception $e) {
            ?>
                <p class="message_failure">Failed to post message. <?php echo $e->getMessage();?></p>
            <?php
        }
    }
}

function displayMessages($connection)
{
    // Get all messages from database
    try {
        $query = "SELECT * FROM posts";
        $result = $connection->query($query);

        // Display all messages
        foreach ($result as $row) {
            ?>
            <div id="messagebox">
                <p class="name"><?php echo $row['name']; ?></p>
                <p class="message"><?php echo nl2br($row['message']); ?></p>
                <p class="date"><?php echo $row['posted_at']; ?></p>
            </div>
            <?php
        }
    } catch (PDOException $e) {
        ?>
        <p class="message_failure">Could not retrieve messages.</p>
        <?php
    }
}

// If a connection is made, display all the messages
if ($connection) {
    displayMessages($connection);
}
?>
</body>
</html>
