<?php
    function SanitiseRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

    return $data;
    }

?>

<html>
    
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bebas+Neue&family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="stylesheet.css" />
    </head>

    <body>
        <?php 
            if (isset($_GET["error"])) {
                switch(SanitiseRequest($_GET["error"])) {
                    case "1":
                        echo "No username given!";
                        break;
                    case "2":
                        echo "No password given!";
                        break;
                    default:
                        break;
                }
            }; 
        ?>
        <center><h1 class="LogoHeader">dynamica</h1></center>

        <center>
            <form method="post" action="login.php">
                <div class="LoginContainer">
                    <h3 style="font-family: 'Roboto'; font-weight:400;">Login</h3>
                    <hr style="width:85%; height:1px; border: 0;" class="gradient" />
                    
                    <div style="position:relative; top:20px; font-family:Roboto; font-weight:300;">
                        <p style="position:relative; right:95px; top:5px;">Username</p>
                        <input id="UsernameBox" name="Username" type="text" class="InputBox" style="outline:none;" placeholder="Enter your username" />
                    </div>

                    <div style="position:relative; top:35px; font-family:Roboto; font-weight:300;">
                        <p style="position:relative; right:95px; top:5px;">Password</p>
                        <input id="PasswordBox" name="Password" type="password" class="InputBox" style="outline:none;" placeholder="Don't share with anyone!" />
                    </div>

                    <div style="position:relative; top:80px;">
                        <input type="submit" value="Submit" class="InputButton"/>
                    </div>
                    
                </div>
            </form>
        </center>
    </body>
</html>