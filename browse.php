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

        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <!-- linking JQuery module and scripts -->
        <script src="/js/jquery-functions.js"></script>

        <?php
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

                $posts_per_page = 5;

                if (isset($_GET["page"])) {
                    $page = SanitiseRequest($_GET["page"]);
                } else {
                    $page = 0;
                }

                // Count posts to decide how many pages are required
                $sqlCountPosts = "SELECT COUNT(ID) AS total FROM posts";
                $countResult = $conn->query($sqlCountPosts);

                $count = $countResult->fetch_assoc(); // first row, identifying the count
                $number_of_pages = ceil($count["total"] / $posts_per_page); // ceil == round up


                // Get the latest post information
                $sqlGetPosts = "SELECT * FROM posts ORDER BY ID DESC LIMIT " . ($page * $posts_per_page) . "," . $posts_per_page . ";";
                $postsResult = $conn->query($sqlGetPosts);


                // Get associated profile information
                $sqlGetProfiles = "SELECT * FROM profiles WHERE ID IN (SELECT UserID FROM posts ORDER BY ID DESC)";
                $profilesResult = $conn->query($sqlGetProfiles);
        ?>

    </head>

    <body>
        <div class="Header" style="overflow:hidden;">
            <h1 class="HeaderLogo" style="margin-right:20px; float:left;">Dynamica</h1>

            <form action="/dashboard.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Home" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/browse.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Browse" class="HeaderElement" style="width:100px; height:50px; box-shadow: 0 1px 0 #fff;">
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
                <h1 style="font-family:sans-serif;">Browse</h1> <!-- uses cookies for username -->

            </div>
        </div>
        </center>

        <div class="ProfileContainer" id="ProfilePostContainer" style="width:1050px; height:740px; position:relative; left:50%; transform: translate(-50%, 0%);">
            <div class="PostContainer" id="PostConstructor" style="width:95%; height:100px; position:relative; left:50%; transform: translate(-50%, 0%); margin-top:20px;">
                <div class="PostPhoto" style="width:80px; height:50%; display:inline-block; vertical-align:middle; margin-right:20px; margin-left:25px; position:fixed; top:10px">
                    <img src="" style="object-fit: cover; width: 100%; height:80px; position:relative;border-radius:25px;"/>
                </div>

                <div class="PostContent" style="color:white; display:inline-block; padding-left:125px; width:80%; font-family:Montserrat;">
                    <a class="PostUsernameLink"><p class="PostUsername" style="display:inline-block; font-weight:800;">dynamica,</p></a>
                    <p style="display:inline-block;">at</p>
                    <p class="PostDate" style="display:inline-block; font-weight:800;">17/04/2022</p>

                    <p class="PostBodyText" style="margin-top:0;">200 maximum characters in this spacing</p>
                </div>
            </div>

        <script>
            var profile_element = $(".PostContainer");

            <?php
                while($postRow = $postsResult->fetch_assoc()) {  // Step through all of the post records gathered in the <head> of page
                    $sqlGetProfile = "SELECT * FROM profiles WHERE ID=" . $postRow["UserID"];
                    $profileResult = ($conn->query($sqlGetProfile))->fetch_assoc();
                   
                    $sqlGetUsername = "SELECT Username FROM users WHERE users.ID=" . $postRow["UserID"];
                    $usernameResult = ($conn->query($sqlGetUsername))->fetch_assoc();
                    ?>

                    var $clone = profile_element.clone();
                    $clone.attr("id", "<?= $postRow["ID"] ?>");
                    $clone.children("div.PostContent").children("a.PostUsernameLink").attr("href", "/users/profile.php?ID=<?=$postRow["UserID"]?>");
                    $clone.children("div.PostContent").children("a.PostUsernameLink").children("p.PostUsername").text("<?= $usernameResult["Username"] ?>"); // edit
                    

                    $clone.children("div.PostContent").children("p.PostDate").text("<?=$postRow["Created"] ?>"); // edit
                    $clone.children("div.PostContent").children("p.PostBodyText").text("<?=$postRow["Content"] ?>");

                    $clone.children("div.PostPhoto").children("img").attr("src", "<?=$profileResult["Photosrc"]?>");


                    $clone.appendTo("#ProfilePostContainer");
            <?php } ?>

            $("#PostConstructor").remove();
        </script>


        <center>
            <?php
                $LeftNavigationEnabled = ""; // These two variables will act as the style tags for navigation buttons in html
                $RightNavigationEnabled = "";

                if(($page - 1) < 0) {
                    $LeftNavigationEnabled = "display:none;"; // hide button
                }

                if(($page + 1) > $number_of_pages - 1) {
                    $RightNavigationEnabled = "display:none;"; // hide button
                }

            ?>

            <a href="browse.php?page=<?=$page - 1;?>" class="NavigationLink">
                <button style="<?= $LeftNavigationEnabled ?>" class="NavigationButton"><</button>
            </a>

            <a href="browse.php?page=<?=$page + 1;?>" class="NavigationLink">
                <button style="<?= $RightNavigationEnabled ?>" class="NavigationButton">></button>
            </a>
        </center>
        </div>
    </body>
</html>
