<?php
    session_start();
    $config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/config.ini", true);
    $timeout = $config["auth"]["timeout"]; // how long can the user remain active

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

    function UpdateSession() {
        global $timeout;

        if (!isset($_SESSION["AUTHCREATED"])) {
            CreateSession("AUTHCOOKIE", (SanitiseRequest($_POST["Username"]) . "_" . random_int(1000000, 2000000)), (time() + $timeout));
        } else {
            session_regenerate_id(true);
            $_SESSION["AUTHCREATED"] = time();
            $_SESSION["AUTHEXPIRY"] = time() + $timeout;
        }
    }


    if (ValidateSession()) {
        UpdateSession();
    } else {
        DestroySession();
        header('Location: ' . "/dashboard.php?error=0");
        die();
    }
    

    // Establish login details for sql. NOT username logon
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

    // Find User ID that we need
    $MyUsername = substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_")); // Uses session for current username of logged in player
    $sqlGetUserID = "SELECT ID FROM users WHERE Username='" . $MyUsername . "'";
    $UserID = ($conn->query($sqlGetUserID))->fetch_assoc();

    // Create query to update bio
    $sqlUpdateBio = "UPDATE profiles SET Description='" . SanitiseRequest($_POST["Bio"]) . "' WHERE ID=" . $UserID["ID"];

    if ($conn->query($sqlUpdateBio) === TRUE) {
        echo "Bio updated successfully";
        header('Location: ' . "/dashboard.php?", true);
    } else {
        echo mysqli_error($conn);
        header('Location: ' . "/dashboard.php?error=0", true);
        die();
    }

    $conn->close();
    
?>