<?php
##### Funktionssammlung ######
#
# Autor: Michael Hassel
# Email: hassel@mediakontur.de
# Stand: 12.02.2013
# Version: Basisversion
# für Schulungszwecke

# Überprüft, ob ein Bild existiert und gibt das <img>-Tag aus
function return_img( $bild, $pfad = "", $default = "" ){

    # Dateiendung des Bildes ermitteln
    $ext = strtolower( pathinfo($bild,PATHINFO_EXTENSION) );
    # Dateiendung des Default-Bildes ermitteln
    $defext = strtolower( pathinfo($default,PATHINFO_EXTENSION) );
    
    # ist ein Bild angegeben, dann Bild mit img-Tag zurückgeben
    if( $bild && is_file($pfad.$bild) && $ext == "jpg" ){

        return "<img src=\"$pfad$bild\" />";
    }
    # ist ein Default-ild angegeben, dann Default-Bild mit img-Tag zurückgeben 
    elseif( $default && is_file($default) && $defext == "jpg" ){
        
        return "<img src=\"$default\" />";
    }
}

# deutsches Datum in MySQL-Datum umwandeln mit Prüfung auf Gültigkeit
function germandate_to_mysql($datum){

    # Datum nach . zerlegen
    $dsplit = explode(".",$datum);
    # Keine drei Werte?
    if(count($dsplit) != 3) return false;
    # keine numerischen Werte?
    if(!is_numeric($dsplit[0]) || !is_numeric($dsplit[1]) || !is_numeric($dsplit[2])) return false;
    # Tag und Monat nicht zweistellig? Jahr nicht vierstellig?
    if(strlen($dsplit[0]) != 2 || strlen($dsplit[1]) != 2 || strlen($dsplit[2]) != 4) return false;
    # kein gültiges Datum?
    if(!checkdate($dsplit[1],$dsplit[0],$dsplit[2])) return false;

    # Datum in MySQL-Format wandeln 
    return $dsplit[2]."-".$dsplit[1]."-".$dsplit[0];

}


# MySQL-Datum in deutsches Datum umwandeln
function mysqldate_to_german($mysqldate){

    # MySQl-Datum nach "-" aufteilen
    $dsplit = explode("-",$mysqldate);
    # deutsches Datum zurückgeben
    return $dsplit[2].".".$dsplit[1].".".$dsplit[0];
}

# Funktion zur Emailüberprüfung
function checkemail($email){

    # minimale oder maximale Anzahl an Zeichen ereicht?
    if( strlen($email) < 6 || strlen($email) > 255 ) return false;

    # Email am @-Zeichen zerlegen
    # Ergebnis ist ein Array
    $at_explode = explode( "@", $email );
    # gab es mehr oder weniger als ein @-Zeichen?
    if( count($at_explode) != 2 ) return false;

    # besteht der Benutzerteil aus weniger als 1 oder mehr als 64 Zeichen?
    if( strlen($at_explode[0]) < 1 || strlen($at_explode[0]) > 64 ) return false;

    # besteht der Domainteil aus weniger als 4 oder mehr als 255 Zeichen?
    if( strlen($at_explode[1]) < 4 || strlen($at_explode[1]) > 255 ) return false;

    # Domainteil am Punkt zerlegen
    $dot_explode = explode( ".", $at_explode[1] );
    # bei weiteren Prüfungen ist localhost ausgenommen
    if( $at_explode[1] != "localhost" ){

        # Gibt es weniger als einen Punkt?
        if( count($dot_explode) < 2 ) return false;

        # letzter Index des TLD-Teils
        $tld_index = count($dot_explode) - 1;
        # TLD hat weniger als 2 Zeichen oder mehr als 6 Zeichen 
        if( strlen($dot_explode[$tld_index]) < 2 || strlen($dot_explode[$tld_index]) > 6 ) return false;
    }

    # bis hier her alles gut
    return true;

}

//prüft eine Emailadresse (regulärer Ausdruck) 
//Autor: Christian Kruse
//http://aktuell.de.selfhtml.org/tippstricks/programmiertechnik/email/

function checkemail_reg($email) {

    // RegEx begin
    $nonascii      = "\x80-\xff"; # Non-ASCII-Chars are not allowed

    $nqtext        = "[^\\\\$nonascii\015\012\"]";
    $qchar         = "\\\\[^$nonascii]";

    $protocol      = '(?:mailto:)';

    $normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
    $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
    $user_part     = "(?:$normuser|$quotedstring)";

    $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
    $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
    $dom_tldpart   = '[a-zA-Z]{2,5}';
    $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";

    $regex         = "$protocol?$user_part\@$domain_part";
    // RegEx end

    return preg_match("/^$regex$/",$email);
}

# Aufgabe 1 benutzerdefinierte Funktionen
function temperatur($grad,$modus="f"){

    # $grad ist kein numerischer Wert, dann false zurückgeben
    if( !is_numeric($grad) ) return false;
    # $modus ist weder f noch c
    if( $modus != "f" && $modus != "c" ) return false;

    # Modus Fahrenheit?
    if($modus == "f"){
      
      $ergebnis = (($grad * 9) /5) + 32;
      return $ergebnis;
    
    }else{
      
      $ergebnis = (($grad - 32) * 5) / 9;
      return $ergebnis;
      
    }    
}

# Aufgabe 2 benutzerdefinierte Funktionen
function distanz($distanz,$modus="m"){

    # $grad ist kein numerischer Wert, dann false zurückgeben
    if( !is_numeric($distanz) ) return false;
    # $modus ist weder f noch c
    if( $modus != "m" && $modus != "k" ) return false;


    # Modus Meilen?
    if($modus == "m"){
      
      $ergebnis = $distanz / 1.609;
      return $ergebnis;
    
    }else{
      
      $ergebnis = $distanz * 1.609;
      return $ergebnis;
      
    }    
}




?>