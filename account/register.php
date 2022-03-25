<!-- register.php
-----------------------

Register handler for dynamica
Handles the functionality of registering new accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


<!-- TODO: https://www.codingnepalweb.com/create-custom-captcha-in-javascript/ -->
<!-- or email method -->

<?php
    session_start();
    $timeout = 900; // how long can the user remain active

    function SanitiseRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

    return $data;
    }

    function CreateSession($auth_cookie_name, $auth_cookie_value, $auth_cookie_expiry) {
        global $timeout;
        $auth_cookie_created = time();

        setcookie($auth_cookie_name, $auth_cookie_value, $auth_cookie_expiry, "/"); // Accessed with superglobal $_COOKIE["AUTHCOOKIE"];


        // Declare session variables to create a server-client pair
        $_SESSION["AUTHID"] = $auth_cookie_value;
        $_SESSION["AUTHEXPIRY"] = $auth_cookie_expiry;
        $_SESSION["AUTHCREATED"] = $auth_cookie_created;
    }

    function DestroySession() {
        CreateSession("AUTHCOOKIE", "",(time() - 3600));

        session_unset();
        session_destroy();
    }

    function ValidateSession() {
        if (isset($_SESSION["AUTHEXPIRY"])) {
            if (time() - $_SESSION["AUTHEXPIRY"] > 0) {
                // extra time has passed since the expiry date of the auth session
                DestroySession();

                return 0;
            } else {
                if ($_COOKIE["AUTHCOOKIE"] == $_SESSION["AUTHID"]) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }



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
    
        // Create a new account
        $sqlCreateNewAccount = "INSERT INTO users (Username, Password) VALUES ('" . SanitiseRequest($_POST["Username"]) . "', '" . SanitiseRequest($_POST["Password"]) . "');";
    
        if ($conn->query($sqlCreateNewAccount) === TRUE) {
            echo "New account created successfully";
        } else {
            header('Location: ' . "/index.php?error=0", true);
            die();
        }
    
        $conn->close();
    
        CreateSession("AUTHCOOKIE", SanitiseRequest($_POST["Username"]), time() + $timeout);
    
        header("Location: " . "/dashboard.php");
        die();
    }
?>
