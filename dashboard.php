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
        <link rel="stylesheet" href="/styles/user_profile.css" />

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


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
                <input type="Submit" value="Home" class="HeaderElement" style="width:100px; height:50px; box-shadow: 0 1px 0 #fff;">
            </form>

            <form action="/browse.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Browse" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/upload/post.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Create" class="HeaderElement" style="width:100px; height:50px;">
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

        <center>
        <div>
            <div>
                <h1 style="font-family:sans-serif;">Hello, <?= substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_")) ?>!</h1> <!-- uses cookies for username -->
                <p style="font-family:Montserrat; font-size:14pt;">would you like to:</p>
            </div>

            <div class="ProfileContainer" style="width:380px; position:fixed; height: 250px; left:60px;">
                <form action="/users/uploadbio.php" method="post">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Update your bio</h1>
                        <p style="font-family:Montserrat; font-weight:300;">Let others know what you're up to</p>
                        <input id="BioBox" name="Bio" type="text" class="InputBox" style="outline:none;margin-top:20px;font-size:13pt;" placeholder="Enter a new bio here.." />
                        <input type="submit" value="Change" class="InputButton" style="border-radius:10px; width: 150px; margin-top:20px; font-family:Montserrat;"/>
                </form>
            </div>

            <div class="ProfileContainer" style="width:900px; display:inline-block;">
                <form action="/users/me.php">
                    <div class="PostContainer" style="width:500px; height:120px; margin-top:100px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">View your profile?</h1>
                        <input type="submit" value="Visit" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>
                
                <form action="/upload/post.php">
                    <div class="PostContainer" style="width:500px; height:120px; margin-top:50px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Create a post?</h1>
                        <input type="submit" value="Create" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>

                <form action="/browse.php">
                    <div class="PostContainer" style="width:500px; height:120px; margin-top:50px; margin-bottom:100px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Browse others' posts?</h1>
                        <input type="submit" value="Look" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>
            </div>
            

            
        </div>
        </center>
    </body>
</html>
