<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src='https://www.google.com/recaptcha/api.js'></script>
<style type="text/css">
.btn-danger {
    background-color: #FFCC00 !important;
    color: #4c5059 !important;
	border: none !important;
}
h1 {
	color: #33363C !important;
}
</style>

<?php


require_once('../config.php');
global $CFG, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Form name');
$PAGE->set_heading('Form name');
$PAGE->set_url($CFG->wwwroot.'/phd/index.php');


?>

<div class="container">
    <br><img src="https://moodle.daad.de/phd/rig-logo.svg">
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h1>Herzlich Willkommen!</h1>
            <p>Auf der interaktiven Plattform der internationalen DAAD-Akademie (iDA) finden Sie digitale Lernmaterialien des Bereichs „Englisch für Angestellte der Hochschulverwaltung“ zum flexiblen Selbststudium. Dieses kostenfreie Online-Angebot ergänzt das Portfolio der iDA an Präsenzkursen und begleitenden Publikationen.</p>
            <p>Viel Spaß und Erfolg beim Lernen und Üben!</p>
        </div>
        <div class="col-md-6">
            <img class="img-responsive" src="https://moodle.daad.de/phd/startpage.jpg" alt="" width="100%" height="">
        </div>
  </div>
  <hr/>
  <div class="form-row">
    <div class="col-md-5">
        <br>
        <h4>Falls Sie bereits ein Nutzerkonto haben, tragen Sie Ihre Zugangsdaten ein</h4>
        <br><br>
        <form action="https://moodle.daad.de/login/index.php?lang=en" method="post" name="form" id="loginform">
            <div class="form-group">
                <input class="form-control" type="text" name="username" size="15" placeholder="Username"/>
            </div>
            <div class="form-group">
                <input  class="form-control" type="password" name="password" size="15" placeholder="Password"/>
            </div> 
                <button type="submit" class="btn btn-danger btn-lg btn-block"><b>Login</b></button>
        </form>           
        <div class="form-group">
            <p><a href="https://moodle.daad.de/login/forgot_password.php?lang=en">Forgotten your username or password?</a></p>
        </div> 
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-5">
        <br><br><br><br><br>
        <h4>Falls Sie noch nicht registriert sind, können Sie hier ein Nutzerkonto anlegen<h4>
        <br><br>
        <a href="https://moodle.daad.de/login/signup.php?lang=en"><button type="button" id="submitButton" class="btn btn-danger btn-lg btn-block"><b>Create new account</b></button></a>
    </div>
  </div>

</div>

<div class="col-md-12" style="background:#bdbdbd ;display:block; padding:15px; margin-top:10px">

    <div class="container">
    <div class="row">
        <div class="col-md-7">
            <p>Finanziert durch:</p>
            <a href="https://www.bmbf.de/en/index.html" target="blank"><img src="https://moodle.daad.de/phd/BMBF_englisch.png" width="218" height="119"></a>
        </div>
        <div class="col-md-3">
                   </div>
		<div class="col-md-2">
		<br><br>
            <a href="https://www.daad.de/en/" target="blank"><img src="https://moodle.daad.de/phd/daad-logo-blue.png" width="143" height="37"></a>
        </div>
    </div>
    </div>
</div>
</div>
<div class="col-md-12" style="background:#9e9e9e;display:block; padding:15px;">
    <div class="container">
        <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzerklärung</a>
    </div>
</div>