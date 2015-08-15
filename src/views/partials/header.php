<?php
if (isset($_GET['errors']))
    $errors = unserialize(urldecode($_GET['errors']));
$user = AuthenticationManager::getAuthenticatedUser();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SlackLight</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap-multiselect.css" rel="stylesheet" />

    <!-- Custom Fonts -->
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-multiselect.js"></script>

</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=".">SlackLight</a>
        </div>
        <!-- Top Menu Items -->
        <ul class="nav navbar-right top-nav">

            <li class="dropdown">
                <?php if ($user == null): ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-expanded="false">
                        Not logged in!
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="?view=login">Login now</a>
                        </li>
                        <li>
                            <a href="?view=register">Register</a>
                        </li>
                    </ul>
                <?php else: ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Logged in as <strong><?php echo $user->getFirstName() . " " . $user->getLastName(); ?></strong>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <form method="post" action="<?php echo Util::action(Controller::ACTION_LOGOUT); ?>">
                                <input class="btn btn-link" role="button" type="submit" value="Logout"/>
                            </form>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>
        </ul>
        <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <?php
            require("channels.php");
        ?>
        <!-- /.navbar-collapse -->
    </nav>