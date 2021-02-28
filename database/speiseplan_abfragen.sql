#################### PHP 1 Projekt ######################

#Alle Datums
SELECT * FROM datum ORDER BY datum;

# Ein Datum nach DatumID holen
SELECT datum FROM datum WHERE d_id = 2;

# Alle Gerichte zu einer Woche auf Startseite (zB Woche 1)
SELECT g.gid, g.gericht_name AS Gericht, g.bild, g.zubereitungszeit, g.zutaten, g.beschreibung
FROM gericht AS g
JOIN datum AS d ON d.d_id = g.datum_id
WHERE d.d_id = 1
ORDER BY g.gericht_name;

# Ein bestimmtes Gericht holen (für Detailseite)
SELECT * FROM gericht WHERE gid = 3;

# alle Gerichte, mit Zubereitungszeit weniger als 31 Minuten
SELECT gericht_name, zubereitungszeit
FROM gericht
WHERE zubereitungszeit < 31
ORDER BY zubereitungszeit;

# Alle Zutaten für ein Gericht
SELECT zutaten FROM gericht WHERE gid = 3;

# Alle Zutaten für eine Woche sammeln in Einkaufsliste (zB Woche 1)
SELECT g.gid, g.gericht_name AS Gericht, g.zutaten
FROM gericht AS g
JOIN datum AS d ON d.d_id = g.datum_id
WHERE d.d_id = 1
ORDER BY g.zutaten;

# Alle Gerichte mit Datum
SELECT g.gid AS Gerichtid, g.gericht_name AS Gericht, d.datum AS Woche
FROM gericht g
INNER JOIN datum d ON datum_id = d.d_id
ORDER BY d.datum;

# Ein Gericht verändern (update über Formular?)
UPDATE gericht SET Datum_id = 2, gericht_name = 'Gericht2', zubereitungszeit = 111, Bild = 'bild2.jpg', 
zutaten = 'milch, eier', beschreibung = 'Testbeschreibung2'
WHERE id = 1;

# neues Gericht anlegen
INSERT INTO gericht ( datum_id, gericht_name, bild, zutaten, zubereitungszeit, beschreibung)
VALUES (4, 'Milchreis', 'milchreis-aus-dem-schnellkochtopf.jpg', 'milchreis, milch, zucker', 15, 'einfach alles zusammen schmeißen und kochen');



#################### PHP 2 Projekt ######################

# VIEW erstellen mit SELECT anweisung
CREATE OR REPLACE VIEW alle_gerichte AS 
SELECT 
	m.m_id,
	m.monat AS Monat,
	d.d_id,
	d.datum AS Woche,
	g.gid,
	g.gericht_name AS Gericht,
	g.bild,
	g.zubereitungszeit,
	g.zutaten,
	g.beschreibung
FROM monat AS m
JOIN datum AS d ON m.m_id = d.monat_id
LEFT JOIN gericht AS g ON d.d_id = g.datum_id
GROUP BY g.gid;

#SELECT alle Monate
SELECT * FROM monat ORDER BY m_id;

# SELECT alle KW's für ein Monat
SELECT * FROM datum WHERE monat_id = 2
ORDER BY d_id;

# SELECT Gerichte in alle KW's für alle Monate
SELECT * FROM alle_gerichte ORDER BY d_id;

# SELECT alle Gerichte und alle KW's für einen Monat auf den View
SELECT * FROM alle_gerichte
WHERE m_id = 3
ORDER BY d_id;

# SELECT alle Gerichte für eine KW (Darstellung Homepage)
SELECT gid, Gericht, bild, zubereitungszeit, zutaten, beschreibung
FROM alle_gerichte
WHERE d_id = 10
ORDER BY Gericht;

# Ein bestimmtes Gericht holen (für Detailseite)
SELECT * FROM alle_gerichte WHERE gid = 60;

# Ein Datum nach DatumID holen
SELECT * FROM alle_gerichte WHERE d_id = 2;

# alle Gerichte, mit Zubereitungszeit weniger als 31 Minuten
SELECT gericht, bild, zubereitungszeit, beschreibung
FROM alle_gerichte
WHERE zubereitungszeit < 31
ORDER BY zubereitungszeit;

# Alle Zutaten für eine Woche sammeln in Einkaufsliste
SELECT gid, gericht, zutaten
FROM alle_gerichte
WHERE d_id = 1
ORDER BY zutaten;