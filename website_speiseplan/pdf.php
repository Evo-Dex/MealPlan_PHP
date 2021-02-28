<?php

# Datenbankverbindung

require 'dbconn.php';

# URL-/GET-Parameter mit der Filter-Funktion auslesen
$datum = filter_input(INPUT_GET, 'datum', FILTER_VALIDATE_INT);
$m_id = filter_input(INPUT_GET, 'm_id', FILTER_VALIDATE_INT);

# TCPDF Library einbinden
require_once('TCPDF-master/tcpdf.php');

# PDF document erstellen
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

# Metadaten
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Evelien Dexters');
$pdf->SetTitle('Ihre Einkaufsliste');

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
$pdf->Cell(0, 20, 'Einkaufsliste', 0, 1, 'C');
$pdf->SetFontSize(11);

# ausgewähltes Datum darstellen
$sql = "SELECT datum FROM datum WHERE d_id = $datum";
$statement = $pdo->query($sql);

foreach ($statement as $datensatz) {

    $title = '<h2>Ihre Gerichte für den '. $datensatz['datum'] .'</h2><br>';
}
$pdf->writeHTML($title);

# ausgewählte gerichte + zutaten in tabelle darstellen
$sql = "SELECT gid, gericht, zutaten
            FROM alle_gerichte
            WHERE d_id = $datum
            ORDER BY zutaten";
$statement2 = $pdo->query($sql);

$content = '
    <table cellpadding="3" border="1">
    <tr>
        <th><h3>Gericht</h3></th>
        <th><h3>Zutaten</h3></th>
    </tr>';
   foreach ($statement2 as $datensatz) {
        $content .= '<tr>
                <td>' . $datensatz['gericht'] . '</td>
                <td>' . nl2br($datensatz['zutaten']) . '</td>
            </tr>';
    }
$content .= '</table>';

$pdf->writeHTML($content);

# PDF erstellen
$pdf->Output("Einkaufsliste.pdf", "I");

?>