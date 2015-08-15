<?php

if (!AuthenticationManager::isAuthenticated())
    Util::redirect('/');

$channels = DataManager::getChannelsForUser(AuthenticationManager::getAuthenticatedUser()->getId());

$favoritesAdded = false;
$unreadAdded = false;

?>

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Overview
        </h1>
    </div>
</div>
<!-- /.row -->


<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Favorites by channel</h3>
        <?php foreach ($channels as $channel) {
            $posts = DataManager::getFavoritesByChannel($channel->getId()); ?>

            <?php if (count($posts) > 0): ?>
                <?php $favoritesAdded = true; ?>
                <div class="row">
                    <h4 class="page-header" style="margin: 10px 20px; padding-bottom: 0;">
                        <a href="?view=channel&id=<?php echo $channel->getId(); ?>">
                            <?php echo $channel->getName(); ?>
                        </a>
                    </h4>

                    <?php foreach ($posts as $post) {
                        $user = DataManager::getUserById($post->getUserId()); ?>
                        <div class='col-lg-8'>
                            <div class='panel panel-primary'>
                                <div class='panel-heading'>
                                    <h3 class='panel-title'>
                                        <?php echo $post->getTitle(); ?>
                                        <span class='align-right'>
                                        <?php echo $user->getFirstName() . " " . $user->getLastName() . ", " . $post->getDatetime(); ?>
                                    </span>
                                    </h3>
                                </div>
                                <div class='panel-body'><?php echo $post->getText(); ?></div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            <?php endif; ?>
        <?php } ?>
        <?php if(!$favoritesAdded) echo "<i>No favorites to display.</i>" ?>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Unread posts by channel</h3>
        <?php foreach ($channels as $channel) {
            $posts = DataManager::getUnreadPostsBy($channel->getId()); ?>

            <?php if (count($posts) > 0): ?>
                <?php $unreadAdded = true; ?>
                <div class="row">
                    <h4 class="page-header" style="margin: 10px 20px; padding-bottom: 0;">
                        <a href="?view=channel&id=<?php echo $channel->getId(); ?>">
                            <?php echo $channel->getName(); ?>
                        </a>
                    </h4>

                    <?php foreach ($posts as $post) {
                        $user = DataManager::getUserById($post->getUserId()); ?>
                        <div class='col-lg-8'>
                            <div class='panel panel-primary'>
                                <div class='panel-heading'>
                                    <h3 class='panel-title'>
                                        <?php echo $post->getTitle(); ?>
                                        <span class='align-right'>
                                        <?php echo $user->getFirstName() . " " . $user->getLastName() . ", " . $post->getDatetime(); ?>
                                    </span>
                                    </h3>
                                </div>
                                <div class='panel-body'><?php echo $post->getText(); ?></div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            <?php endif; ?>
        <?php } ?>
        <?php if(!$unreadAdded) echo "<i>No unread posts to display.</i>" ?>
    </div>
</div>
<!-- /.row -->