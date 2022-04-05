<?php
    session_start();
    $timeout = 900; // how long can the user remain active

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
                    header('Location: ' . "/dashboard.php?error=2", true);
                    die();
                }

                // Run an SQL query e.g. "SELECT * FROM users WHERE Username='admin' AND Password='root'
                $sql = "SELECT * FROM users";

                // Store results in a variable
                $result = $conn->query($sql);

                if ($result != false) {
                    if ($result->num_rows == 0) {
                        header('Location: ' . "/dashboard.php?error=1", true);
                        die();
                    }
                } else {
                    header('Location: ' . "/dashboard.php?error=0", true);
                    die();
                }

                $num=$result->num_rows;

                echo "<b><center>Users</b><br><br>";

                echo "<table>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Username</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {  
                    echo "<tr><td>" . $row['ID'] . "</td> <td>" . $row['Username']."</td></tr>"; //etc...
                }
                echo "</table></center>";
        ?>
    </head>


    <body>
        <center>
            <br/>
            <form action="/dashboard.php">
                <input type="submit" value="Dashboard"/>
            </form>

            <form action="/account/logout.php">
                <input type="submit" value="Logout"/>
            </form>
        </center>
    </body>
</html>
