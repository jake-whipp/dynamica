<!-- users/me.php
-----------------------

Pointer towards user's own profile.
Saves hassle or messy code in main body of code for header buttons in other pages.

Dynamica - Jacob Whipp (2022).

-->


<?php
     // Establish login details for sql. NOT a dynamica logon
     $servername = "localhost";
     $username_SQL = "root";
     $password_SQL = "root";
     $dbname = "dynamica";

     // Create connection
     $conn = new mysqli($servername, $username_SQL, $password_SQL, $dbname);


     // Check connection
     if (mysqli_connect_error()) {
         header('Location: ' . "/dashboard.php?error=2", true);
         die();
     }

     // Run an SQL query
     $my_username = substr($_COOKIE["AUTHCOOKIE"], 0, strpos($_COOKIE["AUTHCOOKIE"], "_"));
     $sql = "SELECT ID FROM users WHERE Username = '" . $my_username . "'";

     $result = $conn->query($sql);
     $row = $result->fetch_assoc(); // Get first row, in which the ID will be contained

     header('Location: ' . "/users/profile.php?ID=" . $row["ID"], true);
     die();
?>