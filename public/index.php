<?php

session_start();

require_once '../source/handlers.php';

if (isset($_POST['download'])) {
  $_POST = array_map('trim', $_POST);

  if (empty($_POST['input_name']))
    $bad_inputs['name'] = true;

  if (empty($_POST['input_email']) || !filter_var($_POST['input_email'], FILTER_VALIDATE_EMAIL))
    $bad_inputs['email'] = true;

  // Check captcha
  if (empty($_POST['g-recaptcha-response'])) {
    $bad_inputs['captcha'] = true;
  } else {
    $captcha_is_valid = utilities::is_valid_recaptcha_response(
      $_POST['g-recaptcha-response'],
      constants::CAPTCHA_SECRET,
      constants::CAPTCHA_URL);

    if (!$captcha_is_valid)
      $bad_inputs['captcha'] = true;
  }

  if (empty($_POST['input_company']))
    $_POST['input_company'] = null;

  if (empty($bad_inputs)) {
    // All ok, put contact in db
    $inserted_id = database::save_contact(
        $_POST['input_name'],
        $_POST['input_email'],
        $_POST['input_company']);

    // Give access to files
    $_SESSION['token'] = utilities::get_random_string();
    $_SESSION['contact-id'] = $inserted_id;

    $ip = utilities::get_client_ip();
    $name = utilities::html_entities($_POST['input_name']);
    $email = utilities::html_entities($_POST['input_email']);
    $company = utilities::html_entities($_POST['input_company']);

    // Build notify message
    $message = "New contact.\n\n";
    $message .= "Datetime: ".date('Y-m-d H:i:s')."\n";
    $message .= "IP: ".($ip ? $ip : '-')."\n";
    $message .= "Name: ".$name."\n";
    $message .= "E-mail: ".$email."\n";
    $message .= "Company: ".($company ? $company : '-')."\n\n";
    $message .= "/".gethostname();

    mail::send('C: '.$name.', '.$email, nl2br($message), array(constants::ROOT_EMAIL));
  }
}

?>

<?php ob_clean(); ob_start('utilities::get_minified_html'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Personal website">
  <meta name="author" content="Robin Arnesson">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/grayscale.css" rel="stylesheet">
  <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

  <script src='https://www.google.com/recaptcha/api.js'></script>
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->

  <title>Robin Arnesson</title>
</head>

<!-- <body id="page&#45;top" data&#45;spy="scroll" data&#45;target=".navbar&#45;fixed&#45;top"> -->
<body id="page-top">

  <nav class="navbar navbar-custom navbar-fixed-top top-nav-collapse" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
          <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand page-scroll" href="#page-top">
          <i class="fa fa-caret-right"></i> Robin Arnesson
        </a>
      </div>
      <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
        <ul class="nav navbar-nav">
          <li class="hidden">
            <a href="#page-top"></a>
          </li>
          <li>
            <a class="page-scroll" href="#page-top">CV &amp; Grades</a>
          </li>
          <li>
            <a class="page-scroll" href="#github">Github</a>
          </li>
          <li>
            <a class="page-scroll" href="#contact">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="intro">
    <div class="intro-body">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2">
            <div class="row">
              <div class="col-lg-8 col-lg-offset-2">
                <i class="fa fa-graduation-cap fa-4x bott-margin"></i>
                <h2 style="margin-bottom: 15px;">CV &amp; grades</h2>

                <?php if (empty($_SESSION['token'])) { ?>

                <p style="margin-bottom: 10px;">Download curriculum vitae and school grades.</p>

                <form role="form" class="form-download" method="post" action="#download" novalidate>
                  <?php if (isset($bad_inputs['name'])) { ?>
                  <div class="alert alert-danger alert-cust" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    Name is missing
                  </div>
                  <?php } ?>

                  <label for="input_name" class="sr-only">Your name</label>
                  <input type="text" name="input_name" id="input_name"
                      class="form-control input-bott-margin not-rounded" placeholder="Your name"
                      value="<?php if (isset($_POST['input_name']))
                      echo utilities::html_entities($_POST['input_name']); ?>" required>

                  <?php if (isset($bad_inputs['email'])) { ?>
                  <div class="alert alert-danger alert-cust" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    Invalid e-mail address
                  </div>
                  <?php } ?>

                  <label for="input_email" class="sr-only">Your e-mail</label>
                  <input type="email" name="input_email" id="input_email"
                      class="form-control input-bott-margin not-rounded" placeholder="Your e-mail"
                      value="<?php if (isset($_POST['input_company']))
                      echo utilities::html_entities($_POST['input_email']); ?>" required>

                  <?php if (isset($bad_inputs['company'])) { ?>
                  <div class="alert alert-danger alert-cust" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    Company name is missing
                  </div>
                  <?php } ?>

                  <label for="input_company" class="sr-only">Company name</label>
                  <input type="text" name="input_company" id="input_company"
                      class="form-control input-bott-margin not-rounded" placeholder="Company name (optional)"
                      value="<?php if (isset($_POST['input_company']))
                      echo utilities::html_entities($_POST['input_company']); ?>" required>

                  <?php if (isset($bad_inputs['captcha'])) { ?>
                  <div class="alert alert-danger alert-cust" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    Are you a robot?
                  </div>
                  <?php } ?>

                  <div class="g-recaptcha" data-sitekey="<?php echo constants::CAPTCHA_KEY; ?>"></div>

                  <br />
                  <button type="submit" name="download" style="margin-top: 10px;"
                      class="btn btn-default btn-lg">Show files</button>
                </form>

                <?php } else { ?>

                <div class="list-group list-group-mod">
                  <?php
                    $files = explode(';', constants::VALID_FILES);
                    foreach ($files as $file) {
                  ?>
                  <a href="download.php?token=<?php echo $_SESSION['token']; ?>&file=<?php echo urlencode($file); ?>"
                      class="list-group-item">
                    <?php echo $file; ?>
                  </a>
                  <?php } ?>
                </div>

                <?php } ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <section id="github" class="container content-section text-center">
    <div class="row">
      <div class="col-lg-8 col-lg-offset-2">

        <i class="fa fa-github fa-4x bott-margin"></i> <h2>Github</h2>
        <p>Here are some of my personal projects.</p>
        <a class="btn btn-default btn-lg bott-margin" href="https://github.com/robinarnesson/"
            target="_blank" role="button">Go to Github</a>

      </div>
    </div>
  </section>

  <section id="contact" class="container content-section text-center">
    <div class="row">
      <div class="col-lg-8 col-lg-offset-2">

        <i class="fa fa-envelope fa-4x bott-margin"></i> <h2>Contact</h2>
        <p>
          <strong>E-mail:</strong><br />
          &#114;&#111;&#98;&#105;&#110;&#97;&#114;&#110;&#101;&#115;&#115;&#111;&#110;&#32;&#97;&#116;
          &#32;&#103;&#109;&#97;&#105;&#108;&#32;&#100;&#111;&#116;&#32;&#99;&#111;&#109;
        </p>
        <p>
          <strong>Postal address:</strong><br />
          Multr&#xE5;gatan 62<br />
          16255 V&#xC4;LLINGBY<br />
          Sweden
        </p>
        <a class="btn btn-default btn-lg bott-margin" href="http://kartor.eniro.se/m/8Rxip"
            target="_blank" role="button">Show map</a>

      </div>
    </div>
  </section>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.min.js"></script>
  <script src="js/grayscale.js"></script>

  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-63878030-1', 'auto');
    ga('send', 'pageview');
  </script>

</body>
</html>

<?php ob_flush(); ?>
