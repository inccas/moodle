<?php
$empfaenger = "dominik.cuber@inccas.de"; //Hier die E-Mail
$absendername = "Dhoch3 Kontaktformular";
$absendermail = $_POST['email'];
$betreff = "Dhoch3 MOODLE Anfrage";
$text = "
     Vorname: ".$_POST['vorname']."\n
     Nachname: ".$_POST['nachname']."\n
     E-Mail: ".$_POST['email']."\n
     Hochschule: ".$_POST['hochschule']."\n
     Abteilung/Fachbereich: ".$_POST['abteilung']."\n
     Ihre Funktion: ".$_POST['funktion']."\n
     Stadt: ".$_POST['stadt']."\n
     Land: ".$_POST['land']."\n
     Modul1: ".$_POST['modul1']."\n";

mail($empfaenger, $betreff, $text, "From: $absendername <$absendermail>");

echo("Das Formular wurde erfolgreich versendet");
?>