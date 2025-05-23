<?php
     $headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n" .
     "From: Dhoch3 <" . $_POST['email'] . ">" . "\r\n" ;
     //"From: Dhoch3 <noreply@moodle.daad.de>" . "\r\n";

     //$empfaenger = "office@cubesites.de, " . $_POST['email'] . ""; // Hier die E-Mail
     $empfaenger = "support@inccas.de, ".$_POST['email'].""; // Hier die E-Mail
     $absendername = "Dhoch3 Kontaktformular";
     $absendermail = $_POST['email'];
     $betreff = "Dhoch3 MOODLE Anfrage";

     // Handle checkbox values
     $gruppe = isset($_POST['gruppe']) ? $_POST['gruppe'] : "";

     $text = "
     Vielen Dank für Ihre Anmeldung. Folgende Informationen wurden übermittelt:\n\n
     Vorname: " . $_POST['vorname'] . "\n
     Nachname: " . $_POST['nachname'] . "\n
     E-Mail: " . $_POST['email'] . "\n
     Hochschule: " . $_POST['hochschule'] . "\n
     Institut: " . $_POST['institut'] . "\n
     Funktion: " . $_POST['funktion'] . "\n
     Gruppe: " . $gruppe . "\n
     Stadt: " . $_POST['stadt'] . "\n
     Land: " . $_POST['land'] . "\n
     Name des Lehrstuhlinhabers: " . $_POST['lehrstuhlinhaber'] . "\n
     Website des Lehrstuhls : " . $_POST['website'] . "\n";

     mail($empfaenger, $betreff, $text, $headers);

     echo("Das Formular wurde erfolgreich versendet");
?>
