<!-- post.php
-----------------------

Create page for dynamica.
Serves as the post creation page a user will see after navigating through the header.

Dynamica - Jacob Whipp (2022).

-->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs ?>

<html>
    <head>
        <!-- Link to style sheet -->
        <link rel="stylesheet" href="/css/index.css" /> 
        <link rel="stylesheet" href="/css/forms.css" />
        <link rel="stylesheet" href="/css/main.css" /> 


        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


        <!-- Additional styles for this page -->
        <style>
            textarea {
                resize: none;
                border-radius:20px;
                padding:10px;
                font-size:15pt;
            }
        </style>
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
                <img src="/images/logout.png" style="position:relative; right:25px; bottom:36px;"/>
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

                    <center><textarea id="PostContentBox" name="Content" maxlength="200" cols="50" rows="5" form="PostForm" style="margin-top:50px; width:66%;"></textarea></center>

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