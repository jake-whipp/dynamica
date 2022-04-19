<!-- register.php
-----------------------

Register handler for dynamica
Handles the functionality of registering new accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


<!-- TODO: https://www.codingnepalweb.com/create-custom-captcha-in-javascript/ -->
<!-- or email method -->

<?php 
    include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs

    
    // Establish login details for sql. NOT username logon
    $servername = "localhost";
    $username = "root"; 
    $password = "root";
    $dbname = "dynamica";


    if (ValidateSession()) {
        header('Location: ' . "/dashboard.php");
        die();

    } else { // If user is NOT logged in:

        if (empty(SanitiseRequest($_POST["Username"]))) {
            header('Location: ' . "/index.php?error=1", true);
            die();
        } elseif (empty(SanitiseRequest($_POST["Password"]))) {
            header('Location: ' . "/index.php?error=2", true);
            die();
        } 
        
        // Check for alphanumerical characters
        $alnumUser = ctype_alnum(SanitiseRequest($_POST["Username"]));
    
        if (!($alnumUser)) {
            header('Location: ' . "/index.php?error=4", true);
            die();
        }
    
    
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
    
        // We need to secure the password with a cryptographic function
        $EncryptedPassword = md5(SanitiseRequest($_POST["Password"]));

        // Create a new account
        $sqlCreateNewAccount = "INSERT INTO users (Username, Password) VALUES ('" . SanitiseRequest($_POST["Username"]) . "', '" . $EncryptedPassword . "');";
    
        if ($conn->query($sqlCreateNewAccount) === TRUE) {
            echo "New account created successfully";
        } else {
            header('Location: ' . "/index.php?error=0", true);
            die();
        }

        // Get the new user's ID
        $sqlGetUserID = "SELECT ID FROM users WHERE Username='" . SanitiseRequest($_POST["Username"]) . "';";
        $result = $conn->query($sqlGetUserID);
        $row = $result->fetch_assoc();


        // Create related profile data
        $sqlAddProfile = "INSERT INTO profiles (ID) VALUES (" . $row["ID"] . ")";
        if ($conn->query($sqlAddProfile) === TRUE) {
            echo "New account created successfully";
        } else {
            header('Location: ' . "/index.php?error=0", true);
            die();
        }
    
        $conn->close();
    
        CreateSession("AUTHCOOKIE", SanitiseRequest($_POST["Username"]) . "_" . random_int(1000000, 2000000), time() + $timeout);
    
        header("Location: " . "/dashboard.php");
        die();
    }
?>