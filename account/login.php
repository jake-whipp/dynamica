<!-- login.php
-----------------------

Login handler for dynamica
Handles the functionality of logging into user accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


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
        }

        if (empty(SanitiseRequest($_POST["Password"]))) {
            header('Location: ' . "/index.php?error=2", true);
            die();
        }

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
