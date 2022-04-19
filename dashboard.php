<!-- dashboard.php
-----------------------

Main page for dynamica.
Serves as the home page a user will see after logging in.

Dynamica - Jacob Whipp (2022).

-->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs ?>

<html>
    <head>
        <!-- Link to style sheet -->
        <link rel="stylesheet" href="/css/main.css" />
        <link rel="stylesheet" href="/css/forms.css" /> 


        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


        <!-- Additional styles for this page -->
        <style>
            .SubContainer + .SubContainer {
                margin-top:15px;
            }
        </style>
    </head>

    <body>
        <div class="Header" style="overflow:hidden;"> <!-- Header at top of page -->
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
                <img src="/images/logout.png" style="position:relative; right:25px; bottom:36px;"/>
            </form>
        </div>

        <center>
        <div>
            <div>
                <h1 style="font-family:sans-serif;">Hello, <?= substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_")) ?>!</h1> <!-- uses session/cookie for username -->
                <p style="font-family:Montserrat; font-size:14pt;">would you like to:</p>
            </div>

            <div class="Container" style="width:380px; position:fixed; height: 250px; left:60px;">
                <form action="/users/upload.php" method="post">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Update your bio</h1>
                        <p style="font-family:Montserrat; font-weight:300;">Let others know what you're up to</p>
                        <input id="BioBox" name="Bio" type="text" class="InputBox" style="outline:none;margin-top:20px;font-size:13pt;" placeholder="Enter a new bio here.." />
                        <input type="submit" value="Change" class="InputButton" style="border-radius:10px; width: 150px; margin-top:20px; font-family:Montserrat;"/>
                </form>
            </div>

            <div class="Container" style="width:900px; display:inline-block;">
                <form action="/users/me.php">
                    <div class="SubContainer" style="width:500px; height:120px; margin-top:100px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">View your profile?</h1>
                        <input type="submit" value="Visit" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>
                
                <form action="/upload/post.php">
                    <div class="SubContainer" style="width:500px; height:120px; margin-top:50px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Create a post?</h1>
                        <input type="submit" value="Create" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>

                <form action="/browse.php">
                    <div class="SubContainer" style="width:500px; height:120px; margin-top:50px; margin-bottom:100px;">
                        <h1 style="font-family:Montserrat; font-size:14pt;">Browse others' posts?</h1>
                        <input type="submit" value="Look" class="InputButton" style="border-radius:10px; font-family:Montserrat;"/>
                    </div>
                </form>
            </div>
            

            
        </div>
        </center>
    </body>
</html>
