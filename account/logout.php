<!-- logout.php
-----------------------

Logout handler for dynamica
Handles the functionality of logging out of user accounts for dynamica. Not visible to user.

Dynamica - Jacob Whipp (2022).

-->


<?php
    CreateSession("AUTHCOOKIE", "",(time() - 3600));

    session_unset();
    session_destroy();

    header('Location: ' . "/index.php");
?>
