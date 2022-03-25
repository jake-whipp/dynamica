<!-- login.php
-----------------------

Login handler for dynamica
Handles the functionality of logging into user accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


<!--
    TODO:
    - Case sensitive checks in login
    - Auth cookie & expiry
-->


<?php
    function SanitiseRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    if (empty(SanitiseRequest($_POST["Username"]))) {
        header('Location: ' . "/index.php?error=1", true);
        die();
    }

    if (empty(SanitiseRequest($_POST["Password"]))) {
        header('Location: ' . "/index.php?error=2", true);
        die();
    }

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "dynamica";


    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if (mysqli_connect_error()) {
        header('Location: ' . "/index.php?error=0", true);
        die("Database connection failed: " . mysqli_connect_error());
    }


    // Run an SQL query e.g. "SELECT * FROM users WHERE Username='admin' AND Password='root'
    $sql = "SELECT * 
            FROM users 
            WHERE Username='" . SanitiseRequest($_POST["Username"]) . "' 
            AND BINARY Password='" . SanitiseRequest($_POST["Password"]) . "'"; // BINARY enables case-sensitive search
    

    // Store results in a variable
    $result = $conn->query($sql);

    if ($result != false) {
        if ($result->num_rows != 1) {
            header('Location: ' . "/index.php?error=3", true);
            die();
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    

    $conn->close();

    header("Location: " . "/dashboard.php");
    die();
?>
