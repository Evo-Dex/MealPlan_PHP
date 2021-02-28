<?php

# Speiseplan, Website mit Verbindung zur MySQL-Datenbank
# Logout

session_start();
session_destroy ();

?>

<!DOCTYPE html>
<html lang="de">
<head>
    
    <meta charset="utf-8">
    <title>Logout Speiseplan</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin.css">  

</head>
<body>

<div class="border">
    <h1>Logout Speiseplan</h1>
</div>
    
<div id="content" class="border">


    <div id="formular">

        Sie sind abgemeldet.<br><br>
        <a href="../index.php">erneut anmelden</a>

    </div>

</div>

</body>
</html>
