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

    echo "<h1> Welcome, " . SanitizeRequest($_POST["Username"]) . "!</h1>";
?>
