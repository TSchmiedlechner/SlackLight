<?php
require_once('inc/bootstrap.php');
require_once('views/partials/header.php');

$postAction = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
if ($postAction != null)
    Controller::getInstance()->invokePostAction();

?>
    <div id="page-wrapper">

        <div class="container-fluid">

            <?php
            
            $view = isset($_REQUEST['view']) ? $_REQUEST['view'] :
                (AuthenticationManager::isAuthenticated() ? 'overview' : 'welcome');
            $path = 'views/' . $view . '.php';
            if (file_exists($path))
                require_once($path);

            ?>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

<?php
require_once('views/partials/footer.php');
?>