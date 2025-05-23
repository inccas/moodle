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

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<div class="container">

    <div class="starter-template">
        <div class="col-md-4">
            <div id="message"></div>

            <!-- Formular Beginn -->

            <form role="form" id="frmContact">
                <div class="form-group" id="frmGrpVorname">
                    <label for="vorname" class="control-label">Vorname</label>
                    <input type="text" id="vorname" class="form-control" placeholder="Ihr Vorname">
                </div>
                <div class="form-group" id="frmGrpNachname">
                    <label for="nachname" class="control-label">Nachname</label>
                    <input type="text" id="nachname" class="form-control" placeholder="Ihr Nachname">
                </div>
                <div class="form-group" id="frmGrpEmail">
                    <label for="email" class="control-label">E-Mail Adresse</label>
                    <input type="text" id="email" class="form-control"  placeholder="Ihre E-Mail Adresse">
                </div>
				
				<div class="form-group" id="frmGrpModule">
					
						<label for="module" class="control-label">Module</label>
                        <input type="text" id="module" class="form-control"  placeholder="Module">
					</div>
					
                <div class="form-group" id="frmGrpNachricht">
                    <label for="nachricht" class="control-label">Nachricht</label>
                    <textarea id="nachricht" class="form-control">Ihre Nachricht an uns...</textarea>
                </div>
                <div class="form-group" id="frmGrpCaptcha">
                    <label for="captcha" class="control-label">Wie viel ist "2 + 3"?</label>
                    <input type="text" id="captcha" class="form-control" placeholder="Ergebnis der o.g. Rechenaufgabe">
                </div>

                <div class="form-group text-right">
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">Absenden</button>
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
		$( '#frmGrpModule' ).removeClass( 'has-error' );
        $( '#frmGrpNachricht' ).removeClass( 'has-error' );
        $( '#frmGrpCaptcha' ).removeClass( 'has-error' );


        var vorname = $( '#vorname' );
        var nachname = $( '#nachname' );
        var email = $( '#email' );
		var module = $( '#module' );
        var nachricht = $( '#nachricht' );
        var captcha = $( '#captcha' );

        if(vorname.val() == '') {
            formControl = false;
            $( '#frmGrpVorname' ).addClass( 'has-error' );
        }

        if(nachname.val() == '') {
            formControl = false;
            $( '#frmGrpNachname' ).addClass( 'has-error' );
        }

        if(nachricht.val() == '') {
            formControl = false;
            $( '#frmGrpNachricht' ).addClass( 'has-error' );
        }

        if(validateEmail(email.val()) == false) {
            formControl = false;
            $( '#frmGrpEmail' ).addClass( 'has-error' );
        }

		 if(nachricht.val() == '') {
            formControl = false;
            $( '#frmGrpModule' ).addClass( 'has-error' );
        }
		
        if(captcha.val() != '5') {
            formControl = false;
            $( '#frmGrpCaptcha' ).addClass( 'has-error' );
        }

        if(formControl) {
            $.ajax({
                type: "POST",
                url: "php/senden.php",
                data: { keyword:vorname }
            }).done(function(msg) {
                $( '#message' ).addClass( 'alert' );
                $( '#message' ).addClass( 'alert-success' );
                $( '#message').html( msg );
            });
        }

        return false;
    } );
</script>
</body>
</html>
