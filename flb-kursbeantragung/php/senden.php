<?php
     $empfaenger = "support@inccas.de, ".$_POST['email'].""; //Hier die E-Mail
     $absendername = "FLB Kursbeantragung";
     $absendermail = $_POST['email'];
     $betreff = "FLB Kursbeantragung";
     $text = "
          Vielen Dank für Ihre FLB Kursbeantragung. Folgende Informationen wurden übermittelt:\n\n
          Vorname: ".$_POST['vorname']."\n
          Nachname: ".$_POST['nachname']."\n
          E-Mail: ".$_POST['email']."\n
          Stadt: ".$_POST['stadt']."\n
          Land: ".$_POST['land']."\n
          Gasthochschule des DAAD-Lektorats: ".$_POST['gasthochschule']."\n
          Titel des neuen Kurses: ".$_POST['kursraum']."\n
          Neuer Kurs: ".$_POST['kursneu']."\n
          Weitere Kommentare: ".$_POST['kommentare']."\n";

     mail($empfaenger, $betreff, $text, "From: $absendername <$absendermail>");

     echo("Das Formular wurde erfolgreich versendet");
?>