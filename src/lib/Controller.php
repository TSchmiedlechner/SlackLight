<?php

class Controller extends BaseObject {

    const ACTION = 'action';
    const METHOD_POST = 'POST';
    const PAGE = 'page';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_REGISTER = 'register';
    const ACTION_NEWPOST = 'newPost';
    const ACTION_EDITPOST = 'editPost';
    const ACTION_DELETEPOST = 'deletePost';
    const ACTION_SETFAVORITE = 'setFavorite';

    const USR_NAME = 'userName';
    const USR_PASSWORD = 'password';
    const USR_PASSWORD2 = 'password2';
    const USR_FIRSTNAME = 'firstName';
    const USR_LASTNAME = 'lastName';
    const USR_MAIL = 'mail';
    const USR_CHANNELS = 'channels';


    private static $instance = false;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    private function __construct() {
    }

    public function invokePostAction() {

        if ($_SERVER['REQUEST_METHOD'] != self::METHOD_POST) {
            throw new Exception('Controller can only handle POST requests.');
        } elseif (!isset($_REQUEST[self::ACTION])) {
            throw new Exception('Action not specified.');
        }

        $action = $_REQUEST[self::ACTION];

        switch ($action) {
            case self::ACTION_LOGIN:
                if (!AuthenticationManager::authenticate($_REQUEST[self::USR_NAME], $_REQUEST[self::USR_PASSWORD])) {
                    $this->forwardRequest(array('Invalid username or password.'), '?view=login', array(self::USR_NAME => $_REQUEST[self::USR_NAME]));
                }
                break;

            case self::ACTION_LOGOUT:
                AuthenticationManager::signOut();
                Util::redirect();
                break;

            case self::ACTION_REGISTER:
                if (!AuthenticationManager::isAuthenticated()) {
                    self::handleRegister();
                }
                break;

            case self::ACTION_NEWPOST:
                if (AuthenticationManager::isAuthenticated()) {
                    self::handleNewPost();
                }
                break;

            case self::ACTION_EDITPOST:
                if (AuthenticationManager::isAuthenticated()) {
                    self::handleEditPost();
                }
                break;

            case self::ACTION_DELETEPOST:
                if (AuthenticationManager::isAuthenticated()) {
                    self::handleDeletePost();
                }
                break;

            case self::ACTION_SETFAVORITE:
                if (AuthenticationManager::isAuthenticated()) {
                    self::handleSetFavorite();
                }
                break;

            default:
                throw new Exception('Unknown controller action ' . $action);
        }
    }

    private function handleNewPost() {
        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
        $text = isset($_REQUEST['text']) ? $_REQUEST['text'] : null;
        $channelId = isset($_REQUEST['channelId']) ? $_REQUEST['channelId'] : null;

        if ($title == null || $text == null || $channelId == null)
            throw new Exception('Invalid data for new post.');

        DataManager::createPost($title, $text, AuthenticationManager::getAuthenticatedUser()->getId(), $channelId);
    }

    private function handleRegister() {
        $errors = array();

        $username = isset($_REQUEST[self::USR_NAME]) ? Util::escape($_REQUEST[self::USR_NAME]) : null;
        $firstName = isset($_REQUEST[self::USR_FIRSTNAME]) ? Util::escape($_REQUEST[self::USR_FIRSTNAME]) : null;
        $lastName = isset($_REQUEST[self::USR_LASTNAME]) ? Util::escape($_REQUEST[self::USR_LASTNAME]) : null;
        $mail = isset($_REQUEST[self::USR_MAIL]) ? Util::escape($_REQUEST[self::USR_MAIL]) : null;
        $password = isset($_REQUEST[self::USR_PASSWORD]) ? Util::escape($_REQUEST[self::USR_PASSWORD]) : null;
        $password2 = isset($_REQUEST[self::USR_PASSWORD2]) ? Util::escape($_REQUEST[self::USR_PASSWORD2]) : null;
        $channels = isset($_REQUEST[self::USR_CHANNELS]) ? $_REQUEST[self::USR_CHANNELS] : null;

        if ($username == null || $firstName == null || $lastName == null || $mail == null || $password == null || $password2 == null || $channels == null)
            $errors[] = "Please fill in all fields.";
        if (DataManager::userNameExists($username))
            $errors[] = "Username already exists.";
        if (DataManager::mailAddressExists($mail))
            $errors[] = "Mail address already in use.";
        if ($password !== $password2)
            $errors[] = "Entered passwords have to be equal.";
        if (count($channels) == 0)
            $errors[] = "Please select one or more channels.";

        if (count($errors) > 0) {
            echo $_REQUEST[self::USR_MAIL];
            $this->forwardRequest($errors, '?view=register',
                array(
                    self::USR_NAME => $_REQUEST[self::USR_NAME],
                    self::USR_FIRSTNAME => $_REQUEST[self::USR_FIRSTNAME],
                    self::USR_LASTNAME => $_REQUEST[self::USR_LASTNAME],
                    self::USR_MAIL => $_REQUEST[self::USR_MAIL]
                )
            );
        } else {
            DataManager::createUser($username, $firstName, $lastName, $mail, $password, $channels);
            AuthenticationManager::authenticate($username, $password);
        }
    }

    private function handleEditPost() {
        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
        $text = isset($_REQUEST['text']) ? $_REQUEST['text'] : null;
        $postId = isset($_REQUEST['postId']) ? $_REQUEST['postId'] : null;
        $channelId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

        if ($title == null || $text == null || $postId == null || $channelId == null)
            throw new Exception('Invalid data for editing post.');

        DataManager::updatePost($postId, $title, $text);

        Util::redirect("?view=channel&id=" . $channelId);
    }

    private function handleDeletePost() {
        $postId = isset($_REQUEST['postId']) ? $_REQUEST['postId'] : null;
        $channelId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

        if ($channelId == null || $postId == null)
            throw new Exception('Invalid data for deleting post.');

        DataManager::deletePost($postId);

        Util::redirect("?view=channel&id=" . $channelId);
    }

    private function handleSetFavorite() {
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $value = isset($_REQUEST['value']) ? intval($_REQUEST['value']) : -1;

        if ($id == 0 || $value == -1)
            throw new Exception('Invalid data in set favorite.');

        DataManager::setFavoriteState($id, $value, AuthenticationManager::getAuthenticatedUser()->getId());
    }

    /**
     * @param array $errors : optional assign it to
     * @param string $target : url for redirect of the request
     * @throws Exception
     */
    protected function forwardRequest(array $errors = null, $target = null, $parameters = null) {
        //check for given target and try to fall back to previous page if needed
        if ($target == null) {
            if (!isset($_REQUEST[self::PAGE])) {
                throw new Exception('Missing target for forward.');
            }
            $target = $_REQUEST[self::PAGE];
        }
        //forward request to target
        // optional - add errors to redirect and process them in view
        if (count($errors) > 0)
            $target .= '&errors=' . urlencode(serialize($errors));
        foreach ($parameters as $key => $val)
            $target .= '&' . $key . '=' . urlencode($val);
        header('location: ' . $target);
        exit();
    }
}

?>
