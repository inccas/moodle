<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src='https://www.google.com/recaptcha/api.js'></script>

<style type="text/css">
.btn-primary {
    background-color: #0060af !important;
    color: #fff !important;
}
h1 {
	color: #E98A11 !important;
}

h2 {
    font-size: 1.5rem!important;
    margin-bottom: 2.5rem!important;
}
.error{
    color:red;
    font-weight: bold;
    display:block;
    margin-bottom: 1.5rem;
}
</style>

<?php
// Include the Moodle config file
require_once('../config.php');

// Start a session
session_start();

// Check if the user is already logged in
if (isloggedin()) {
    // Redirect the user to the course they want to access
    redirect($CFG->wwwroot . '/course/index.php?categoryid=4');
}

// Get the username and password from the form
$username = optional_param('username', '', PARAM_RAW);
$password = optional_param('password', '', PARAM_RAW);

// Set a variable to track if there is an error
$error = false;

// Check if the form has been submitted
if ($username !== '' && $password !== '') {
    // Try to log in the user
    if ($user = authenticate_user_login($username, $password)) {
        // Log the user in
        complete_user_login($user);
        
        // Redirect the user to the course they want to access
        redirect($CFG->wwwroot . '/course/index.php?categoryid=4');
    } else {
        // Show an error message
        $error_message = 'Ungültige Anmeldedaten. Versuchen Sie es noch einmal!';
    }
} 
?>

<div class="container">
    <img style="margin-left:-25px; width:auto ;height:106px" src="dhoch3-logo.svg">
    <hr style="height: 2px; background-color: #007bff; border: 0;" />
    <div class="row">
        <div class="col-md-6">
            <h1>Herzlich willkommen!</h1>
            <p>Mit dem Projekt Dhoch3 unterstützt der DAAD die akademische Ausbildung künftiger Deutschlehrerinnen und Deutschlehrer in Schule und Hochschule. Das Online-Angebot ist kostenlos und richtet sich an Dozentinnen und Dozenten und deren Studierende an Hochschulen weltweit.</p>
            <p>Mehr Infos rund um Dhoch3 auf <a href="https://www.daad.de/dhoch3" target="_blank">www.daad.de/dhoch3</a></p>
        </div>
        <div class="col-md-6">
            <img class="img-responsive" src="https://moodle.daad.de/dhoch3/start-seite.jpg" alt="" width="100%" height="">
        </div>
  </div>
  <hr style="height: 2px; background-color: #007bff; border: 0;" />
  <p><i>Bitte nutzen Sie für eine optimale mobile Darstellung den Webbrowser auf Ihrem Handy.</i></p>

  <div class="form-row">
    <div class="col-md-5">
        <br>
        <h2>Falls Sie bereits ein Nutzerkonto für Dhoch3 haben, tragen Sie Ihre Zugangsdaten ein.</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <input class="form-control" type="text" name="username" size="15" placeholder="Anmeldename"/>
            </div>
            <div class="form-group">
                <input  class="form-control" type="password" name="password" size="15" placeholder="Kennwort"/>
            </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
        </form>
        <div class="form-group">
            <p><a href="https://moodle.daad.de/login/forgot_password.php">Anmeldename oder Kennwort vergessen?</a></p>
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-5">
        <br>
        <h2>Falls Sie noch kein Nutzerkonto für Dhoch3 haben, klicken Sie auf den Button „Registrierung“.<h2>
         <a href="https://moodle.daad.de/dhoch3-anmeldung/" target="_blank"><button type="button" id="submitButton" class="btn btn-primary btn-lg btn-block">Registrierung</button></a>
    </div>
  </div>

</div>

<div class="col-md-12" style="border-top: 2px solid #007bff ;display:block; padding:15px; margin-top:20px">

    <div class="container">
    <div class="row">
        <div class="col-md-5">
            <p>Gefördert durch:</p>
            <a href="https://www.auswaertiges-amt.de/DE/Startseite_node.html" target="blank"><img src="https://moodle.daad.de/dhoch3/auswaertiges-amt.svg"></a>
        </div>
		 <div class="col-md-2">
         </div>
        <div class="col-md-5">
            <br><br>
            <a href="https://www.daad.de/de/" target="blank"><img style="margin-top:-25px" src="daad-footer-logo.svg" width="100%" height="auto"></a>
        </div>

    </div>
    </div>
</div>
</div>
<div class="col-md-12" style="background:#9e9e9e;display:block; padding:15px;">
    <div class="container">
        <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzerklärung</a><span>&nbsp;|&nbsp;</span><a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/daad-moodle-impressum.pdf" target="blank">Impressum</a>
    </div>
</div>

<script>
    document.querySelector('.error').scrollIntoView();
</script>