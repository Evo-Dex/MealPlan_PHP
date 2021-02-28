<?php

/*
* Speiseplan, Website mit Verbindung zur MySQL-Datenbank
*/

# Liste der Gerichte mit Auswahl zur Bearbeitung

session_start();
# keine gültige sitzung, dann abbrechen und auf anmelden seite weiterleiten

if ( !isset ( $_SESSION['login'] ) || $_SESSION['login'] !== true){
    
die('Bitte zuerst auf Homepage<a href ="../index.php">anmelden</a>'); 
}

# Einbindung der Datenbankverbindung
require("../dbconn.php");
# Einbindung von Zusatzfunktionen
require("../funktionen.php");
?>

<!DOCTYPE html>
<html lang="de">
    <head>

        <meta charset="utf-8">
        <title>Liste mit Gerichten</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/admin.css">  

    </head>
    <body>



<?php
echo "<div class=\"border\">";
echo "<div id=\"userlogout\">\n";
echo "Benutzer: &nbsp;&nbsp;&nbsp;<a href=\"logout.php\">Logout</a>";
echo "</div>\n";

echo '<h1>Alle Gerichte</h1>';
echo "</div>";

echo "<div id=\"content\" class=\"border\">";

# Link für neue Gerichte
echo '<h2><a class="linkneu" href="speiseform.php?gid=neu">neues Gericht</a></h2>';

# Alle Gerichte mit Datum & Monat
$sql = "SELECT Monat, Woche, Gericht, d_id, gid
        FROM alle_gerichte
        ORDER BY d_id";

$statement = $pdo->query($sql);

# Schleife zur Ausgabe der Datensätze
echo '<table class="liste">';
echo '<tr><th>Gerichte</th><th>Monat</th><th>Woche</th><th> </th><th> </th></tr>';
foreach ($statement as $datensatz) {

    echo '<tr>';
    echo '<td><a class="filmtitel" href="speiseform.php?gid=' . $datensatz["gid"] . '">' . $datensatz["Gericht"] . '</a></td>';
    echo '<td>' . $datensatz["Monat"] . '</td>';
    echo '<td>' . $datensatz["Woche"] . '</td>';
    echo '<td><a href="upload.php?id=' . $datensatz["gid"] . '">Bild</a></td>';
    echo '<td><a href="gerichtloeschen.php?id=' . $datensatz["gid"] . '">löschen</a></td>';
    echo '</tr>';
}
echo '</table>';

# Datensatzzeiger wieder freigeben
unset($datensatz);

# Datenbankverbindung schliessen
$pdo=NULL;
?>

    </div>

</body>
</html>
