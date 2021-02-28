<?php

# Datenbankverbindung

require 'dbconn.php';

# URL-/GET-Parameter mit der Filter-Funktion auslesen
$gid = filter_input(INPUT_GET,'gid', FILTER_VALIDATE_INT);
$datum = filter_input(INPUT_GET, 'datum', FILTER_VALIDATE_INT);
$m_id = filter_input(INPUT_GET, 'm_id', FILTER_VALIDATE_INT);

# TCPDF Library einbinden
require_once('TCPDF-master/tcpdf.php');

# PDF document erstellen
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

# Metadaten
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Evelien Dexters');
$pdf->SetTitle('Gericht Details');

# Header & Footer
$pdf->setHeaderData('', 0, 'Speiseplan.com', 'Kochen einfach gemacht!', array(237,120,106), array(237,120,106));
$pdf->setHeaderMargin(15);
$pdf->setFooterMargin(15);
$pdf->SetMargins( 20, 30);
$pdf->SetHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN,));
$pdf->SetFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA,));
$pdf->SetPrintHeader(true);
$pdf->SetPrintFooter(true);


$pdf->SetAutoPageBreak(true, 15);
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetFont('helvetica', '', 11);

# PDF Seite Inhalt schreiben
$pdf->AddPage();

$pdf->SetFontSize(24);
$pdf->Cell(0, 20, 'Ihr Gericht:', 0, 1, 'C');
$pdf->SetFontSize(11);

# Detailansicht ausgewähltes Gericht
$sql = "SELECT * FROM alle_gerichte WHERE gid = $gid";
    
$statement = $pdo->query($sql);

foreach ($statement as $datensatz) {
$content = '
        <header>
        <h2><strong>' . $datensatz['Gericht'] . '</strong></h2>
        </header>
        <span class="image featured"><img src="bildergerichte/' . $datensatz['bild'] . '" alt=""/></span>

        <h3>Zutaten</h3>
        <p>' . nl2br($datensatz['zutaten']) . '</p>

        <h3>Zubereitungszeit: ' . $datensatz['zubereitungszeit'] . ' Min </h3>

        <h3>Zubereitung</h3>
        <p>' . nl2br($datensatz['beschreibung']) . '</p>';
    }


$pdf->writeHTML($content);

# PDF erstellen
$pdf->Output("Gericht.pdf", "I");

?>