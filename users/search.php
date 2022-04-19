<?php include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs ?>

<html>
    <head>
        <!-- Link to style sheet -->
        <link rel="stylesheet" href="/css/main.css" /> 
        <link rel="stylesheet" href="/css/user.css" /> 
        <link rel="stylesheet" href="/css/forms.css" />

        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


        <!-- Additional styles for this page -->
        <style>
            .hrgradient {
                background-image: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,1), rgba(0,0,0,0));
            }
        </style>

        <?php
                

                // Establish login details for sql. NOT a dynamica logon
                $servername = "localhost";
                $username = "root";
                $password = "root";
                $dbname = "dynamica";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);


                function ValidateData($data) {
                    global $conn;
                    if ($data != false) { // If successful
                        if ($data->num_rows == 0) { // and there are 0 results (ie incorrect query or page number too big):
                            return 0;
                        } else {
                            return 1;
                        }
                    } else {
                        header('Location: ' . "/dashboard.php?error=0", true);
                        die();
                        return 0;
                    }
                    return 0;
                }


                // Check connection
                if (mysqli_connect_error()) {
                    header('Location: ' . "/dashboard.php?error=2", true);
                    die();
                }


                $users_per_page = 5; // how many user accounts per page on the search index

                // Check if user has made a search upon page load
                
                if (!isset($_GET["search"])) { // If user hasn't searched yet
                    $SearchContent = "";

                    // Count the total number of users
                    $sqlCountUsers = "SELECT COUNT(ID) AS total FROM users";
                    

                    if (isset($_GET["page"])) {
                        $GLOBALS["page"] = $_GET["page"]; // set global for usage in HTML body
                    } else {
                        $GLOBALS["page"] = 0;
                    }

                    $countResult = $conn->query($sqlCountUsers);
                    ValidateData($countResult);

                    $row = $countResult->fetch_assoc(); // get first row
                    $GLOBALS["number_of_pages"] = ceil($row["total"] / $users_per_page); // set global for usage in HTML body



                    // Select the number of users necessary for the current "page".
                    // ie IF on page two, start at user id 5 and select up to id 10 
                    $sqlGetUsers = "SELECT * FROM users ORDER BY ID ASC LIMIT " . ($page * $users_per_page) .  ", " . $users_per_page . ";"; 

                    // Store results in a variable
                    $userResult = $conn->query($sqlGetUsers);

                    // Check validity
                    ValidateData($userResult);



                    // Get related profile data
                    $sqlGetProfileData = "SELECT * FROM profiles ORDER BY ID ASC";

                    // Store results in a variable
                    $profileResult = $conn->query($sqlGetProfileData);

                    // Check validity
                    ValidateData($profileResult);

                } else { // If user HAS made a search
                    $SearchContent = SanitiseRequest($_GET["search"]);

                    if (isset($_GET["page"])) {
                        $GLOBALS["page"] = $_GET["page"]; // set global for usage in HTML body
                    } else {
                        $GLOBALS["page"] = 0;
                    }

                    // Make a query based on search parameters by user
                    // Using "like" because we want to return "dynamica" as part of the list if user searches "dy" or "dyna"
                    $sqlGetUsers =  "SELECT * FROM users WHERE Username like '%" . $SearchContent.  "%' LIMIT " . ($page * $users_per_page) . ", " . $users_per_page; 
                    $userResult = $conn->query($sqlGetUsers);

                    ValidateData($userResult);


                    // Count the total number of users WITHIN our search parameters
                    $sqlCountUsers = "SELECT COUNT(ID) FROM (SELECT * FROM users WHERE Username LIKE '%" . $SearchContent.  "%') AS total;";
                    $countResult = $conn->query($sqlCountUsers);

                    ValidateData($countResult);
                    

                    $row = $countResult->fetch_assoc(); // get first row
                    $GLOBALS["number_of_pages"] = ceil($row["COUNT(ID)"] / $users_per_page); // set global for usage in HTML body


                    // Get related profile data
                    $sqlGetProfileData = "SELECT * FROM profiles WHERE profiles.ID IN (SELECT ID FROM users WHERE Username LIKE '%" . $SearchContent.  "%');";
                    $profileResult = $conn->query($sqlGetProfileData);

                    ValidateData($profileResult);
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
                    <input type="Submit" value="Users" class="HeaderElement" style="width:100px; height:50px; box-shadow: 0 1px 0 #fff;">
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
            <form id="SearchForm" method="get" action="/users/search.php">
                <div style="position:relative; top:20px; font-weight:300;">
                
                    <p style="position:relative;  top:5px; font-family:Montserrat; font-size:20pt;">Search</p>
                    <hr style="width:500px; height:1px; border: 0;" class="hrgradient" />

                    <?php if (ValidateData($userResult) == 0) {echo "<p style='font-family:Montserrat; color:red; font-weight:600;'>No users found!</p>";} ?>

                    <br/>
                    <input id="UsernameBox" name="search" type="text" class="InputBox" style="outline:1px solid grey; width:500; " placeholder="Search for a username..." />

                    <div style="display:inline-block; margin-left:30px;">
                        <input type="submit" value="Submit" class="InputButton" style=" border-radius:15px; vertical-align:middle; font-family:Montserrat; font-size:14pt;"/>
                    </div>
                </div>
            </form>
            </center>

            <div class="ProfileContainer">
                <div class="ProfileButton" id="ProfileButtonConstructor">
                        <img class="ProfileIcon" style="display:inline-block;" src="/users/icons/default.jpg" width="5%" height="80%">
                        <a href="/index.php"><h2 class="ProfileName" style="display:inline-block;">dynamica</h2></a>
                        <p class="ProfileDesc" style="display:inline-block;">"Hi, I'm new to dynamica."</p>
                        <p class="LastSeen" style="display:inline-block;">Registered on 15/04/2022</p>
                        <div style="clear: both;"></div>
                </div>
            </div>

            <script>
                var profile_element = $(".ProfileButton");

                <?php
                    while($userRow = $userResult->fetch_assoc()) { ?> // Step through all of the user records gathered in the <head> of page
                        <?php
                            $sqlGetProfileData = "SELECT * FROM profiles WHERE profiles.ID=" . $userRow["ID"] . ";";
                            $profileResult = $conn->query($sqlGetProfileData);
                        ?>

                        var $clone = profile_element.clone();
                        $clone.attr("id", "<?= $userRow["ID"] ?>");
                        $clone.children("a").children("h2").text("<?= $userRow["Username"] ?>");
                        $clone.children("a").attr("href", "/users/profile.php?ID=<?= $userRow["ID"] ?>");
                        

                        <?php $profileRow = $profileResult->fetch_assoc(); ?>
                        $clone.children("img").attr("src", "<?= $profileRow["Photosrc"] ?>");
                        $clone.children("p.ProfileDesc").text("<?= $profileRow["Description"] ?>");
                        $clone.children("p.LastSeen").text("Registered on <?= $profileRow["Lastseen"] ?>");

                        $clone.appendTo(".ProfileContainer");
                <?php } ?>

                $("#ProfileButtonConstructor").remove();
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

            <a href="search.php?page=<?=$page - 1;?>&search=<?=$SearchContent?>" class="NavigationLink">
                <button style="<?= $LeftNavigationEnabled ?>" class="NavigationButton"><</button>
            </a>

            <a href="search.php?page=<?=$page + 1;?>&search=<?=$SearchContent?>" class="NavigationLink">
                <button style="<?= $RightNavigationEnabled ?>" class="NavigationButton">></button>
            </a>
        </center>
    </body>
</html>