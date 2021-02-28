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

/*
 *  Login im Admin-Bereich
 */

########### Einbinden von Datenbankverbindung und Funktionen ###########

# Einbindung der Datenbankverbindung
include("dbconn.php");
# Einbindung von Zusatzfunktionen
include("funktionen.php");

################# Formulardaten empfangen ################

# Formulardaten empfangen
$name = filter_input(INPUT_POST, 'name');
$passwort = filter_input(INPUT_POST, 'passwort');
$button = filter_input(INPUT_POST, 'button');

# Logindaten (der User der mit seinem Passwort Zugang zur Datenbank hat)
$login_name = 'root';
$login_passwort = '$2y$10$NArnf25OenYO4Rt5v4nk1OjbEIES7CZ.GRAZDco/MvxXriOy80Rka';

#Testausgaben
//echo password_hash('cimdata', PASSWORD_DEFAULT);

# wurden Formulardaten versendet?
if( strlen($button) >1 ){

    ################ Prüfung der Formulardaten ##################

    # Passwort überprüfen, Session starten und Weiterleitung zur Liste mit Gerichten
    
    # Name und Passwort korrekt?
    if (password_verify($passwort, $login_passwort)&& $name = $login_name){
        
        session_start();
        $_SESSION['user'] = $name;
        $_SESSION['login'] = true;
                
                
        header('Location: admin/gerichte.php');
    }
    # sonst meldung ausgeben
    else {
        $meldung [] = 'WRONG!!! try again...';
    }
}
/*
 *  Search Formular
 */

$output= '';

# wurde auf search button gedrückt (+eingabe vorbereiten)
if (isset($_POST['search']) ){
    $searchq = $_POST['search'];
    $searchq = preg_replace("#[^0-9a-zäüöß]#i", "", $searchq);

    $statement = $pdo->prepare("SELECT * FROM alle_gerichte WHERE Gericht LIKE '%$searchq%'");
    $statement->execute();
    $count = $statement->rowCount();

    # wenn keine übereinstimmende Gerichte in Datenbank gefunden
    if($count == 0){
        $output = 'keine Gerichte gefunden';
    }
    # wenn was gefunden wird
    else {
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            
            # get Name & ID
            $gericht = $row['Gericht'];
            $id = $row['gid'];

            $output .='<div><li><a href="detail.php?gid=' . $row['gid'] . '" class="">'.$gericht.'</a></li></div>';
            
        }
    }
}

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
        <title>Speiseplan</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />
    </head>
    <body class="homepage is-preload">
        <div id="page-wrapper">

            <!-- Header -->
            <section id="header">
                <div class="container">

                    <!-- Logo -->
                    <h1 id="logo"><a href="index.php">Speiseplan</a></h1>
                    <p>Kochen einfach gemacht!</p>

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

            <!-- Features -->
            <section id="features">
                <div class="container">
                    <header>
<?php
/*
 * Überschrift für User-Auswahl dynamisch machen
 */

# Wenn ein Datum in Nav angeklickt
if (isset($datum)) {

    # SQL Anweisung um Datum text aus Datenbank zu holen
    $sql = "SELECT datum FROM datum WHERE d_id = $datum";
    $statement = $pdo->query($sql);

    # Datensätze durchlaufen und ausgeben
    foreach ($statement as $datensatz) {

        echo '<h2>Ihre Gerichte für den <strong>' . $datensatz['datum'] . '</strong>!</h2>';
    }
}
# Wenn kein Datum in Nav angeklickt
else {

    echo '<h2><strong>Schnelle</strong> und <strong>einfache</strong> Gerichte!</h2>';
}

# Speicher freigeben
unset($statement);

?>

                    </header>
                    <div class="row aln-center">

                    <!-- hier alle Gerichte für die ausgewählte Woche -->

<?php
/*
 * Gerichte zur User-Auswahl ausgeben
 */

