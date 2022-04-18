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
        <script src="js/jquery-functions.js"></script>

        <?php
            if (isset($_GET["ID"])) {
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

                // Run an SQL query
                $sqlGetProfile = "SELECT * FROM profiles WHERE ID=" . SanitiseRequest($_GET["ID"]) . ";";

                // Store results in a variable
                $GetProfileResult = $conn->query($sqlGetProfile);

                if ($GetProfileResult != false) {
                    if ($GetProfileResult->num_rows != 1) {
                        header('Location: ' . "/dashboard.php", true);
                        die();
                    }
                } else {
                    header('Location: ' . "/dashboard.php", true);
                    die();
                }

                $ProfileDetails = $GetProfileResult->fetch_assoc(); // First row


                // Find the Username from related users table
                $sqlFindUsername = "SELECT Username FROM users WHERE users.ID=" . $ProfileDetails["ID"];
                $ProfileUsername = ($conn->query($sqlFindUsername))->fetch_assoc();


                $posts_per_page = 4;

                if (isset($_GET["page"])) {
                    $page = SanitiseRequest($_GET["page"]);
                } else {
                    $page = 0;
                }

                // Get user's latest post information
                $sqlGetPosts = "SELECT * FROM posts WHERE posts.UserID = " . $ProfileDetails["ID"] . " ORDER BY ID DESC LIMIT " . ($page * $posts_per_page) . "," . $posts_per_page . ";";
                $postsResult = $conn->query($sqlGetPosts);

                // Count posts to decide how many pages are required
                $sqlCountPosts = "SELECT COUNT(ID) FROM (SELECT * FROM posts WHERE posts.UserID = " . $ProfileDetails["ID"] . ") AS total;";
                $countResult = $conn->query($sqlCountPosts);

                $count = $countResult->fetch_assoc(); // first row, identifying the count
                $number_of_pages = ceil($count["COUNT(ID)"] / $posts_per_page); // ceil == round up

            } else {
                header('Location: ' . "/dashboard.php");
                die();
            }
        ?>
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
                <input type="Submit" value="Create" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/users/search.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Users" class="HeaderElement" style="width:100px; height:50px;">
            </form>

            <form action="/users/me.php" class="HeaderElement" style="display:inline-block;">
                <input type="Submit" value="Profile" class="HeaderElement" style="width:100px; height:50px; box-shadow: 0 1px 0 #fff;">
            </form>

            <form action="/account/logout.php" class="HeaderElement" style="display:inline-block; float:right; left:-30px;">
                <input type="Submit" value="Logout" class="HeaderElement" style="width:100px; height:50px; position:relative;"/>
                <img src="/logout.png" style="position:relative; right:25px; bottom:36px;"/>
            </form>
        </div>

        <?php
            $ErrorTypes = array(
                "1" => "Unexpected errors have occurred, please try again later",
                "2" => "You need to include a file!",
                "3" => "Your file is too large!",
                "4" => "Sorry, we only allow files of .PNG or .JPG format",
                "5" => "An internal error has occurred, please try again later",
                "none" => "Photo uploaded successfully.",
            );

            if (isset($_GET["error"])) {
                $Error = SanitiseRequest($_GET["error"]);

                if (!(array_search($Error, $ErrorTypes)) == true) {
                    if ($_GET["error"] == "none") {
                        $ErrorText = $ErrorTypes[$Error];
                        $ErrorToDisplay = true;
                        $SuccessToDisplay = true;
                    } else {
                        $ErrorToDisplay = true;
                        $SuccessToDisplay = false;
                        $ErrorText = $ErrorTypes[$Error];
                    }
                } else {
                    $ErrorToDisplay = false;
                }
            }
        ?>

        <div class="ErrorContainer" style="width:700px; height:50px; margin-top:8px; position:fixed; left:50%; transform:translate(-50%, 0%);">
            <center><p style="font-family:Montserrat; font-weight:900;"><?php global $ErrorText; echo $ErrorText?></p></center>
        </div>

        <script>
            <?php 
                global $ErrorToDisplay;
                global $SuccessToDisplay;

                    if ($ErrorToDisplay == false) { ?>
                        $(".ErrorContainer").remove();
            <?php   }
            
                    if ($SuccessToDisplay == true) { ?>
                        $(".ErrorContainer").css("background-color", "#a0ebb4"); 
                        $(".ErrorContainer").css("border", "3px solid rgb(125, 222, 140)"); 
            <?php   } else {
                echo "what";
            }
            ?>
        </script>

        <div style="position:relative;">
            <div class="ProfileContainer" id="IDTag" style="width:500px; height:200px; margin-left:150px; margin-top:70px; margin-right:60px; display:inline-block;">
                <div style="position:relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    <div style="width:30%; height:80%; display:inline-block; vertical-align:middle; margin-right:20px; margin-left:25px; background-size: cover; ">
                        <img class="MyProfilePhoto" src="<?= $ProfileDetails["Photosrc"] ?>" style="display:inline-block; object-fit: cover; width: 100%; height:160px; position:relative;border-radius:25px;"/>
                    </div>
                        
                    <div style="display:inline-block;">
                        <h1 style="font-family:Montserrat; color:black; display:inline-block;" id="UsernameTag"> <?= $ProfileUsername["Username"] ?></h1>

                        <script> // It is necessary to apply an equation to scale the text size based on number of username characters, so that it fits inside the div.
                            let numChars = $("#UsernameTag").text().length;

                            let x = 5 // Constant scaling factors
                            let y = 0.6

                            let textSize = (1/(y*numChars + x) * $("#IDTag").width() * Math.pow(0.96, (y*numChars + x)));

                            $("#UsernameTag").css("font-size", textSize);
                        </script>

                        <h3 style="font-family:Montserrat; font-size:16pt; color:black; font-weight:300; display:inline-block; margin-left:10px;">
                            ID #<?= $ProfileDetails["ID"] ?>
                        </h3>

                        <?php
                            $MyUsername = strtolower(substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_")));

                            if ($ProfileUsername["Username"] == $MyUsername) {
                                $AllowEdits = true;
                            }
                        ?>


                        <form action="/users/uploadpicture.php" method="post" enctype="multipart/form-data" id="EditForm">
                            <input type="file" name="fileToUpload" id="fileToUpload" style="position:fixed; bottom:60px; left:40%;"/>
                            <input type="submit" value="Change photo" name="submit" class="InputButton EditButton"/>
                        </form>

                        <script>// use php within javascript to check if $AllowEdits = true
                            if (!<?php global $AllowEdits; echo $AllowEdits ? "true" : "false";?> == true) { // php does not display bool false as string "false" therefore we need it to echo string "false"
                                $("#EditForm").remove()
                                
                            } else {
                                $(".MyProfilePhoto").css("bottom", "5%"); // fix profile photo position to match edit button
                            }
                        </script>
                    </div>

                    <?php
                        
                    ?>
                </div>
            </div>

            <div class="ProfileContainer" id="ProfilePostContainer" style="width:1050px; height:700px; margin-top: 70px; display:inline-block; position:fixed;">
                <h1 style="font-family:Montserrat; font-size:30pt; color:black; display:inline-block; margin-left:35px;">posts</h1>
                <hr style="width:90%"/>
                <br/>

                <div class="PostContainer" id="PostConstructor" style="width:95%; height:100px; position:relative; left:50%; transform: translate(-50%, 0%);">
                    <div class="PostPhoto" style="width:80px; height:80%; display:inline-block; vertical-align:middle; margin-right:20px; margin-left:25px; position:fixed; top:10%">
                        <img src="<?= $ProfileDetails["Photosrc"] ?>" style="object-fit: cover; width: 100%; height:100%; position:relative; border-radius:25px;"/>
                    </div>

                    <div class="PostContent" style="color:white; display:inline-block; padding-left:125px; width:80%; font-family:Montserrat;">
                        <p class="PostUsername" style="display:inline-block; font-weight:800;">dynamica,</p>
                        <p style="display:inline-block;">at</p>
                        <p class="PostDate" style="display:inline-block; font-weight:800;">17/04/2022</p>

                        <p class="PostBodyText" style="margin-top:0;">200 maximum characters in this spacing</p>
                    </div>
                </div>

                <script>
                    var profile_element = $(".PostContainer");

                    <?php
                        while($postRow = $postsResult->fetch_assoc()) { ?> // Step through all of the post records gathered in the <head> of page
                            var $clone = profile_element.clone();
                            $clone.attr("id", "<?= $ProfileDetails["ID"] ?>");
                            $clone.children("div.PostContent").children("p.PostUsername").text("<?= $ProfileUsername["Username"] ?>"); // edit
                            $clone.children("div.PostContent").children("p.PostDate").text("<?=$postRow["Created"] ?>"); // edit
                            $clone.children("div.PostContent").children("p.PostBodyText").text("<?=$postRow["Content"] ?>");

                            $clone.children("div.PostPhoto").children("img").attr("src", "<?= $ProfileDetails["Photosrc"] ?>");

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

                    <a href="profile.php?ID=<?=$ProfileDetails["ID"]?>&page=<?=$page - 1;?>" class="NavigationLink">
                        <button style="<?= $LeftNavigationEnabled ?>" class="NavigationButton"><</button>
                    </a>

                    <a href="profile.php?ID=<?=$ProfileDetails["ID"]?>&page=<?=$page + 1;?>" class="NavigationLink">
                        <button style="<?= $RightNavigationEnabled ?>" class="NavigationButton">></button>
                    </a>
                </center>
            </div>

            <div class="ProfileContainer" style="width:500px; height:300px; margin-left:150px; margin-right:0; margin-top:35px;">
                <h1 style="font-family:Montserrat; font-size:30pt; color:black; display:inline-block; margin-left:35px;">info</h1>
                <hr style="width:90%; position:relative; bottom:10px;"/>

                <div style="margin-left:30px; font-family:Montserrat; font-size:16pt;">
                    <p>Bio: <?php global $ProfileDetails; echo $ProfileDetails["Description"]?></p>
                    <p>Joined: <?php global $ProfileDetails; echo $ProfileDetails["Lastseen"]?></p>
                    <p>Posts: <?php global $count; echo $count["COUNT(ID)"]?></p>
                </div>
            </div>
        </div>
        
    </body>
</html>
