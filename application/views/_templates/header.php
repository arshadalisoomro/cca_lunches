<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CCA Lunch Ordering</title>
    <meta name="description" content="The Chandler Christian Academy Lunch Ordering System">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
	
	<meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo URL; ?>public/img/favicon-144.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL; ?>public/img/favicon-152.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo URL; ?>public/img/favicon-152.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL; ?>public/img/favicon-144.png">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo URL; ?>public/img/favicon-120.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL; ?>public/img/favicon-114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL; ?>public/img/favicon-72.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL; ?>public/img/favicon-57.png">
    <link rel="icon" href="<?php echo URL; ?>public/img/favicon-32.png" sizes="32x32">
	
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo URL; ?>public/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="<?php echo URL; ?>public/select2/select2.css">
    <link rel="stylesheet" href="<?php echo URL; ?>public/css/common1.css" />
    <?php if (Session::get('user_account_type') == ACCOUNT_TYPE_ADMIN):?>
    <link rel="stylesheet" href="<?php echo URL; ?>public/css/admin1.css">
    <?php endif; ?>
    <script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.min.js"></script>
</head>
<body>

<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
           <!-- <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>-->
            <a class="navbar-img" href="<?php echo URL; ?>"><img src="<?php echo URL; ?>public/img/cca.png"></a>
            <a class="navbar-lunchtext" href="<?php echo URL; ?>">Lunch Ordering</a>
        </div>
        <!--<div class="navbar-collapse collapse">-->

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Main Menu <b class="caret"></b></a>
                    <ul class="dropdown-menu">

                        <?php if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN):?>
                        <li<?php if ($this->checkForActiveController($filename, "index")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>">Home</a></li>
                        <li<?php if ($this->checkForActiveController($filename, "calendar")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>calendar/index">Order Lunches</a></li>
                        <?php endif; ?>

                        <li<?php if ($this->checkForActiveController($filename, "account")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>account/index">My Account</a></li>
                        <li<?php if ($this->checkForActiveController($filename, "reports")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>reports/index">Lunch Report</a></li>

                        <?php if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN):?>
                        <li<?php if ($this->checkForActiveController($filename, "contactus")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>contactus/index">Contact Us</a></li>
                        <?php endif; ?>

                        <?php if (Session::get('user_account_type') == ACCOUNT_TYPE_ADMIN):?>
                            <li class="divider"></li>
                            <li<?php if ($this->checkForActiveController($filename, "accmaint")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>accmaint/index">Account Maintenance</a></li>
                            <li<?php if ($this->checkForActiveController($filename, "schedule")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>schedule/index">Schedule Lunches</a></li>
                            <li<?php if ($this->checkForActiveController($filename, "calendar")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>calendar/index">Order Lunches</a></li>
							<li<?php if ($this->checkForActiveController($filename, "payments")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>payments/index">Receive Payments</a></li>
                            <li<?php if ($this->checkForActiveController($filename, "lunchorders")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>lunchorders/index">Lunch Order Maintenance</a></li>
                            <li<?php if ($this->checkForActiveController($filename, "admreports")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>admreports/index">Admin Reports</a></li>
                            <li<?php if ($this->checkForActiveController($filename, "sendemails")) { echo ' class="active"'; } ?>><a href="<?php echo URL; ?>sendemails/index">Send Emails To Users</a></li>
                        <?php endif; ?>

                        <?php if (Session::get('user_logged_in') == true):?>
                            <li class="divider"></li>
                            <li><a href="<?php echo URL; ?>login/logout">Logout</a></li>
                        <?php endif; ?>

                    </ul>
                </li>
            </ul>
        <!--</div>-->
    </div>
</div>

<!--[if lt IE 9]>
<div class="container alert alert-danger">
    You are using an <b>outdated</b> version of Internet Explorer.<br /><br />You must <a style="color:#3366ff;" href="http://browsehappy.com/">upgrade your browser</a> to use this system.</p>
</div>
<![endif]-->

<noscript>
    <div class="container alert alert-danger">
        <p>Javascript and cookies must be enabled in your browser to run the CCA Lunch Ordering System.</p>
        <br />
        <p><a style="color:#3366ff;" href="http://www.enable-javascript.com/">Enable Javascript</a></p>
        <p><a style="color:#3366ff;" href="http://www.whatarecookies.com/enable.asp">Enable Cookies</a></p>
    </div>
</noscript>