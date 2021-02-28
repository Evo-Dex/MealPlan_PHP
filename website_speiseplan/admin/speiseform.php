<?php
# Speiseplan, Website mit Verbindung zur MySQL-Datenbank
# Formular für Gerichte

session_start();
# keine gültige sitzung, dann abbrechen und auf anmeldeseite weiterleiten
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {

    die('Bitte zuerst auf Homepage<a href ="../index.php">anmelden</a>');
}

########### Einbinden von Datenbankverbindung und Funktionen ###########
# Einbindung der Datenbankverbindung
require("../dbconn.php");
# Einbindung von Zusatzfunktionen
require("../funktionen.php");


################# Formulardaten empfangen ################
# alle Formularfelder empfangen und Variablen merken
$gid = $_REQUEST['gid'];
$d_id = $_POST['d_id'];
$m_id = $_POST['m_id'];
$gericht_name = trim($_POST['gericht_name']);
$beschreibung = trim($_POST['beschreibung']);
$monat = trim($_POST['monat']);
$datum = trim($_POST['datum']);
$zubereitungszeit = ($_POST['zubereitungszeit']);
$zutaten = trim($_POST['zutaten']);
$bild = trim($_POST['bild']);
$button = $_POST['button'];

//echo "\$gid: ".$gid."<br />";

################ Prüfung und Verarbeitung der Formulardaten ###############

# wurden Formulardaten empfangen?
if (strlen($button) > 1) {


    ################ Prüfung der Formulardaten ##################
    if ($m_id == 0) {
        $meldung[] = 'Bitte geben sie einen Monat an';
    }
    if ($d_id == 0) {
        $meldung[] = 'Bitte geben sie ein Datum an';
    }
    if (strlen($gericht_name) == 0) {
        $meldung[] = 'Bitte geben sie ein Gerichtname an';
    }
    if (strlen($beschreibung) == 0) {
        $meldung[] = 'Bitte geben sie eine Anleitung für die Zubereitung an';
    }
    if (strlen($zutaten) == 0) {
        $meldung[] = 'Bitte geben sie die Zutaten, getrennt durch kommas, an';
    }
    # zubereitungszeit angegeben aber kein zahlenwert
    if (strlen($zubereitungszeit) > 0 && !is_numeric($zubereitungszeit)) {
        $meldung[] = 'Bitte geben sie einen Zahlenwert für die Zubereitungszeit an';
    }


    ################ Verarbeitung der Formulardaten #############

    if (!isset($meldung)) {

        ###### daten für sql aufarbeiten #####
        
        #keine dauer angegeben, dann null in der datenbank
        if (strlen($zubereitungszeit) > 0) {
            $sqldauer = $zubereitungszeit;
        } else {
            $sqldauer = 'NULL';
        }
        # hochkommas aus text entschärfen
        //$sqltitel = mysqli_escape_string($pdo, $gericht_name);
        //$sqlbeschreibung = mysqli_escape_string($pdo, $beschreibung);

        ########## datensatz speichern ############
        
        #neues Gericht, dann INSERT
        if ($gid == 'neu') {

            #neuen datensatz anlegen
            $sql = "INSERT INTO gericht ( datum_id, monat_id, gericht_name, bild, zutaten, zubereitungszeit, beschreibung)
                       VALUES ($d_id, $m_id, '$gericht_name', '$bild', '$zutaten', $zubereitungszeit, '$beschreibung')";

            $statement = $pdo->query($sql);
        }
        # vorhandenes Gericht, dann UPDATE
        elseif (is_numeric($gid)) {

            $sql = "UPDATE gericht SET datum_id = $d_id, monat_id= $m_id, gericht_name = '$gericht_name', zubereitungszeit = $sqldauer, Bild = '$bild', 
                        zutaten = '$zutaten', beschreibung = '$beschreibung'
                        WHERE gid = $gid";

            $statement = $pdo->query($sql);
        }
        #testausgabe
        //echo $sql;
        //echo '<p>'.mysqli_error($conn).'</p>';
        
        # wenn alles in ordnung und er gespeichert hat, springt er wieder zu Speiseliste zurück
        header('Location: gerichte.php');
    }
}

############ vorhandenes Gericht aus der Datenbank holen ###########

