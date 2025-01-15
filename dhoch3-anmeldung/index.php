<!-- Entwickler: © CubesITes | Dominik Cuber | https://cubesites.de/ -->
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
        <div class="row">
            <div id="message"></div>

            <!-- Formular Beginn -->

            <form role="form" id="frmContact">
                <div class="col-md-6">
                    <div class="form-group" id="frmGrpVorname">
                        <label for="vorname" class="control-label">Vorname *</label>
                        <input type="text" id="vorname" class="form-control" placeholder="Ihr Vorname">
                    </div>
                    <div class="form-group" id="frmGrpNachname">
                        <label for="nachname" class="control-label">Nachname *</label>
                        <input type="text" id="nachname" class="form-control" placeholder="Ihr Nachname">
                    </div>
                    <div class="form-group" id="frmGrpEmail">
                        <label for="email" class="control-label">E-Mail Adresse * ¹</label>
                        <input type="text" id="email" class="form-control"  placeholder="Ihre E-Mail Adresse">
                    </div>
                    <div class="form-group" id="frmGrpHochschule">
                        <label for="hochschule" class="control-label">Hochschule *</label>
                        <input type="text" id="hochschule" placeholder="Ihre Hochschule" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" id="frmGrpInstitut">
                        <label for="institut" class="control-label">Institut *</label>
                        <input type="text" id="institut" placeholder="Ihr Institut" class="form-control">
                    </div>

                    <div class="form-group" id="frmGrpFunktion">
                        <label for="funktion" class="control-label">Funktion (z.B. Dozentin/Dozent DaF, Masterstudierende DaF, etc.) *</label>
                        <input type="text" id="funktion" placeholder="Ihre Funktion" class="form-control">
                    </div>

                    <div class="form-group" id="frmGrpGruppe">
                        <label for="gruppe" class="control-label">Gehören Sie einer oder mehrerer der folgenden Gruppen an? *</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="chkOrtslektorin" name="gruppe" value="Lektor/in (DAAD)">
                            <label class="form-check-label" for="chkOrtslektorin">Lektor/in (DAAD)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="chkRegelLektorin" name="gruppe" value="Ortslektor/in (DAAD)">
                            <label class="form-check-label" for="chkRegelLektorin">Ortslektor/in (DAAD)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="chkLehrAssistentin" name="gruppe" value="Sprach-/Lehr-Assistent/in (DAAD)">
                            <label class="form-check-label" for="chkLehrAssistentin">Sprach-/Lehr-Assistent/in (DAAD)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="chkKeineGruppe" name="gruppe" value="Keine der genannten Gruppen">
                            <label class="form-check-label" for="chkKeineGruppe">Keine der genannten Gruppen</label>
                        </div>
                    </div>

                    <div class="form-group" id="frmGrpStadt">
                        <label for="stadt" class="control-label">Stadt (Hochschulstandort) *</label>
                        <input type="text" id="stadt" placeholder="Ihre Stadt" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpLand">
                        <label for="land" class="control-label">Land (Hochschulstandort) *</label>
                        <input type="text" id="land" placeholder="Ihr Land" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <p><em>Mit * markierte Felder sind Pflichtfelder</em></p>
                    <p><em>¹ Falls Sie bereits zu einem anderen Bereich auf der DAAD-Moodle-Plattform Zugang haben, nutzen Sie bitte die gleiche Email-Adresse</em></p>
                </div>
                <div class="col-md-12 captchacode">
                    <p>Falls Sie an einer Hochschule in Deutschland lehren, füllen Sie bitte zusätzlich die folgenden Felder aus:</p>
                </div>
                <div class="col-md-6">
                    <div class="form-group" id="frmGrpLehrstuhlinhaber">
                        <label for="lehrstuhlinhaber" class="control-label">Lehrstuhlinhaber</label>
                        <input type="text" id="lehrstuhlinhaber" placeholder="Name des Lehrstuhlinhabers" class="form-control">
                    </div>
                    <div class="form-group" id="frmGrpWebsite">
                        <label for="website" class="control-label">Nach Möglichkeit stellen Sie bitte einen Link zur Website des Lehrstuhls ein</label>
                        <input type="text" id="website" placeholder="Website des Lehrstuhls (url)" class="form-control">
                    </div>
                </div>
                <div class="col-md-12 captchacode">
                    <p><strong>Hinweis:</strong> Mit dem Absenden des Registrierungsformulars bestätigen Sie die Verarbeitung Ihrer personenbezogenen Daten gemäß der <a href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzbestimmungen</a>.</p>
                    <p>&nbsp;</p>
                    <p><i>Sie erhalten Ihre Anmeldedaten an Werktagen innerhalb von 48 Stunden</i></p>
                </div>
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
        $( '#frmGrpInstitut' ).removeClass( 'has-error' );
        $( '#frmGrpFunktion' ).removeClass( 'has-error' );
        $( '#frmGrpStadt' ).removeClass( 'has-error' );
        $( '#frmGrpLand' ).removeClass( 'has-error' );
        $( '#frmGrpCaptcha' ).removeClass( 'has-error' );
        $( '#frmGrpLehrstuhlinhaber' ).removeClass( 'has-error' );
        $( '#frmGrpWebsite' ).removeClass( 'has-error' );


        var vorname = $( '#vorname' );
        var nachname = $( '#nachname' );
        var email = $( '#email' );
        var hochschule = $( '#hochschule' );
        var institut = $( '#institut' );
        var funktion = $( '#funktion' );
        var stadt = $( '#stadt' );
        var land = $( '#land' );
        var lehrstuhlinhaber = $( '#lehrstuhlinhaber' );
        var website = $( '#website' );
        var captcha = $( '#captcha' );
        var gruppe = $('input[name="gruppe"]:checked');


        // Überprüfe, ob mindestens eine Checkbox ausgewählt ist
        if (gruppe.length === 0) {
            formControl = false;
            $('#frmGrpGruppe').addClass('has-error');
        } else {
            // Wenn mindestens eine Checkbox ausgewählt ist, entferne die Fehlerklasse
            $('#frmGrpGruppe').removeClass('has-error');
        }

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
        if(institut.val() == '') {
            formControl = false;
            $( '#frmGrpInstitut' ).addClass( 'has-error' );
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
        console.log("Funktion values:", gruppe.map(function() { return this.value; }).get().join(','));
        if(formControl) {
            $.ajax({
                type: "POST",
                url: "php/senden.php",
                data: { vorname:vorname.val(),
                        nachname:nachname.val(),
                        email:email.val(),
                        hochschule:hochschule.val(),
                        institut:institut.val(),
                        funktion:funktion.val(),
                        gruppe: $('input[name="gruppe"]:checked').map(function () { return this.value; }).get().join(', '),
                        stadt:stadt.val(),
                        land:land.val(),
                        lehrstuhlinhaber:lehrstuhlinhaber.val(),
                        website:website.val(), 
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
