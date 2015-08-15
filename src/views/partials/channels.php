<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        <?php if (AuthenticationManager::isAuthenticated()): ?>
            <li><h4>Channels</h4></li>
            <?php foreach (DataManager::getChannelsForUser(AuthenticationManager::getAuthenticatedUser()->getId()) as $channel) { ?>
                <li>
                    <a href="<?php echo "?view=channel&id=" . $channel->getId(); ?>">
                        <i class="fa fa-fw fa-bookmark-o"></i><?php echo $channel->getName(); ?>
                    </a>
                </li>
            <?php } ?>
        <?php else: ?>
            <li><h5>Login to view channels</h5></li>
        <?php endif; ?>
    </ul>
</div>
