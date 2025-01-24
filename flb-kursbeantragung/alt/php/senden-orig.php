<?php
$empfaenger = "support@inccas.de"; //Hier die E-Mail
$absendername = "Dhoch3 Kontaktformular";
$absendermail = $_POST['email'];
$betreff = "Dhoch3 MOODLE Anfrage";
$text = "
     Vorname: ".$_POST['vorname']."\n
     Nachname: ".$_POST['nachname']."\n
     E-Mail: ".$_POST['email']."\n
     Hochschule: ".$_POST['hochschule']."\n
     Stadt: ".$_POST['stadt']."\n
     Land: ".$_POST['land']."\n";
mail($empfaenger, $betreff, $text, "From: $absendername <$absendermail>");

echo("Das Formular wurde erfolgreich versendet");
?>