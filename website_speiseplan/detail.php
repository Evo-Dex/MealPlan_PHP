<?php
/*
 * Datenbankverbindung
 */

require 'dbconn.php';

/*
 * URL-Parameter
 */

# URL-/GET-Parameter auslesen
$gid = filter_input(INPUT_GET,'gid', FILTER_VALIDATE_INT);
$datum = filter_input(INPUT_GET, 'datum', FILTER_VALIDATE_INT);
$monat = filter_input(INPUT_GET, 'monat', FILTER_VALIDATE_INT);
$m_id = filter_input(INPUT_GET, 'm_id', FILTER_VALIDATE_INT);

# Warnings von PHP unterdrücken
// error_reporting(0);

?>

<!DOCTYPE HTML>
<!--
        Strongly Typed by HTML5 UP
        html5up.net | @ajlkn
        Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
    <head>
        <title>Detailansicht Gericht</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />
    </head>
    <body class="no-sidebar is-preload">
        <div id="page-wrapper">

            <!-- Header -->
            <section id="header">
                <div class="container">

                    <!-- Logo -->
                    <h1 id="logo"><a href="_index.html">Speiseplan</a></h1>
                    <p>Gerichtauswahl</p>

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
                    <div id="content">

                        <!-- Ausgewähltes Gericht -->
                        <article class="box post">
<?php
/*
 * Ausgewähltes Gericht -> alle Info ausgeben
 */

# Wenn ein Gericht ausgewählt (gid vorhanden in URL)
if (isset($gid)) {

    # SQL für alle Daten von einem bestimmten Gericht
    $sql = "SELECT * FROM alle_gerichte WHERE gid = $gid";
    
    $statement = $pdo->query($sql);

    # Datensätze durchlaufen
    foreach ($statement as $datensatz) {

        echo '<header>
            <h2><strong>' . $datensatz['Gericht'] . '</strong></h2>
        </header>
        <span class="image featured"><img src="bildergerichte/' . $datensatz['bild'] . '" alt="" /></span>

        <h3>Zutaten</h3>
        <p>' . nl2br($datensatz['zutaten']) . '</p>

        <h3>Zubereitungszeit: ' . $datensatz['zubereitungszeit'] . ' Min </h3>

        <h3>Zubereitung</h3>
        <p>' . nl2br($datensatz['beschreibung']) . '</p>';
    }
}   
# Wenn kein Gericht ausgewählt (kein gültiger URL)   
else {

    echo 'Gehen Sie bitte zurück zur <a href="index.php">Homepage</a> und wählen Sie ein Gericht aus';
}

# Link download Detailansicht Gericht

echo '<li><a href="pdf_detail.php?gid=' . $datensatz['gid'] . '" class="button icon solid fa-file">
    Download Gericht (PDF)</a></li>';

# Speicher freigeben
unset($statement);
?>

                        </article>
                        <!-- Ende ausgewähltes Gericht -->
                        
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