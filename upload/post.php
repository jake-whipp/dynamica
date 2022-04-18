<!-- dashboard.php
-----------------------

Main page for dynamica.
Serves as the home page a user will see after logging in.

Dynamica - Jacob Whipp (2022).

-->


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
        header('Location: ' . "/index.php?error=1");
        die();
    }
    
?>

<html>
    <head>
        <!-- Link to style sheet -->
        <link rel="stylesheet" href="/styles/index.css" /> 
        <link rel="stylesheet" href="/styles/main.css" /> 
        <link rel="stylesheet" href="/styles/post.css" />


        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <!-- linking JQuery module and scripts -->
        <script src="js/jquery-functions.js"></script>
    </head>

    <body>
        <div class="Header" style="overflow:hidden;">
            <h1 class="HeaderLogo" style="margin-right:20px; float:left;">Dynamica</h1>

            <form action="/dashboard.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Home" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/browse.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Browse" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/upload/post.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Create" class="HeaderElement" style="width:100px; height:50px; box-shadow: 0 1px 0 #fff;">
            </form>

            <form action="/users/search.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Users" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/users/me.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Profile" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/account/logout.php" class="HeaderElement" style="display:inline-block; float:right; left:-30px;">
                <input type="Submit" value="Logout" class="HeaderElement" style="width:100px; height:50px; position:relative;"/>
                <img src="/logout.png" style="position:relative; right:25px; bottom:36px;"/>
            </form>
        </div>

        <div>
            <div class="Container" style="width:40%; margin-top:50px; position:relative; left:50%; transform:translate(-50%, 0%)">
                <center><h1 style="font-family:sans-serif;">Create a post</h1></center>
            </div>

            <div class="Container" style="width:40%; height:500px; margin-top:30px; position:relative; left:50%; transform:translate(-50%, 0%)">
                <h1 style="font-family:sans-serif; margin-left:30px; margin-top:30px;">Content</h1>
                <hr style="width:93%;"/>
                <p style="font-family:Montserrat; margin-left:30px; margin-right:30px;">
                    Your post can have up to 200 characters. Make sure not to post anything offensive, as it can be seen by others and is subject to punishment.
                </p>

                

                <form method="post" action="/upload/create.php" id="PostForm">

                    <center><textarea id="PostContentBox" name="Content" cols="50" rows="5" form="PostForm" style="margin-top:50px; width:66%;"></textarea></center>

                    <script>
                        $('#PostContentBox').keyup( function() { // Prevents usage of linedowns in post content
                            $(this).val( $(this).val().replace( /\r?\n/gi, '' ) );
                        });
                    </script>

                    <div style="margin-top:50px;">
                        <center><input type="submit" value="Post" class="InputButton" style="font-family:Montserrat; font-size:18pt; width:150px; height:50px;"/></center>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>