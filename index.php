<!-- index.php
-----------------------

Splash page for dynamica.
Serves as login screen and registration screen for users.

Dynamica - Jacob Whipp (2022).

-->

<?php
    function SanitiseRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

    return $data;
    }
?>

<?php
    session_start();

    if (isset($_SESSION["AUTHID"])){
        header("Location: " . "/dashboard.php");
        die();
    }
?>

<html>
    
    <head>
        <!-- Google fonts stuff... -->
        <link rel="preconnect" href="https://fonts.googleapis.com"> 
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bebas+Neue&family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">


        <!-- Link to style sheet -->
        <link rel="stylesheet" href="styles/stylesheet.css" /> 


        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <!-- linking JQuery module and scripts -->
        <script src="js/jquery-functions.js"></script> 
        <script src="js/index.js"></script>
    </head>

    <body>
        <center>
            <div>
                <h1 class="Logo" style="top:65px;">dynamica</h1>
                <h4 class="Motto">bringing people together</h4>
            </div>
        </center>
       

        <center>
            <form id="LoginForm" method="post" action="account/login.php">
                <div class="LoginContainer">
                    <h3 id="LoginHeader" style="font-family: 'Roboto'; font-weight:400;">Login</h3>
                    <hr style="width:85%; height:1px; border: 0;" class="gradient" />
                    
                    <?php 
                        if (isset($_GET["error"])) {
                            switch(SanitiseRequest($_GET["error"])) {
                                case "0":
                                    echo "<p class='ErrorMessage'>Unexpected error</p>";
                                    break;
                                case "1":
                                    echo "<p class='ErrorMessage'>No username given!</p>";
                                    break;
                                case "2":
                                    echo "<p class='ErrorMessage'>No password given!</p>";
                                    break;
                                case "3":
                                    echo "<p class='ErrorMessage'>Username or password is incorrect!</p>";
                                    break;
                                case "4":
                                    echo "<p class='ErrorMessage'>Please only use characters a-z, A-Z, or numbers 0-9!</p>";
                                    break;
                                case "5":
                                    echo "<p class='ErrorMessage'>Username already exists!</p>";
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
                    <p id="ToggleLogRegText" style="font-family:Roboto; font-weight:300;">no account? <a class="ToggleLogReg" href="#">register</a></p> 
                </div>
                
            </form>
        </center>
    </body>
</html>
