<?php

if (AuthenticationManager::isAuthenticated())
    Util::redirect('/');

$userName = isset($_REQUEST['userName']) ? $_REQUEST['userName'] : null;
$firstName = isset($_REQUEST['firstName']) ? $_REQUEST['firstName'] : null;
$lastName = isset($_REQUEST['lastName']) ? $_REQUEST['lastName'] : null;
$mail = isset($_REQUEST['mail']) ? $_REQUEST['mail'] : null;

?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Register
        </h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                Please fill out the form below to register:
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" id="registerForm"
                      action="<?php echo Util::action(Controller::ACTION_REGISTER, array('view' => $view)); ?>">
                    <div class="form-group">
                        <div class="row">
                            <label for="<?php echo Controller::USR_NAME; ?>"
                                   class="col-sm-3 control-label">Username:</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control"
                                       name="<?php echo Controller::USR_NAME; ?>"
                                       value="<?php echo htmlentities($userName); ?>"
                                       pattern=".{4,}" required title="4 characters minimum"
                                       style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                            </div>
                        </div>
                        <div class="row">
                            <label for="<?php echo Controller::USR_FIRSTNAME; ?>" class="col-sm-3 control-label">First
                                name:</label>

                            <div class="col-sm-8">
                                <input type="text" required class="form-control"
                                       name="<?php echo Controller::USR_FIRSTNAME; ?>"
                                       value="<?php echo htmlentities($firstName); ?>"
                                       style="border-radius: 0; margin-top: -1px;">
                            </div>
                        </div>
                        <div class="row">
                            <label for="<?php echo Controller::USR_LASTNAME; ?>" class="col-sm-3 control-label">Last
                                name:</label>

                            <div class="col-sm-8">
                                <input type="text" required class="form-control"
                                       name="<?php echo Controller::USR_LASTNAME; ?>"
                                       value="<?php echo htmlentities($lastName); ?>"
                                       style="border-top-left-radius: 0; border-top-right-radius: 0; margin-top: -1px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="<?php echo Controller::USR_MAIL; ?>" class="col-sm-3 control-label">Mail
                                address:</label>

                            <div class="col-sm-8">
                                <input type="email" required class="form-control"
                                       name="<?php echo Controller::USR_MAIL; ?>"
                                       value="<?php echo htmlentities($mail); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="<?php echo Controller::USR_PASSWORD; ?>" class="col-sm-3 control-label">Password:</label>

                            <div class="col-sm-8">
                                <input type="password" required class="form-control"
                                       name="<?php echo Controller::USR_PASSWORD; ?>"
                                       style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                            </div>
                        </div>
                        <div class="row">
                            <label for="<?php echo Controller::USR_PASSWORD2; ?>" class="col-lg-3 control-label">Retype
                                Password:</label>

                            <div class="col-lg-8">
                                <input type="password" required class="form-control"
                                       name="<?php echo Controller::USR_PASSWORD2; ?>"
                                       style="border-top-left-radius: 0; border-top-right-radius: 0; margin-top: -1px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="<?php echo Controller::USR_CHANNELS; ?>" class="col-lg-3 control-label">Channels:</label>

                            <div class="col-lg-8">
                                <select required class="form-control" id="example-getting-started" multiple="multiple"
                                        name="<?php echo Controller::USR_CHANNELS; ?>[]">
                                    <?php foreach (DataManager::getChannels() as $channel) { ?>
                                        <option value="<?php echo $channel->getId(); ?>">
                                            <?php echo $channel->getName(); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-9 col-lg-2" style="padding-left: 0; padding-right: 5px">
                            <button type="submit" class="btn btn-default" style="width: 100%;">Register</button>
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

<script type="text/javascript">
    $(document).ready(function () {
        $('#example-getting-started').multiselect();
    });
</script>
