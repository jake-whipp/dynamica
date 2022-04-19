<!-- login.php
-----------------------

Login handler for dynamica
Handles the functionality of logging into user accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->



<?php 

    include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs

    // Establish login details for sql. NOT username logon
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "dynamica";


    if (ValidateSession()) { // if user is logged in
        header('Location: ' . "/dashboard.php");
        die();

    } else { // If user is NOT logged in:

        if (empty(SanitiseRequest($_POST["Username"]))) { // if no username given
            header('Location: ' . "/index.php?error=1", true); // redirect with error code 1
            die();
        }

        if (empty(SanitiseRequest($_POST["Password"]))) { // if no password given
            header('Location: ' . "/index.php?error=2", true); // redirect with error code 2
            die();
        }

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if (mysqli_connect_error()) {
            header('Location: ' . "/index.php?error=0", true);
            die("Database connection failed: " . mysqli_connect_error());
        }


        // Convert user's entered password into encrypted format when comparing to the database
        $EncryptedPassword = md5(SanitiseRequest($_POST["Password"]));

        // Run an SQL query e.g. "SELECT * FROM users WHERE Username='admin' AND Password='root'
        $sql = "SELECT * 
                FROM users 
                WHERE Username='" . SanitiseRequest($_POST["Username"]) . "' 
                AND BINARY Password='" . $EncryptedPassword . "'"; // BINARY enables case-sensitive search
        

        // Store results in a variable
        $result = $conn->query($sql);

        if ($result != false) {
            if ($result->num_rows != 1) {
                header('Location: ' . "/index.php?error=3", true);
                die();
            }
        } else {
            header('Location: ' . "/index.php?error=0", true);
            die();
        }
        

        // If everything has worked well, and the page is not already dead, we need to set a new auth session
        CreateSession("AUTHCOOKIE", (SanitiseRequest($_POST["Username"]) . "_" . random_int(1000000, 2000000)), time() + $timeout);
        $conn->close();

        header("Location: " . "/dashboard.php");
        die();
    }
?>
