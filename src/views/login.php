<?php

if (AuthenticationManager::isAuthenticated())
    Util::redirect('.');

$userName = isset($_REQUEST['userName']) ? $_REQUEST['userName'] : null;

?>


    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Login
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Please fill out the form below to login:
                </div>
                <div class="panel-body">

                    <form class="form-horizontal" method="post"
                          action="<?php echo Util::action(Controller::ACTION_LOGIN, array('view' => $view)); ?>">
                        <div class="form-group">
                            <label for="<?php echo Controller::USR_NAME; ?>" class="col-sm-2 control-label">User
                                name:</label>

                            <div class="col-sm-8">
                                <input type="text" required class="form-control"
                                       name="<?php echo Controller::USR_NAME; ?>"
                                       placeholder="try 'toms'" value="<?php echo htmlentities($userName); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="<?php echo Controller::USR_PASSWORD; ?>"
                                   class="col-sm-2 control-label">Password</label>

                            <div class="col-sm-8">
                                <input type="password" required class="form-control" id="inputPassword"
                                       name="<?php echo Controller::USR_PASSWORD; ?>"
                                       placeholder="try 'password'">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-6">
                                <button type="submit" class="btn btn-default">Login</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
<?php

if (isset($errors) && is_array($errors)): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="errors alert alert-danger">
                <ul>
                    <?php foreach ($errors as $errMsg): ?>
                        <li><?php echo(Util::escape($errMsg)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>