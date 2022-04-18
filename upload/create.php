<?php
    // Create functions, validate sessions, etc. -----------------------------
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
        header('Location: ' . "/index.php");
        die();
    }
    
?>



<?php // Handle post request -----------------------------
    $Content = SanitiseRequest($_POST["Content"]);

    if (strlen($Content) > 200) {
        header('Location: ' . "/upload/post.php?error=1", true);
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
    if (mysqli_connect_error()) {
        header('Location: ' . "/dashboard.php", true);
        die();
    }

    // Get the UserID of post creator
    echo "SELECT ID FROM users WHERE Username='" . (substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_"))) . "';";

    $sqlGetUserID = "SELECT ID FROM users WHERE Username='" . (substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_"))) . "';";
    $UserID = ($conn->query($sqlGetUserID))->fetch_assoc();

    // Run an SQL query
    $sqlCreatePost = "INSERT INTO posts (Content, UserID) VALUES ('". $Content . "','" . $UserID["ID"] . "')";
    
    if ($conn->query($sqlCreatePost) === TRUE) {
        header('Location: ' . "/users/me.php", true);
        die();
    } else {
        header('Location: ' . "/upload/post.php?error=2", true);
        die();
    }
?>