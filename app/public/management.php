<?php
    // Hide all errors for the user
    error_reporting(0);

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
<body class="background">
<div id="userinfo">
    <p>Hello, <?php echo $_SESSION['username'] ?></p>
    <a href="management.php">Management</a>
    <a href="index.php">Guestbook</a>
    <a href="login.php?logout=1">Log out</a>
</div>
<h1>Post Management</h1>

<?php
require_once("dbconfig.php");

// Set up connection and retrieve messages
try {
    $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    ?>
    <p>Connection failed: <?php echo $e->getMessage(); ?></p>
    <?php
}

function displayMessages($connection)
{
    // Get all messages from database
    try {
        $query = "SELECT * FROM posts";
        $result = $connection->query($query);

        // Display all messages
        foreach ($result as $row) {
            $id = $row['id'];
            ?>
            <tr>
                <td><?php echo $row['id']; ?> </td>
                <td><?php echo $row['name']; ?> </td>
                <td><?php echo $row['email']; ?> </td>
                <td><?php echo $row['message']; ?> </td>
                <td><?php echo $row['posted_at']; ?> </td>
                <td><?php echo $row['ip_address']; ?> </td>
                <td><a href="updatemessage.php?id=<?php echo $id ?>" class="btn-edit">Edit</a></td>
                <td>
                    <a href="management.php?deleteId=<?php echo $id ?>"
                        onclick="return confirm('Are you sure you want to delete the post?')"
                        class="btn-delete">
                        Delete
                    </a>
                </td>
            </tr>
            <?php
        }
    } catch (PDOException $e) {
        ?>
        <p class="message_failure">Could not retrieve the posts. <?php echo $e->getMessage(); ?></p>
        <?php
    }
}

if (isset($_GET['deleteId'])) {
    // Filter
    $id = htmlspecialchars($_GET['deleteId']);

    // Delete post from database
    try {
        $statement = $connection->prepare("DELETE FROM posts WHERE id=:id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        ?>
        <p class="message_success">Successfully deleted post.</p>
        <?php
    } catch (Exception $e) {
        ?>
        <p class="message_failure">An error occurred deleting the post. <?php echo $e->getMessage(); ?></p>
        <?php
    }
}

// If connection succeeds, display all messages
if ($connection) {
    ?>
    <div id="tableContainer">
    <table>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Posted At</th>
            <th>Ip Address</th>
            <th></th>
            <th></th>
        </tr>
        <?php
        displayMessages($connection);
        ?>
    </table>
    </div>
    <?php
}
?>
</body>
</html>

