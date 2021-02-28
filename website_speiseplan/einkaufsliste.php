<?php
/*
 * Datenbankverbindung
 */

require 'dbconn.php';

/*
 * URL-Parameter
 */

# URL-/GET-Parameter mit der Filter-Funktion auslesen
$datum = filter_input(INPUT_GET, 'datum', FILTER_VALIDATE_INT);
$monat = filter_input(INPUT_GET, 'monat', FILTER_VALIDATE_INT);
$m_id = filter_input(INPUT_GET, 'm_id', FILTER_VALIDATE_INT);
$gid = filter_input(INPUT_GET,'gid', FILTER_VALIDATE_INT);

# Warnings von PHP unterdrücken
//error_reporting(0);

?>

<!DOCTYPE HTML>
<!--
        Strongly Typed by HTML5 UP
        html5up.net | @ajlkn
        Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
    <head>
        <title>Einkaufsliste</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />
    </head>
    <body class="right-sidebar is-preload">
        <div id="page-wrapper">

            <!-- Header -->
            <section id="header">
                <div class="container">

                    <!-- Logo -->
                    <h1 id="logo"><a href="_index.html">Einkaufsliste</a></h1>


                    <!-- Nav -->
                    <nav id="nav">

<?php
/*
 * Navigation
 */

# Dropdown Menu für alle Monate

echo '<ul>';
echo '<li><a class="icon solid fa-home" href="index.php"><span>Home</span></a></li>';

echo '<li>
    <a href="#" class="icon fa-calendar-week"><span>Monat</span></a>
    <ul>';
    
    # SQL für alle Monate & Datensätze durchlaufen
    $stmt = $pdo->prepare('SELECT * FROM monat ORDER BY m_id');
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        echo '<li><a href="' . $_SERVER['SCRIPT_NAME'] . '?m_id=' . $row['m_id'] .'">'.$row['monat'].'</a></li>';
    }

echo '</ul>
</li>';

# SQL für alle Wochen pro Monat
$sql = "SELECT * FROM datum WHERE monat_id = $m_id ORDER BY d_id";

$statement = $pdo->query($sql);

# Datensätze durchlaufen
foreach( $statement as $datensatz ){
                        
    # geklickte Kategorie highlighten
    if($datum == $datensatz['d_id']){
        $current = 'class="current"';
    }
    else{
        $current = '';
    }

    echo '<li><a class="icon solid fa-apple-alt' . $current . '" href="' . $_SERVER['SCRIPT_NAME'] . '?m_id='.$datensatz['monat_id'].'&datum=' . $datensatz['d_id'] . '">' . $datensatz['datum'] . '</a></li>';
                       
}
echo '</ul>';

# Datensätze aus dem Speicher löschen
unset($statement);

?>
                    </nav>
                    <!-- Nav Ende -->

                </div>
            </section>

            <!-- Main -->
            <section id="main">
                <div class="container">
                    <div class="row">

                        <!-- Content -->
                        <div id="content" class="col-8 col-12-medium">

                            <!-- Post -->
                            <article class="box post">
                                <header>
<?php
/*
 * Überschrift für User-Auswahl dynamisch machen
 */

# Wenn ein datum in Nav angeklickt
if (isset($datum)) {

    # SQL Anweisung um Datum text aus Datenbank zu holen
    $sql = "SELECT datum FROM datum WHERE d_id = $datum";
    $statement = $pdo->query($sql);

    # Datensätze durchlaufen und ausgeben
    foreach ($statement as $datensatz) {

        echo '<h2>Ihre Einkaufsliste für die Woche vom <br> <strong>' . $datensatz['datum'] . '</strong>!</h2>';
    }
}
# Wenn kein datum in Nav angeklickt
else {

    echo '<h2>Bitte wählen Sie zuerst die <strong>Woche</strong> für Ihre Einkaufe aus</h2>';
}

# Speicher freigeben
unset($statement);
?>

                                </header>
                                <span class="image featured"><img src="bildergerichte/gnstig-lebensmittel-einkaufen.jpg" alt="" /></span>
<?php
/*
 * Einkaufsliste -> alle Zutaten & Gerichte für User-Auswahl ausgeben
 */

# Wenn ein Datum ausgewählt wurde
if (isset($datum)) {

    # Alle Zutaten für eine Woche sammeln in Einkaufsliste (zB Woche 1)
    $sql = "SELECT gid, gericht, zutaten
            FROM alle_gerichte
            WHERE d_id = $datum
            ORDER BY zutaten";
    
    $statement2 = $pdo->query($sql);

    # Datensätze durchlaufen
    echo '<table>
            <tr>
                <th>Gericht</th>
                <th>Zutaten</th>
            </tr>';
    foreach ($statement2 as $datensatz) {
        echo'<tr>
                <td>' . $datensatz['gericht'] . '</td>
                <td>' . nl2br($datensatz['zutaten']) . '</td>
            </tr>';
    }
    echo '</table>';
}
# Wenn keine Woche ausgewählt 
else {

    echo 'Bitte wählen Sie eine Woche in der Navigation aus um Einkaufsliste darzustellen';
}

# Speicher freigeben
unset($statement2);

?>
                                
                            </article>

                        </div>

                        <!-- Sidebar -->
                        <div id="sidebar" class="col-4 col-12-medium">

                            <!-- Excerpts -->
                            <section>
                                <ul class="divided">
                                    <li>

                                        <!-- Highlight -->
                                        <article class="box highlight">
<?php  

# Link download Einkaufsliste

echo '<ul class="actions">';
    echo '<li><a href="pdf.php?m_id='.$m_id.'&datum=' . $datum . '" class="button icon solid fa-file">Download Einkaufsliste</a></li>';
echo '</ul>';

?>
                                        </article>

                                    </li>
                                </ul>
                            </section>

                        </div>

                    </div>
                </div>
            </section>

            <!-- Footer -->
            <section id="footer">
                
                <div id="copyright" class="container">
                    <ul class="links">
                        <li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
                    </ul>
                </div>
            </section>

        </div>

        <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery.dropotron.min.js"></script>
        <script src="assets/js/browser.min.js"></script>
        <script src="assets/js/breakpoints.min.js"></script>
        <script src="assets/js/util.js"></script>
        <script src="assets/js/main.js"></script>

    </body>
</html>