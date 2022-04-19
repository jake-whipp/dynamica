<?php
    session_start();
    $config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/config.ini", true); // The path to the config.ini file
    $timeout = $config["auth"]["timeout"]; // how long can the user remain active

    function SanitiseRequest($data) { // This function will be used to "clean up" a user input. It is necessary in order to prevent threats like XSS.
        $data = trim($data); // Remove spaces
        $data = stripslashes($data); // Remove slashes
        $data = htmlspecialchars($data); // Convert certain characters into html entities

        return $data;
    }

    function CreateSession($auth_cookie_name, $auth_cookie_value, $auth_cookie_expiry) {
        $auth_cookie_created = time();

        setcookie($auth_cookie_name, $auth_cookie_value, $auth_cookie_expiry, "/"); // Accessed with superglobal $_COOKIE["AUTHCOOKIE"];


        // Declare session variables to create a server-client pair
        $_SESSION["AUTHID"] = $auth_cookie_value;
        $_SESSION["AUTHEXPIRY"] = $auth_cookie_expiry;
        $_SESSION["AUTHCREATED"] = $auth_cookie_created;
    }

    function DestroySession() {
        CreateSession("AUTHCOOKIE", "",(time() - 3600)); // Set cookie with expiry date in the past

        session_unset();
        session_destroy(); // Delete server-sided session
    }

    function ValidateSession() {
        if (isset($_SESSION["AUTHEXPIRY"])) {
            if (time() - $_SESSION["AUTHEXPIRY"] > 0) { // extra time has passed since the expiry date of the auth session
                
                DestroySession();

                return 0;
            } else { // if the auth session is still in-date
                if ($_COOKIE["AUTHCOOKIE"] == $_SESSION["AUTHID"]) {    // if the server session matches the client cookie 
                    return 1;   // return that the session is valid
                } else {
                    return 0; // else return invalid
                }
            }
        } else {
            return 0;
        }
    }

    function UpdateSession() {
        global $timeout;

        if (!isset($_SESSION["AUTHCREATED"])) { // if there is no auth session
            CreateSession("AUTHCOOKIE", (SanitiseRequest($_POST["Username"]) . "_" . random_int(1000000, 2000000)), (time() + $timeout)); // create a new session
        } else {
            session_regenerate_id(true); // regenerate the auth session and reset the countdown to its expiry
            $_SESSION["AUTHCREATED"] = time();
            $_SESSION["AUTHEXPIRY"] = time() + $timeout;
        }
    }


    if (ValidateSession()) { // if session is valid
        UpdateSession(); // refresh session
    } else {
        DestroySession();
        header('Location: ' . "/index.php"); // move user to main (login) page
        die();
    }
    
?>