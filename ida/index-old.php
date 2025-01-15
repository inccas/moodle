<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src='https://www.google.com/recaptcha/api.js'></script>
<style type="text/css">
.btn-cyan {
    background-color: #0BA1E2 !important;
    color: #fff !important;
}
h1 {
	color: #0BA1E2 !important;
}
</style>

<?php


require_once('../config.php');
global $CFG, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Form name');
$PAGE->set_heading('Form name');
$PAGE->set_url($CFG->wwwroot.'/ida/index.php');


?>

<div class="container">
    <img style="margin-left:-25px; width:auto ;height:106px" src="https://moodle.daad.de/ida/ida-logo.svg">
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h1>Herzlich Willkommen!</h1>
            <p>Die Internationale DAAD-Akademie bietet auf ihrer interaktiven Plattform ein umfangreiches kostenfreies Online-Angebot, welches das Portfolio der iDA an Präsenzkursen und begleitenden Publikationen stetig ergänzt. Hier finden Sie beispielsweise Zugang zu den Seminarteamräumen oder den digitalen interaktiven Lernmaterialien des Bereichs „Englisch für Angestellte der Hochschulverwaltung“ zum flexiblen Selbststudium.</p>
			<p>Darüber hinaus stellen wir Ihnen hier auch die Aufzeichnungen der iDA-Online-Fortbildungen sowie weitere Materialien zum Download zur Verfügung.</p>
            <p>Das aktuelle Jahresprogramm sowie die Inhouse-Angebote finden Sie auf <a href="http://www.daad-akademie.de/" target="_blank">www.daad-akademie.de</a>.</p>
			<p>Viel Spaß und Erfolg beim Lernen und Üben!</p>
        </div>
        <div class="col-md-6">
            <img class="img-responsive" src="https://moodle.daad.de/ida/ida-startseite.jpg" alt="" width="100%" height="">
        </div>
  </div>
  <hr/>
  <p><i>Bitte nutzen Sie für eine optimale mobile Darstellung den Webbrowser auf Ihrem Handy.</i></p>
  <div class="form-row">
    <div class="col-md-5">
        <br>
        <h4>Falls Sie bereits ein Nutzerkonto für die Internationale DAAD Akademie haben, tragen Sie Ihre Zugangsdaten ein.</h4>
        <br>
        <form action="https://moodle.daad.de/login/index_ida_cat.php" method="post" name="form" id="form">
            <input type="hidden" name="logintoken" value="<?php echo s(\core\session\manager::get_login_token()); ?>" />
            <div class="form-group">
                <input class="form-control" type="text" name="username" size="15" placeholder="Anmeldename"/>
            </div>
            <div class="form-group">
                <input  class="form-control" type="password" name="password" size="15" placeholder="Kennwort"/>
            </div>
                <button type="submit" class="btn btn-cyan btn-lg btn-block"><b>Login</b></button>
        </form>
        <div class="form-group">
            <p><a href="https://moodle.daad.de/login/forgot_password.php">Anmeldename oder Kennwort vergessen?</a></p>
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-5">
        <br>
        <h4>Falls Sie noch nicht registriert sind, können Sie hier ein Nutzerkonto anlegen.<h4>
        <br>
        <a href="https://moodle.daad.de/login/signup.php"><button type="button" id="submitButton" class="btn btn-cyan btn-lg btn-block"><b>Nutzerkonto anlegen</b></button></a>
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
        <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzerklärung</a><span>&nbsp;|&nbsp;</span><a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/daad-moodle-impressum.pdf" target="blank">Impressum</a> | <a style="color:#fff!important"href="https://moodle.daad.de/infos-daad/nutzungsbedingungen.pdf" target="blank">Nutzungsbedingungen</a>
    </div>
</div>