<!-- 2014 by Bootstrapaholic.de -->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kontaktformular</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/starter-template.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <img style="margin-left:-25px; max-width: 300px;" src="img/dhoch3-logo.svg">
    <div class="starter-template">
        <h3>Formular zur Beantragung lokaler Dhoch3-Räume auf der DAAD-Moodle-Plattform</h3>
        <div class="row">
            <p>&nbsp;</p>
            <div id="message"></div>

            <!-- Formular Beginn -->
            <form role="form" id="frmContact">
                <div class="col-md-6">
                    <h4>Angaben zur Person</h4>
                    <div class="form-group" id="frmGrpVorname">
                        <label for="vorname" class="control-label">Vorname *</label>
                        <input type="text" id="vorname" class="form-control" placeholder="Ihr Vorname">
                    </div>
                    <div class="form-group" id="frmGrpNachname">
                        <label for="nachname" class="control-label">Nachname *</label>
                        <input type="text" id="nachname" class="form-control" placeholder="Ihr Nachname">
                    </div>
                    <div class="form-group" id="frmGrpEmail">
                        <label for="email" class="control-label">E-Mail Adresse *</label>
                        <input type="text" id="email" class="form-control"  placeholder="Ihre E-Mail Adresse">
                    </div>
                </div>
                <div class="col-md-6">
                    <h4>Angaben zur Hochschule</h4>
                    <div class="form-group" id="frmGrpHochschule">
                        <label for="hochschule" class="control-label">Ihre Hochschule *</label>
                        <input type="text" id="hochschule" placeholder="Ihre Hochschule" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpAbteilung">
                        <label for="hochschule" class="control-label">Abteilung/Fachbereich *</label>
                        <input type="text" id="abteilung" placeholder="Ihre Abteilung/Fachbereich" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpFunktion">
                        <label for="hochschule" class="control-label">Ihre Funktion *</label>
                        <input type="text" id="funktion" placeholder="Ihre Funktion" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpStadt">
                        <label for="stadt" class="control-label">Ihre Stadt *</label>
                        <input type="text" id="stadt" placeholder="Ihre Stadt" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpLand">
                        <label for="land" class="control-label">Ihr Land *</label>
                        <input type="text" id="land" placeholder="Ihr Land" class="form-control">
                    </div>
                </div>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <div class="col-md-12">
                    <h4>Angaben zu den Modulen</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p>Wählen Sie aus dem Menü eines oder mehrere Module aus:</p>
                            <p><b>Modul 1: Fremdsprachendidaktik Deutsch</b></p>
                            <select class="form-control custom-select" id="modul1">
                            <option selected>Bitte wählen...</option>
                            <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                            <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                            <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 2: Lehr- und Unterrichtsplanung DaF</b></p>
                            <select class="form-control custom-select" id="modul2">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                                </select>
                                <p>&nbsp;</p>
                            <p><b>Modul 3: Lehren /Lernen mit elektronischen Medien</b></p>
                            <select class="form-control custom-select" id="modul3">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 4: Berufsorientierter Deutschunterricht</b></p>
                            <select class="form-control custom-select" id="modul4">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 5: Fachkommunikation Deutsch</b></p>
                            <select class="form-control custom-select" id="modul5">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 6: Wissenschaftssprache Deutsch </b></p>
                            <select class="form-control custom-select" id="modul6">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 7: Mehrsprachigkeit, Tertiärsprachendidaktik</b></p>
                            <select class="form-control custom-select" id="modul7">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <p><b>Modul 8: Fremdsprachenlehren und -lernen erforschen</b></p>
                            <select class="form-control custom-select" id="modul8">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>

                             <p>&nbsp;</p>
							                            <p><b>Modul 9: Diskursive Landeskunde / Kulturstudien Deutsch als Fremdsprache</b></p>
							                            <select class="form-control custom-select" id="modul9">
							                                <option selected>Bitte wählen...</option>
							                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
							                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
							                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>

                             <p>&nbsp;</p>
							                            <p><b>Modul 10: Literatur, ästhetische Medien und Sprache in Deutsch als Fremdsprache</b></p>
							                            <select class="form-control custom-select" id="modul10">
							                                <option selected>Bitte wählen...</option>
							                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
							                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
							                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>
                            <p>&nbsp;</p>
														   <p><b>Praxiskomponente</b></p>
														      <select class="form-control custom-select" id="praxiskomponente">
														     <option selected>Bitte wählen...</option>
														      <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
														      <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
														      <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>
                            <p>&nbsp;</p>
														    <p><b>Fachtexte zu Dhoch3 und HSK-Bände</b></p>
														     <select class="form-control custom-select" id="fachtexte">
														    <option selected>Bitte wählen...</option>
														    <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
														    <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
														    <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung</option>
                            </select>

                        </div>
                        <div class="col-md-6" style="background-color: #f2f2f2; padding:20px; margin-top: 35px;">
                            <h4>Sonstiges</h4>
                            <p>Wenn Sie keine Modulkopie, sondern einen leeren Kursraum zur eigenen Gestaltung benötigen, geben Sie das hier bitte an:</p>
                            <div class="form-group" id="frmGrpKursraum">
                                <label for="kursraum" class="control-label">Titel des Kursraumes</label>
                                <input type="text" id="kursraum" placeholder="Titel des Kursraumes" class="form-control">
                            </div>
                            <p><b>Wie möchten Sie den Kursraum nutzen?</b></p>
                            <select class="form-control custom-select" id="modulneu">
                                <option selected>Bitte wählen...</option>
                               <option value="Kurzfristige Nutzung">Kurzfristige Nutzung (Schulung / Workshop)</option>
                               <option value="Mittel-/Langfristige Nutzung">Mittel-/Langfristige Nutzung (für die konkrete Anwendung im Unterricht)</option>
                               <option value="Beides: Kurzfristige- und Mittel-/Langfristige Nutzung">Beides: Kurzfristige- und Mittel-/Langfristige Nutzung </option>
                            </select>
                            <p>&nbsp;</p>
                            <div class="form-group" id="frmGrpBeschreibung">
                                <label for="beschreibung" class="control-label">Beschreibung (wozu möchten Sie den Kursraum nutzen?) </label>
                                <textarea type="text" id="beschreibung" placeholder="Beschreibung" class="form-control"></textarea>
                            </div>
                        </div>
						<p>&nbsp;</p>
						<p><h4>Urheberrecht und Haftungsfreistellung</h4></p>
						<p> Bitte beachten Sie bei der Verwendung von urheberrechtlich geschützten Texten, Bildern und Videos in Ihren lokalen Kursräumen unbedingt die Regelungen zum Urheberrecht und der Haftungsfreistellung.</p>
						<p> Sie müssen die Quelle jeweils ausweisen (entweder direkt unter dem Text/Bild/Video oder an einer Stelle im Kursraum gesammelt hinterlegt), ein solcher Hinweis auf die Quelle reicht allein aber nicht aus.</p>
						<p>  Sie müssen die entsprechenden Nutzungsrechte einholen und dokumentieren, denn Sie selbst haften für urheberrechtswidrig
                        eingestellte Inhalte. Diesem Sachverhalt haben Sie beim ersten Login auf die DAAD-Moodle-Plattform durch das Akzeptieren der Nutzungsbedingungen zugestimmt.</p>

                    </div>
                </div>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <div class="col-md-12">
                    <div class="form-group" id="frmGrpKommentare">
                        <label for="kommentare" class="control-label">Weitere Kommentare</label>
                        <textarea type="text" id="kommentare" placeholder="Weitere Kommentare" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-12 captchacode"></div>
                    <p><strong>Bitte beantworten Sie die unten stehende Sicherheitsabfrage!</strong>
                    <p><strong>Hinweis:</strong> Mit dem Absenden des Registrierungsformulars bestätigen Sie die Verarbeitung Ihrer personenbezogenen Daten gemäß der <a href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzbestimmungen</a>.</p>
                    <p>&nbsp;</p>
                    <p><i>Sie erhalten eine Rückmeldung, sobald der Kursraum erstellt ist.</i></p>

                <div class="col-md-4">
                    <div class="form-group" id="frmGrpCaptcha">
                        <label for="captcha" class="control-label">Sicherheitsfrage: Wie viel ist "2 + Drei"? *</label>
                        <p>(Bitte nur Zahlen eingeben)</p>
                        <input type="text" id="captcha" class="form-control" placeholder="Ergebnis der o.g. Rechenaufgabe">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg btn-block">Absenden</button>
                    </div>
                </div>
            </form>
            <!-- Formular Ende -->
        </div>
    </div>
