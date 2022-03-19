<!-- dashboard.php
-----------------------

Main page for dynamica.
Serves as the home page a user will see after logging in.

Dynamica - Jacob Whipp (2022).

-->

<?php
    function SanitiseRequest($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

    return $data;
    }

    // TODO: If  there's no current auth cookie active for signed in user, then redirect them to /dynamica-main/index.php/
?>
