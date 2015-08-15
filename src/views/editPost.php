<?php

$postId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$post = DataManager::getPostById($postId);
$user = AuthenticationManager::getAuthenticatedUser();

if (!DataManager::isPostLastInChannel($post) || $user == null || $user->getId() != $post->getUserId())
    $error = "You can only edit or delete a post if it has been created by you and is the last one in it's channel.";

$channel = DataManager::getChannelById($post->getChannelId());
if ($post == null || $channel == null)
    Util::redirect('/');

?>

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Edit post
            <small>in channel <i><?php echo $channel->getName() ?></i></small>
        </h1>
    </div>
</div>
<!-- /.row -->

<?php if (isset($error)): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="alert alert-danger">
                <?php echo(Util::escape($error)); ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div id="messages" class="row">
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="post" id="addNewPost"
                  action="<?php echo Util::action(Controller::ACTION_EDITPOST, array('view' => 'channel', 'id' => $channel->getId())); ?>">
                <div>
                    <input type="text" class="form-control" name="title" placeholder="Title" id="postTitle"
                           value="<?php echo $post->getTitle() ?>" required>
                    <textarea class="form-control" rows="3" placeholder="Text" name="text" id="postText"
                              required><?php echo $post->getText() ?></textarea>
                    <input type="hidden" name="postId" value="<?php echo $postId ?>">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </form>
        </div>
    </div>


<?php endif; ?>
<!-- /.row -->