</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    $( '#frmContact').submit( function() {
        var formControl = true;

        $( '#frmGrpVorname' ).removeClass( 'has-error' );
        $( '#frmGrpNachname' ).removeClass( 'has-error' );
        $( '#frmGrpEmail' ).removeClass( 'has-error' );
        $( '#frmGrpHochschule' ).removeClass( 'has-error' );
        $( '#frmGrpAbteilung' ).removeClass( 'has-error' );
        $( '#frmGrpFunktion' ).removeClass( 'has-error' );
        $( '#frmGrpStadt' ).removeClass( 'has-error' );
        $( '#frmGrpLand' ).removeClass( 'has-error' );
        $( '#frmGrpCaptcha' ).removeClass( 'has-error' );

        var vorname = $( '#vorname' );
        var nachname = $( '#nachname' );
        var email = $( '#email' );
        var hochschule = $( '#hochschule' );
        var abteilung = $( '#abteilung' );
        var funktion = $( '#funktion' );
        var stadt = $( '#stadt' );
        var land = $( '#land' );
        var modul1 = $( '#modul1' );
        var modul2 = $( '#modul2' );
        var modul3 = $( '#modul3' );
        var modul4 = $( '#modul4' );
        var modul5 = $( '#modul5' );
        var modul6 = $( '#modul6' );
        var modul7 = $( '#modul7' );
        var modul8 = $( '#modul8' );
        var modul9 = $( '#modul9' );
         var modul10 = $( '#modul10' );
         var praxiskomponente = $( '#praxiskomponente' );
         var fachtexte = $( '#fachtexte' );

        var kursraum = $( '#kursraum' );
        var beschreibung = $( '#beschreibung' );
        var modulneu = $( '#modulneu' );
        var kommentare = $( '#kommentare' );
        var captcha = $( '#captcha' );

        if(vorname.val() == '') {
            formControl = false;
            $( '#frmGrpVorname' ).addClass( 'has-error' );
        }

        if(nachname.val() == '') {
            formControl = false;
            $( '#frmGrpNachname' ).addClass( 'has-error' );
        }

        if(hochschule.val() == '') {
            formControl = false;
            $( '#frmGrpHochschule' ).addClass( 'has-error' );
        }

        if(abteilung.val() == '') {
            formControl = false;
            $( '#frmGrpAbteilung' ).addClass( 'has-error' );
        }

        if(funktion.val() == '') {
            formControl = false;
            $( '#frmGrpFunktion' ).addClass( 'has-error' );
        }

        if(stadt.val() == '') {
            formControl = false;
            $( '#frmGrpStadt' ).addClass( 'has-error' );
        }

        if(land.val() == '') {
            formControl = false;
            $( '#frmGrpLand' ).addClass( 'has-error' );
        }

        if(validateEmail(email.val()) == false) {
            formControl = false;
            $( '#frmGrpEmail' ).addClass( 'has-error' );
        }

        if(captcha.val() != '5') {
            formControl = false;
            $( '#frmGrpCaptcha' ).addClass( 'has-error' );
        }

        if(formControl) {
            $.ajax({
                type: "POST",
                url: "php/senden.php",
                data: { vorname:vorname.val(),
                        nachname:nachname.val(),
                        email:email.val(),
                        hochschule:hochschule.val(),
                        abteilung:abteilung.val(),
                        funktion:funktion.val(),
                        stadt:stadt.val(),
                        land:land.val(),
                        modul1:modul1.val(),
                        modul2:modul2.val(),
                        modul3:modul3.val(),
                        modul4:modul4.val(),
                        modul5:modul5.val(),
                        modul6:modul6.val(),
                        modul7:modul7.val(),
                        modul8:modul8.val(),
                        modul9:modul9.val(),
                        modul10:modul10.val(),
                        praxiskomponente:praxiskomponente.val(),
                        fachtexte:fachtexte.val(),
                        kursraum:kursraum.val(),
                        beschreibung:beschreibung.val(),
                        modulneu:modulneu.val(),
                        kommentare:kommentare.val(),
                }

            }).done(function(msg) {
                $( '#message' ).addClass( 'alert' );
                $( '#message' ).addClass( 'alert-success' );
                $( '#message').html( msg );
                window.location.href = 'vielendank.html';
            });
        }

        return false;
    } );
</script>
</body>
</html>