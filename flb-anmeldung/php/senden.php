<?php
     $headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n" .
     "From: FLB <noreply@moodle.daad.de>" . "\r\n";

     $empfaenger = "support@inccas.de, ".$_POST['email'].""; //Hier die E-Mail
     $absendername = "FLB Kontaktformular";
     $absendermail = $_POST['email'];
     $betreff = "FLB MOODLE Anfrage";
     $text = "
          Vielen Dank für Ihre Anmeldung. Folgende Informationen wurden übermittelt:\n\n
          Vorname: ".$_POST['vorname']."\n
          Nachname: ".$_POST['nachname']."\n
          E-Mail: ".$_POST['email']."\n
          Hochschule: ".$_POST['hochschule']."\n
          Lektorin/Lektor seit: ".$_POST['lektor']."\n
          Stadt: ".$_POST['stadt']."\n
          Land: ".$_POST['land']."\n";

     mail($empfaenger, $betreff, $text, $headers);

     echo("Das Formular wurde erfolgreich versendet");
?>