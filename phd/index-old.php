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
    <br><img src="https://moodle.daad.de/phd/rig-logo.svg" width="347" height="176">
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h1>How to apply for a PhD in Germany – Online course</h1>
            <p>Thank you for your interest in our online course! Anyone interested in the course is welcome to participate. You will need about six to nine hours to complete the course. </p>

            <p>In this course you will learn about:</p>
            <ul>
                <li>how to find a doctoral position,</li>
                <li>application requirements and how to write a good application,</li>
                <li>characteristics of the German research landscape,</li>
                <li>and how to find a doctoral supervisor.</li>
            </ul>


            <p>We wish you good luck and productive learning! </p>

        </div>
        <div class="col-md-6">
            <img class="img-responsive" src="https://moodle.daad.de/phd/startpage.jpg" alt="" width="100%" height="">
        </div>
  </div>
  <hr/>
  <p><i>For an ideal mobile presentation, please use the preferred web browser on your mobile device.</i></p>
  <div class="form-row">
    <div class="col-md-5">
        <br>
        <form action="https://moodle.daad.de/login/index_phd.php?lang=en" method="post" name="form" id="loginform">
            <input type="hidden" name="logintoken" value="<?php echo s(\core\session\manager::get_login_token()); ?>" />
            <div class="form-group">
                <input class="form-control" type="text" name="username" size="15" placeholder="Username"/>
            </div>
            <div class="form-group">
                <input class="form-control" type="password" name="password" size="15" placeholder="Password"/>
            </div> 
                <button type="submit" id="submitButton" class="btn btn-danger btn-lg btn-block"><b>Login</b></button>
        </form>           
        <div class="form-group">
            <p><a href="https://moodle.daad.de/login/forgot_password.php?lang=en">Forgotten your username or password?</a></p>
        </div> 
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-5">
        <br>
        <h4>If you are not registered yet, you can create a new account by clicking the button below.<h4>
        <br>
        <a href="https://moodle.daad.de/login/signup.php?lang=en"><button type="button" id="submitButton" class="btn btn-danger btn-lg btn-block"><b>Create new account</b></button></a>
    </div>
  </div>

</div>

<div class="col-md-12" style="border-top: 2px solid #007bff  ;display:block; padding:15px; margin-top:10px">

    <div class="container">
    <div class="row">
        <div class="col-md-5">
            <br>
             <a href="https://www.daad.de/en/" target="blank"><img style="margin-top:-25px; margin-left:-35px" src="daad-footer-logo.svg" width="100%" height="auto"></a>
		</div>
        <div class="col-md-2"></div>
		<div class="col-md-5">
		<br><br>
            
        </div>
    </div>
    </div>
</div>
</div>
<div class="col-md-12" style="background:#9e9e9e;display:block; padding:15px;">
    <div class="container">
        <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/datenschutzerklaerung-moodle-daad-en.pdf" target="blank">Data Privacy Statement</a><span>&nbsp;|&nbsp;</span><a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/daad-moodle-impressum-en.pdf" target="blank">Imprint</a> | <a style="color:#fff!important" href="https://moodle.daad.de/infos-daad/nutzungsbedingungen.pdf" target="blank">Terms of Use</a>
    </div>
</div>