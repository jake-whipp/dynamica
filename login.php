<?php
    function SanitizeRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    if (empty(SanitizeRequest($_POST["Username"]))) {
        header('Location: ' . "index.php?error=1", true);
        die();
    }

    if (empty(SanitizeRequest($_POST["Password"]))) {
        header('Location: ' . "index.php?error=2", true);
        die();
    }


    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "dynamica";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if (mysqli_connect_error()) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    echo "Connected successfully<br>";

    $sql = "SELECT * FROM users WHERE Username='" . SanitizeRequest($_POST["Username"]) . "' AND Password='" . SanitizeRequest($_POST["Password"]) . "';"; // e.g. "SELECT * FROM users WHERE Username='admin' AND Password='root';
    $result = $conn->query($sql);

    if ($result != false) {
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
              echo "Welcome, " . SanitizeRequest($_POST["Username"]) . "!";
            }

          } else {
            header('Location: ' . "index.php?error=3", true);
            die();
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    

    $conn->close();

    echo "<h1> Welcome, " . SanitizeRequest($_POST["Username"]) . "!</h1>";
?>
