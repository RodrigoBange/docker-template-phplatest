<?php
    // Hide all errors for the user
    error_reporting(0);
    // Start session and open buffer
    session_start();
    ob_start();

    if (isset($_GET['logout']) && $_GET['logout'] == 1) {
        // Remove all set variables (Keeps the session)
        session_unset();

        // Destroy the session (Deletes all data)
        session_destroy();
    }

    if (isset($_SESSION['username'])) {
        if ($_SESSION['usertype'] == 0) {
            header('Location: index.php');
        } else {
            header('Location: management.php');
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <link rel="stylesheet" href="css/stylesheet_login.css">
    <title>Login page</title>
</head>
<body class="background">
<h1>Login</h1>
<form action="login.php" method="post" id="loginForm">
    <label for="username">username: </label><br>
    <input type="text" id="username" name="username" maxlength="64"><br>
    <label for="password">password: </label><br>
    <input type="password" id="password" name="password" maxlength="255"><br>
    <input type="submit" value="Log in" name="LoginButton">
</form>
<div id="userinfo">
    <p>Visiting?</p>
    <a href="index.php">Guestbook</a>
</div>
<?php
    require_once("dbconfig.php");

    if (isset($_POST['LoginButton'])) {
        if ($_POST['username'] != null && $_POST['password'] != null) {
            // Filter
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);

            // Set up connection and retrieve messages
            try {
                $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                ?>
                <p class="message_failure">Connection failed: <?php echo $e->getMessage(); ?></p>
                <?php
            }

            try {
                // Create query and get single row
                $statement = $connection->prepare("SELECT password, isAdmin FROM users WHERE username=:username");
                $statement->bindParam(':username', $username);
                $statement->execute();
                // Fetches one row and get password
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $hashedPassword = $result['password'];
                $usertype = $result['isAdmin'];

                if (password_verify($password, $hashedPassword)) {
                    // Save values to session
                    $_SESSION['username'] = $username;
                    $_SESSION['usertype'] = $usertype;
                    if ($usertype == 0) { // If user...
                        header("Location: index.php");
                        exit();
                    } elseif ($usertype == 1) { // If admin...
                        header("Location: management.php");
                        exit();
                    }
                } else {
                    ?>
                    <p class="message_failure">Invalid user credentials. Please try again.</p>
                    <?php
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            ?>
            <p class="message_failure">Please fill in all fields.</p>
            <?php
        }
        // Output buffer
        ob_end_flush();

        // Hashes a password (For future reference)
        //$hash = password_hash($password, PASSWORD_DEFAULT);
        //echo $hash;
    }
?>
</body>
</html>
