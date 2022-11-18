<html lang="en">
    <form action="index.php" method="post">
        <label for="firstname">Name: </label><br>
        <input type="text" id="firstname" name="firstname" value="John"><br>
        <label for="birthdate">Last name: </label><br>
        <input type="date" id="birthdate" name="birthdate" value="2000-01-01"><br>
        <input type="submit" value="Submit" name="SubmitButton">
    </form>
</html>

<?php
    $name = null;
    $birthdate = null;
    if (isset($_POST['SubmitButton'])) {
        if ($_POST['firstname'] !== null) {
            $name = $_POST['firstname'];
        }

        if ($_POST['birthdate'] != null) {
            try {
                $birthdate = strtotime($_POST['birthdate']);
                $birthdate = date('Y-m-d', $birthdate);
            }
            catch (Exception $e) {
                return null;
            }
        }
        echo '<p> Assignment Get Parameters From Form </p>';
        echo '<p>' . $name . " " . $birthdate . "\r\n" . '</p>';
    }

?>

<?php
    function getParameters($parameter) {
        if ($parameter == 'name') {
            // Check if both parameters exist
            if (isset($_GET['name'])) {
                return $_GET['name'];
            }
        }
        else if ($parameter == 'birthdate') {
            if (isset($_GET['birthdate'])) {
                try {
                    $birthdate = strtotime($_GET['birthdate']);
                    return date('Y-m-d', $birthdate);
                }
                catch (Exception $e) {
                    return null;
                }

            }
        }
        return null;
    }

    // Get values and convert string to date
    $name = getParameters('name');
    $birthdate = getParameters('birthdate');

    // For display only
    echo '<p> Assignment Get Parameters From URL </p>';
    // If both values are set
    if (!empty($name) && !empty($birthdate)) {
        echo '<p>' . $name . " " . $birthdate . '</p>';
    }
    else {
        echo '<p> Not all parameters are set. </p>';
    }
?>

<?php
echo "Requested URL: " . $_SERVER['REQUEST_URI'];
?>