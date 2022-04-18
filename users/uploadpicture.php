


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
        header('Location: ' . "/index.php");
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

    // Run an SQL query to get the username we need (for naming the file)
    $MyUsername = substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_"));
    
    $sqlGetUID = "SELECT ID FROM users WHERE Username='" . $MyUsername . "';";
    $UserID = ($conn->query($sqlGetUID))->fetch_assoc();



    header("Content-Type: text/plain; charset=utf-8");

    try {
        if (is_array($_FILES["fileToUpload"]["error"])) {
            throw new RuntimeException(1);
        }

        switch ($_FILES["fileToUpload"]["error"]) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(2);
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(3);
            default:
                throw new RuntimeException(1);
        }

        // Check the file size against 100kb so that big images dont fill up my local disk
        if ($_FILES["fileToUpload"]["size"] > 1000000) {
            throw new RuntimeException(3);
        }

        // Must check the file type, to make sure user doesn't have invalid profile photo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tmpName = $finfo->file($_FILES["fileToUpload"]["tmp_name"]); // the temporary name of the file ie "text/plain" if .txt or .ini, etc.

        $AllowedTypes = array(
            "png" => "image/png",
            "jpg" => "image/jpeg",
        );

        $FileExtension = array_search($tmpName, $AllowedTypes, true); // 3rd argument "true" means strict. Will return false if the file provided is not in our allowed list

        if (false === $FileExtension) {
            throw new RuntimeException(4);
        }


        // Move the file to the right folder from its temporary storage
        // Use the name of the username so that it is organised and secure (no breaches by insecure file name upload)
        $FileDirectory = $_SERVER["DOCUMENT_ROOT"] . "users/icons/";
        $NewFile = $FileDirectory . $UserID["ID"] . "." . $FileExtension; // ie "C:/.../users/icons/1.png"

        // Try to move file, if doesn't work, throw exception
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $NewFile)) {
            throw new RuntimeException(5);
        }

        // Assuming no errors up to this point, we are now okay to update the databse
        $sqlUpdateProfilesrc = "UPDATE profiles SET Photosrc='/users/icons/" . $UserID["ID"] . "." . $FileExtension . "' WHERE ID=" . $UserID["ID"];
        if ($conn->query($sqlUpdateProfilesrc) === TRUE) {
            header("Location: /users/profile.php?ID=" . $UserID["ID"] . "&error=none");
        } else {
            echo mysqli_error($conn);
            //throw new RuntimeException(5);
        };


    } catch (RuntimeException $Error) {
        echo $Error->getMessage();
        header("Location: /users/profile.php?ID=" . $UserID["ID"] . "&error=" . $Error->getMessage());
    }

?>