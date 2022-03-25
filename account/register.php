<!-- register.php
-----------------------

Register handler for dynamica
Handles the functionality of registering new accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


<!-- TODO: https://www.codingnepalweb.com/create-custom-captcha-in-javascript/ -->
<!-- or email method -->

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
    } elseif (empty(SanitiseRequest($_POST["Password"]))) {
        header('Location: ' . "/index.php?error=2", true);
        die();
    } 
    
    $alnumUser = ctype_alnum(SanitiseRequest($_POST["Username"]));

    if (!($alnumUser)) {
        header('Location: ' . "/index.php?error=4", true);
        die();
    }

    $servername = "localhost";
    $username = "root"; // login details for sql. NOT username logon
    $password = "root";
    $dbname = "dynamica";


    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for existing user
    $sqlSearchExistingAccounts = "SELECT * FROM users WHERE Username='" . SanitiseRequest($_POST["Username"]) . "';";
    $result = $conn->query($sqlSearchExistingAccounts);

    if ($result->num_rows > 0) {
        header('Location: ' . "/index.php?error=5", true);
        die();
    }

    // Create a new account
    $sqlCreateNewAccount = "INSERT INTO users (Username, Password) VALUES ('" . SanitiseRequest($_POST["Username"]) . "', '" . SanitiseRequest($_POST["Password"]) . "');";

    if ($conn->query($sqlCreateNewAccount) === TRUE) {
        echo "New account created successfully";
    } else {
        header('Location: ' . "/index.php?error=0", true);
        die();
    }

    $conn->close();

    header("Location: " . "/dashboard.php");
    die();
?>
