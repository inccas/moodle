<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src='https://www.google.com/recaptcha/api.js'></script>

<style type="text/css">
.btn-primary {
    background-color: #0060af !important;
    color: #fff !important;
}
h1 {
	color: #333 !important;
}
</style>
<?php


require_once('../config.php');
global $CFG, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Form name');
$PAGE->set_heading('Form name');
$PAGE->set_url($CFG->wwwroot.'/flb/index.php');


?>

<div class="container pb-5">
    <img src="/flb/daad-logo.svg" width="150" height="auto">
    <hr style="height: 2px; background-color: #007bff; border: 0;" />
    <div class="row px-4">
        <div class="col-md-8">
            <h1>Herzlich willkommen!</h1>
            <p>Sie befinden sich im Moodle-Bereich der „Fachlichen Lektorenbetreuung“. Zutritt haben alle geförderten DAAD-Lektorinnen und -Lektoren.</p>
            <p>Mehr Infos rund um das DAAD-Lektorenprogramm auf <a href="https://www.daad.de/lektoren" target="_blank">www.daad.de/lektoren</a></p>
        </div>
        <div class="col-md-4">
            <img class="img-responsive" src="https://moodle.daad.de/flb/start-seite.png" alt="" width="100%" height="">
        </div>
  </div>
  <hr style="height: 2px; background-color: #007bff; border: 0;" />
  <p class="px-4"><i>Bitte nutzen Sie für eine optimale mobile Darstellung den Webbrowser auf Ihrem Handy.</i></p>
  <h4 style="color: #F00">Achtung: Wegen Wartungsarbeiten ist Moodle an diesem Freitag, dem 06.05.22 von 12:00 Uhr - 13:00 Uhr nicht erreichbar.</h4>
  <div class="form-row">
    <div class="col-md-5 px-4">
        <br>
        <h4>Falls Sie bereits ein Nutzerkonto für die Fachliche Lektorenbetreuung haben, tragen Sie Ihre Zugangsdaten ein</h4>
        <br><br>
        <form action="https://moodle.daad.de/login/index_flb.php" method="post" name="form" id="loginform">
            <input type="hidden" name="logintoken" value="<?php echo s(\core\session\manager::get_login_token()); ?>" />
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
        <h4>Falls Sie noch kein Nutzerkonto für die Fachliche Lektorenbetreuung haben, klicken Sie auf den Button „Registrierung“.<h4>
        <br>
        <a href="https://moodle.daad.de/flb-anmeldung/" target="_blank"><button type="button" id="submitButton" class="btn btn-primary btn-lg btn-block">Registrierung</button></a>
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
    <div class="container px-4">
        <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad.pdf" target="blank">Datenschutzerklärung</a><span>&nbsp;|&nbsp;</span><a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/daad-moodle-impressum.pdf" target="blank">Impressum</a>
    </div>
</div>