# wurde in der Navigation auf ein Datum geklickt?
if( is_int($datum)) {

    # SQL für alle Gerichte in einer Woche
    $sql = "SELECT gid, Gericht, bild, zubereitungszeit, zutaten, beschreibung
            FROM alle_gerichte
            WHERE d_id = $datum
            ORDER BY Gericht";
}
# wurde noch kein Datum angeklickt?
else {

    # SQL für schnelle Gerichte (<31Min)
    $sql = 'SELECT gid, Gericht, bild, zubereitungszeit, beschreibung
            FROM alle_gerichte
            WHERE zubereitungszeit < 31
            ORDER BY zubereitungszeit
            LIMIT 12';
}

$statement = $pdo->query($sql);

# Datensätze durchlaufen und ausgeben
foreach ($statement as $datensatz) {

    echo '<div class="col-4 col-6-medium col-12-small">                           
        <section><a href="detail.php?gid='. $datensatz['gid'].'" class="image featured">';

    # ist ein Bild vorhanden
    if (is_file('bildergerichte/' . $datensatz['bild'])) {
        echo '<img src="bildergerichte/' . $datensatz['bild'] . '" alt="" height="218"/>';
    }

    # ist kein Bild vorhanden
    else {
        echo '<img src="bildergerichte/default.jpg" alt="" height="218"/>';
    }

    # (Kurz-)Info zur Gericht ausgeben + Link zur Detailseite
    echo '</a>
            <header>
                <h3>' . $datensatz['Gericht'] . '</h3>
            </header>
            <p>Zubereitungszeit: ' . $datensatz['zubereitungszeit'] . ' Min</p>
            <p>' . mb_substr($datensatz['beschreibung'], 0, 250) . '...</p>

                <ul class="actions">
                    <li><a href="detail.php?gid=' . $datensatz['gid'] . '" class="button icon solid fa-file">Mehr</a></li>
                </ul>
        </section>
    </div>';
}

# Speicher freigeben
unset($statement);

?>
                    </div>    
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
                                    <h2>Bereiten Sie Ihren Einkauf für die <br \><strong>aktuelle Woche</strong>
                                        vor.</h2>
                                </header>
                                <a href="#" class="image featured"><img src="bildergerichte/einkaufen.jpg" alt="" /></a>

                                <ul class="actions">

<?php

/*
 * Navigation zur Einkaufsliste
 */ 

echo '<li><a href="einkaufsliste.php?m_id='.$m_id.'&datum=' . $datum . '" class="button icon solid fa-file">
    Einkaufsliste jetzt erstellen</a></li>';

?>
                                </ul>
                            </article>

                        </div>

                        <!-- Sidebar -->
                        <div id="sidebar" class="col-4 col-12-medium">

                            <!-- Excerpts -->
                            <section>
                                <ul class="divided">
                                    <li>

                                        <!-- Login für Admin -->
                                        <article class="box excerpt">

<?php   

/*
 * Login Admin Bereich
 */ 

if ( isset ($meldung) ){

        echo '<p class="fehler">';
        echo implode("<br>", $meldung); 
        echo '</p>';
    }
?>

                                            <h2><a>Login Admin</a></h2>

                                            <div id="formular">

                                                <form action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" method="post">

                                                    <section id="section_input">
                                                        <label class="form_label" for="name">Name</label>
                                                        <input class="form_input_login" type="text" name="name" id="name">

                                                        <label class="form_label" for="passwort">Passwort</label>
                                                        <input class="form_input_login" type="password" name="passwort" id="passwort">
                                                    </section>

                                                    <section id="section_input">
                                                        <button class="form_button" type="submit" name="button" value="Login">Login</button>
                                                    </section>

                                                </form>

                                            </div>

                                        </article>

                                        <!-- Search Form -->
                                        <article class="box excerpt">
                                        <h2>Search</h2>
                                        <form action="index.php" method="post">
                                            <input type="text" name="search" placeholder="Suche Gerichte..."/><br>
                                            <input type="submit" value="Los"/>
                                        </form>
                                    <br><br>
                                    <?php print_r($output); ?>
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
<?php
#Datenbankverbindung schliessen
$pdo=NULL;