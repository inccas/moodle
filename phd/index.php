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
    redirect($CFG->wwwroot . '/course/view.php?id=1914');
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
        redirect($CFG->wwwroot . '/course/view.php?id=1914');
    } else {
        // Show an error message
        $error_message = 'Invalid login, please try again';
    }
} 
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
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="" method="post">
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
        <h2>If you are not registered yet, you can create a new account by clicking the button below.<h2>
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

<script>
    document.querySelector('.error').scrollIntoView();
</script>