elseif ($gid != "neu" && strlen($gid) > 0) {

    # Gericht aus der Datenbank holen & im Formular darstellen
    $stmt = $pdo->prepare("SELECT * FROM gericht WHERE gid = $gid");

    //echo "\$sql: " . $sql . "<br />";

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    # Datensatz in die variablen speichern, die unten im Formular in value="" verwendet werden
    $datum = $row['datum'];
    $monat = $row['monat'];
    $gericht_name = $row['gericht_name'];
    $bild = $row['bild'];
    $zubereitungszeit = $row['zubereitungszeit'];
    $zutaten = $row['zutaten'];
    $beschreibung = $row['beschreibung'];
    $d_id = $row['datum_id'];
    $m_id = $row['monat_id'];
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>

        <meta charset="utf-8">
        <title>Gericht bearbeiten</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/admin.css">  

    </head>
    <body>

        <div class="border">
            <h1>Gericht bearbeiten</h1>
        </div>

        <div id="content" class="border">

            <div id="formular">


<?php

# gibt es Meldungen, dann ausgeben
if (isset($meldung)) {

    echo '<p class="fehler">';
    echo implode("<br>", $meldung);
    echo '</p>';
}
?>

                <form method="post" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>">

                    <section id="section_select">

                        <!-- alle Monate -->

                        <label class="form_label" for="m_id">Monat *</label>
                        <select class="form_input_select" name="m_id" id="m_id">

<?php
# Auswahlfeld für Monat, um eine Auswahl zu erzwingen
echo '<option class="form_option" value="0">Bitte auswählen</option>';

$stmt = $pdo->prepare('SELECT * FROM monat ORDER BY m_id');
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if ($row['m_id'] == $m_id){

            $selected = 'selected';
        } else {
            $selected = '';
        }
        echo '<option ' . $selected . ' class="form_option" value="' . $row['m_id'] . '">' . $row['monat'] . '</option>';
    }
unset($stmt);

?>
                        </select>
                    </section>

                    <section id="section_select2">
                        <!-- alle Wochen -->

                        <label class="form_label" for="d_id">Woche *</label>
                        <select class="form_input_select" name="d_id" id="d_id">
<?php

# Auswahlfeld für Datum, um eine Auswahl zu erzwingen
echo '<option class="form_option" value="0">Bitte auswählen</option>';

$stmt = $pdo->prepare("SELECT * FROM datum ORDER BY d_id");
$stmt->execute();

# alle Wochen als auswahlfeld
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if ($row['d_id'] == $d_id){

        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option ' . $selected . ' class="form_option" value="' . $row['d_id'] . '">' . $row['datum'] . '</option>';
}
unset($stmt);
?>
                        </select>

                    </section>

                    <section id="section_input">

                        <label class="form_label" for="gericht_name">Gericht *</label>
                        <input class="form_input" type="text" name="gericht_name" id="gericht_name" maxlength="150" value="<?php echo str_replace('"', '&quot;', $gericht_name); ?>">

                        <label class="form_label" for="zubereitungszeit">Zubereitungszeit</label>
                        <input class="form_input" type="text" name="zubereitungszeit" id="zubereitungszeit" maxlength="10" value="<?php echo $zubereitungszeit; ?>">

                        <label class="form_label" for="zutaten">Zutaten</label>
                        <textarea class="form_text" name="zutaten" id="zutaten"><?php echo $zutaten; ?></textarea>

                        <label class="form_label" for="beschreibung">Zubereitung</label>
                        <textarea class="form_text" name="beschreibung" id="beschreibung"><?php echo $beschreibung; ?></textarea>

                        <label class="form_label" for="bild">Bild</label>
                        <input class="form_input" type="text" name="bild" id="bild" maxlength="150" value="<?php echo $bild; ?>">

                    </section>

                    <!-- versteckte Felder für ID -->
                    <input type="hidden" name="gid" value="<?php echo $gid; ?>">

                    <section id="section_submit">

                        <button class="form_button" type="submit" name="button" value="speichern">Speichern</button>
                        <button class="form_button_right" type="button" name="button" value="abbrechen" onClick="window.location.href = 'gerichte.php';">Abbrechen</button>

                    </section>

                </form>

            </div>

        </div>

    </body>
</html>

<?php
#Datenbankverbindung schliessen
$pdo=NULL;
?>
