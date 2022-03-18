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
        <center>
            <div>
                <h1 class="LogoHeader">dynamica</h1>
                <h4 style="position: relative; font-family: sans-serif; left:100px; color:rgb(43, 144, 184); width:200px;">bringing people together</h4>
            </div>
        </center>
       

        <center>
            <form method="post" action="login.php">
                <div class="LoginContainer">
                    <h3 style="font-family: 'Roboto'; font-weight:400;">Login</h3>
                    <hr style="width:85%; height:1px; border: 0;" class="gradient" />
                    
                    <?php 
                        if (isset($_GET["error"])) {
                            switch(SanitiseRequest($_GET["error"])) {
                                case "1":
                                    echo "<p style='color:red; padding:0; margin:0; position:relative; max-height:0px; font-weight:600;'>No username given!</p>";
                                    break;
                                case "2":
                                    echo "<p style='color:red; padding:0; margin:0; position:relative; max-height:0px; font-weight:600;'>No password given!</p>";
                                    break;
                                default:
                                    break;
                            }
                        }; 
                    ?>

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

                <div style="position:absolute; bottom:0; width:99%; padding:0; margin:0;">
                    <p style="font-family:Roboto; font-weight:300;">no account? <a href="register.php">register</a></p> 
                </div>
                
            </form>
        </center>
    </body>
</html>
