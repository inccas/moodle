<?php
     $headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n" .
     "From: Dhoch3 <" . $_POST['email'] . ">" . "\r\n";

     //$empfaenger = "office@cubesites.de, " . $_POST['email'] . ""; // Hier die E-Mail
     $empfaenger = "support@inccas.de, ".$_POST['email'].""; //Hier die E-Mail
     $absendername = "Dhoch3 Modulbeantragung";
     $absendermail = $_POST['email'];
     $betreff = "Dhoch3 MOODLE Modulbeantragung";
     $text = "
          Vielen Dank für Ihre Dhoch3 Modulbeantragung. Folgende Informationen wurden übermittelt:\n\n
          Vorname: ".$_POST['vorname']."\n
          Nachname: ".$_POST['nachname']."\n
          E-Mail: ".$_POST['email']."\n
          Hochschule: ".$_POST['hochschule']."\n
          Abteilung/Fachbereich: ".$_POST['abteilung']."\n
          Ihre Funktion: ".$_POST['funktion']."\n
          Stadt: ".$_POST['stadt']."\n
          Land: ".$_POST['land']."\n
          Modul 1: ".$_POST['modul1']."\n
          Modul 2: ".$_POST['modul2']."\n
          Modul 3: ".$_POST['modul3']."\n
          Modul 4: ".$_POST['modul4']."\n
          Modul 5: ".$_POST['modul5']."\n
          Modul 6: ".$_POST['modul6']."\n
          Modul 7: ".$_POST['modul7']."\n
          Modul 8: ".$_POST['modul8']."\n
          Modul 9: ".$_POST['modul9']."\n
          Modul 10: ".$_POST['modul10']."\n
          Praxiskomponente: ".$_POST['praxiskomponente']."\n
          Fachtexte: ".$_POST['fachtexte']."\n
          Titel des neuen Kurses: ".$_POST['kursraum']."\n
          Beschreibung des neuen Kurses: ".$_POST['beschreibung']."\n
          Neues Modul: ".$_POST['modulneu']."\n
          Weitere Kommentare: ".$_POST['kommentare']."\n";

     mail($empfaenger, $betreff, $text, "From: $absendername <$absendermail>");

     echo("Das Formular wurde erfolgreich versendet");
?>