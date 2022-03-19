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
        header('Location: ' . "/dynamica-main/index.php?error=1", true);
        die();
    }

    if (empty(SanitiseRequest($_POST["Password"]))) {
        header('Location: ' . "/dynamica-main/index.php?error=2", true);
        die();
    }

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "dynamica";


    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (Username, Password) VALUES ('" . SanitiseRequest($_POST["Username"]) . "', '" . SanitiseRequest($_POST["Password"]) . "')";

    if ($conn->query($sql) === TRUE) {
        echo "New account created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

    header("Location: " . "/dynamica-main/dashboard.php");
    die();
?>
