<?php

require_once('../inc/bootstrap.php');

if (!AuthenticationManager::isAuthenticated()) {
    header("HTTP/1.1 403 Forbidden");
    echo "Access not allowed for anonymous users.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Controller::getInstance()->invokePostAction();
} else {
    $channelId = isset($_GET['channelId']) ? Util::escape($_GET['channelId']) : null;
    $lastSeenPost = isset($_GET['lastSeenPost']) ? Util::escape($_GET['lastSeenPost']) : null;
    if ($channelId == null)
        exit();

    $data = DataManager::getPostsByChannel($channelId, $lastSeenPost);

    if ($data != null) {
        header('Content-Type: application/json', true, 200);
        echo json_encode($data);
    } else {
        header("HTTP/1.1 204 No Content");
    }
}

?>