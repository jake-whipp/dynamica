<?php include($_SERVER["DOCUMENT_ROOT"] . "/account/validate.php"); // Run the code that establishes client cookie and server session pairs ?>


<?php // Handle post request -----------------------------
    $Content = SanitiseRequest($_POST["Content"]);

    if (strlen($Content) > 200) {
        header('Location: ' . "/upload/post.php?error=1", true);
        die();
    }

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

    // Get the UserID of post creator

    $sqlGetUserID = "SELECT ID FROM users WHERE Username='" . (substr($_SESSION["AUTHID"], 0, strpos($_SESSION["AUTHID"], "_"))) . "';";
    echo $Content;
    $UserID = ($conn->query($sqlGetUserID))->fetch_assoc();

    // Run an SQL query
    $sqlCreatePost = "INSERT INTO posts (Content, UserID) VALUES ('". $Content . "','" . $UserID["ID"] . "')";
    
    if ($conn->query($sqlCreatePost) === TRUE) { // if query was successful
        header('Location: ' . "/users/me.php", true);
        die();
    } else { // else if not successful
        header('Location: ' . "/upload/post.php?error=2", true); // redirect with error message
        die();
    }
?>