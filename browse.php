<!-- browse.php
-----------------------

Browsing page for dynamica.
Serves as the page to view all other users' posts.

Dynamica - Jacob Whipp (2022).

-->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs ?>

<html>
    <head>
        <!-- Link to style sheet -->
        <link rel="stylesheet" href="/css/index.css" /> 
        <link rel="stylesheet" href="/css/main.css" /> 

        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


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
                <img src="/images/logout.png" style="position:relative; right:25px; bottom:36px;"/>
            </form>
        </div>

        <center>
        <div>
            <div>
                <h1 style="font-family:sans-serif;">Browse</h1> <!-- uses cookies for username -->

            </div>
        </div>
        </center>

        <div class="Container" id="ProfilePostContainer" style="width:1050px; height:740px; position:relative; left:50%; transform: translate(-50%, 0%);">
            <div class="SubContainer" id="PostConstructor" style="width:95%; height:100px; position:relative; left:50%; transform: translate(-50%, 0%); margin-top:20px;">
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
            var profile_element = $(".SubContainer");

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
                    <?php
                        $PostContent = preg_replace("/&#?[a-z0-9]+;/i","",$postRow["Content"]); 
                    ?>
                    $clone.children("div.PostContent").children("p.PostBodyText").text("<?=$PostContent?>");

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
