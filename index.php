<!-- index.php
-----------------------

Splash page for dynamica.
Serves as login screen and registration screen for users.

Dynamica - Jacob Whipp (2022).

-->

<?php

    function SanitiseRequest($data) { // This function will be used to "clean up" a user input. It is necessary in order to prevent threats like XSS.
        $data = trim($data); // Remove spaces
        $data = stripslashes($data); // Remove slashes
        $data = htmlspecialchars($data); // Convert certain characters into html entities

        return $data;
    }

    session_start();

    if (isset($_SESSION["AUTHID"])){ // If the user currently has a session active:
        header("Location: " . "/dashboard.php");
        die();
    }
?>

<html>
    
    <head>
        <!-- Google fonts API -->
        <link rel="preconnect" href="https://fonts.googleapis.com"> 
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bebas+Neue&family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">


        <!-- Link to style sheet -->
        <link rel="stylesheet" href="css/index.css" /> 
        <link rel="stylesheet" href="css/forms.css" /> 
        <link rel="stylesheet" href="css/main.css" /> 

        <!-- JQuery and JQuery UI CDN hosted libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <!-- linking JQuery module and scripts -->
        <script src="javascript/index.js"></script>
    </head>

    <body>
        <center>
            <div> <!-- Logo -->
                <h1 class="Logo" style="top:65px;">dynamica</h1>
                <h4 class="Motto">bringing people together</h4>
            </div>
        </center>
       

        <center> 
            <form id="LoginForm" method="post" action="account/login.php"> <!-- Login form -->
                <div class="LoginContainer">
                    <h3 id="LoginHeader" style="font-family: 'Roboto'; font-weight:400;">Login</h3>
                    <hr style="width:85%; height:1px; border: 0;" class="hrgradient" />
                    
                    <?php 
                        if (isset($_GET["error"])) { // If an error has occurred
                            switch(SanitiseRequest($_GET["error"])) { // Use error codes as opposed to string in order to prevent XSS
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


                <div style=" width:99%; padding:0; margin-top:260;">
                    <p id="ToggleLogRegText" style="font-family:Roboto; font-weight:300; display:inline-block; margin-right:5px;">no account? </p> 
                    <a class="ToggleLogReg" href="#" style="font-family:Roboto; font-weight:300; display:inline-block;">register</a>
                </div>
                

                
            </form>
        </center>
    </body>
</html>
