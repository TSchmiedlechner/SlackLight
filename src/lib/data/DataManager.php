<?php

class DataManager {

    const MYSQL_SERVER = '127.0.0.1';
    const MYSQL_DB = 'fh_2015_scm4_s1310307103';
    const MYSQL_USER = 'fh_2015_scm4';
    const MYSQL_PW = 'fh_2015_scm4';

    /**
     * @return mysqli
     */
    private static function getConnection() {
        $con = new mysqli(DataManager::MYSQL_SERVER, DataManager::MYSQL_USER, DataManager::MYSQL_PW, DataManager::MYSQL_DB);
        if (mysqli_connect_errno()) {
            die('Unable to connect to database: ' . mysqli_connect_error());
        }
        return $con;
    }

    /**
     * @param $connection mysqli
     * @param $query string
     * @param mixed
     * @return mysqli_result|boolean
     */
    private static function query($connection, $query, $dieOnError = true) {
        $res = $connection->query($query);
        if (!$res && $dieOnError) {
            die("Error in query \"" . $query . "\": " . $connection->error);
        }
        return $res;
    }

    /**
     * @param $cursor mysqli_result
     * @return mixed
     */
    private static function fetchObject($cursor) {
        return $cursor->fetch_object();
    }

    /**
     * @param $cursor mysqli_result
     * @return mixed
     */
    private static function close($cursor) {
        $cursor->close();
    }

    /**
     * @param $connection mysqli
     */
    private static function closeConnection($connection) {
        $connection->close();
    }

    /**
     * @param $connection mysqli
     * @return int
     */
    private static function lastInsertId($connection) {
        return mysqli_insert_id($connection);
    }

    /**
     * @return Channel[]
     */
    public static function getChannels() {
        $channels = array();
        $con = self::getConnection();

        $res = self::query($con, "
            SELECT id, name
            FROM channels");

        while ($c = self::fetchObject($res)) {
            $channels[] = (new Channel($c->id, $c->name));
        }

        self::close($res);
        self::closeConnection($con);

        return $channels;
    }

    /**
     * @param $userId integer
     * @return Channel[]
     */
    public static function getChannelsForUser($userId) {
        $channels = array();
        $con = self::getConnection();

        $userId = intval($userId);

        $res = self::query($con, "
            SELECT c.id, c.name
            FROM channels c, channels_for_users cfu WHERE cfu.channel_id = c.id AND cfu.user_id = " . $userId . ";");

        while ($c = self::fetchObject($res)) {
            $channels[] = (new Channel($c->id, $c->name));
        }

        self::close($res);
        self::closeConnection($con);

        return $channels;
    }

    /**
     * @param integer
     * @return Channel
     */
    public static function getChannelById($channelId) {
        $channel = null;
        $con = self::getConnection();
        $channelId = intval($channelId);

        $res = self::query($con, "SELECT id, name
            FROM channels WHERE id = " . $channelId . ";");

        if ($c = self::fetchObject($res)) {
            $channel = new Channel($c->id, $c->name);
        }

        self::close($res);
        self::closeConnection($con);

        return $channel;
    }


    /**
     * @param $channellId integer
     * @return Post[]
     */
    public static function getFavoritesByChannel($channellId) {
        $posts = array();
        $con = self::getConnection();
        $channellId = intval($channellId);
        $userId = AuthenticationManager::getAuthenticatedUser()->getId();

        $res = self::query($con, "SELECT p.id, p.text, p.user_id, p.channel_id, p.datetime, p.title
            FROM posts p, favorites f
            WHERE p.active = 1 AND f.post_id = p.id AND f.user_id = " . $userId . " AND p.channel_id = " . $channellId . ";");

        while ($p = self::fetchObject($res)) {
            $posts[] = (new Post($p->id, $p->title, $p->text, $p->user_id, $p->channel_id, $p->datetime));
        }

        self::close($res);
        self::closeConnection($con);

        return $posts;
    }

    /**
     * @param $channellId integer
     * @return Post[]
     */
    public static function getUnreadPostsBy($channellId) {
        $posts = array();
        $con = self::getConnection();
        $channellId = intval($channellId);
        $userId = AuthenticationManager::getAuthenticatedUser()->getId();

        $res = self::query($con, "SELECT p.id, p.text, p.user_id, p.channel_id, p.datetime, p.title
            FROM posts p
            WHERE p.active = 1 AND  p.channel_id = " . $channellId . "
            AND NOT EXISTS (SELECT  NULL
                FROM read_posts rp
                WHERE rp.post_id = p.id AND rp.user_id = " . $userId . ")");

        while ($p = self::fetchObject($res)) {
            $posts[] = (new Post($p->id, $p->title, $p->text, $p->user_id, $p->channel_id, $p->datetime));
        }

        self::close($res);
        self::closeConnection($con);

        return $posts;
    }

    /**
     * @param $channellId integer
     * @param $fromPostId integer
     * @return Post[]
     */
    public static function getPostsByChannel($channellId, $fromPostId) {
        $posts = array();
        $con = self::getConnection();
        $channellId = intval($channellId);
        $fromPostId = intval($fromPostId);
        $userId = AuthenticationManager::getAuthenticatedUser()->getId();

        $res = self::query($con, "SELECT id, title, text, user_id, channel_id, datetime,
            IFNULL((SELECT TRUE FROM favorites WHERE favorites.user_id = " . $userId . " AND favorites.post_id = posts.id), FALSE) AS is_favorite,
            IFNULL((SELECT FALSE FROM read_posts WHERE read_posts.user_id = " . $userId . " AND read_posts.post_id = posts.id), TRUE) AS is_unread
            FROM posts WHERE active = 1 AND  channel_id = " . $channellId . " AND id > " . $fromPostId . "
            ORDER BY datetime ASC;");

        while ($p = self::fetchObject($res)) {
            $posts[] = (new Post($p->id, $p->title, $p->text, $p->user_id, $p->channel_id, $p->datetime, $p->is_favorite, $p->is_unread));
        }


        self::updateReadPosts($userId, $posts);

        self::close($res);
        self::closeConnection($con);

        return count($posts) > 0 ? $posts : null;
    }

    /**
     * @param $userId integer
     * @param $posts Post[]
     */
    public static function updateReadPosts($userId, $posts) {
        $con = self::getConnection();
        self::query($con, 'BEGIN');

        $userId = intval($userId);

        foreach ($posts as $post) {
            self::updateReadPost($userId, $post->getId());
        }

        self::query($con, 'COMMIT');
        self::closeConnection($con);
    }

    /**
     * @param $userId integer
     * @param $postId integer
     */
    public static function updateReadPost($userId, $postId) {
        $con = self::getConnection();

        $userId = intval($userId);
        $postId = intval($postId);

        self::query($con, "
                INSERT INTO read_posts (
                  post_id, user_id
                ) VALUES (
                  " . $postId . ",
                  " . $userId . ");", false);

        self::closeConnection($con);
    }


    /**
     * @param $postId integer
     * @return Post
     */
    public static function getPostById($postId) {
        $post = null;
        $con = self::getConnection();
        $postId = intval($postId);
        $userId = AuthenticationManager::getAuthenticatedUser()->getId();

        $res = self::query($con, "SELECT id, title, text, user_id, channel_id, datetime,
            IFNULL((SELECT TRUE FROM favorites WHERE favorites.user_id = " . $userId . " AND favorites.post_id = posts.id), FALSE) AS is_favorite,
            IFNULL((SELECT FALSE FROM read_posts WHERE read_posts.user_id = " . $userId . " AND read_posts.post_id = posts.id), TRUE) AS is_unread
            FROM posts WHERE id = " . $postId . ";");

        if ($p = self::fetchObject($res)) {
            $post = (new Post($p->id, $p->title, $p->text, $p->user_id, $p->channel_id, $p->datetime, $p->is_favorite, $p->is_unread));
        }

        self::close($res);
        self::closeConnection($con);

        return $post;
    }


    /**
     * @param $post Post
     * @return bool
     */
    public static function isPostLastInChannel($post) {
        $con = self::getConnection();

        $res = self::query($con, "SELECT id, title, text, user_id, channel_id, datetime
            FROM posts WHERE active = 1 AND channel_id = " . $post->getChannelId() . "
            ORDER BY datetime DESC
            LIMIT 1;");

        $id = -1;
        if ($p = self::fetchObject($res)) {
            $id = $p->id;
        }

        self::close($res);
        self::closeConnection($con);

        return $id === $post->getId();
    }

    /**
     * @param $title string
     * @param $text string
     * @param $userId integer
     * @param $channelId integer
     */
    public static function createPost($title, $text, $userId, $channelId) {
        $con = self::getConnection();
        self::query($con, 'BEGIN');

        $userId = intval($userId);
        $channelId = intval($channelId);
        $title = $con->real_escape_string($title);
        $text = $con->real_escape_string($text);

        self::query($con, "
            INSERT INTO posts (
              title, text, user_id, channel_id, datetime, active
            ) VALUES (
              '" . $title . "',
              '" . $text . "',
              " . $userId . ",
              " . $channelId . ",
              NOW(), 1);");

        self::query($con, "
                INSERT INTO read_posts (
                  post_id, user_id
                ) VALUES (
                  " . self::lastInsertId($con) . ",
                  " . $userId . ");", false);

        self::query($con, 'COMMIT');
        $insertId = self::lastInsertId($con);
        self::closeConnection($con);

        self::logAction('Created post with id=' . $insertId);
    }

    /**
     * @param integer
     * @return User | null
     */
    public static function getUserById($userId) {
        $user = null;
        $con = self::getConnection();
        $userId = intval($userId);

        $res = self::query($con, "SELECT id, first_name, last_name, mail, username, password
            FROM users WHERE id = " . $userId . ";");

        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->username, $u->first_name, $u->last_name, $u->mail, $u->password);
        }

        self::close($res);
        self::closeConnection($con);

        return $user;
    }

    /**
     * @param string
     * @return User | null
     */
    public static function getUserByUserName($userName) {
        $user = null;
        $con = self::getConnection();
        $userName = $con->real_escape_string($userName);

        $res = self::query($con, "SELECT id, first_name, last_name, mail, username, password
            FROM users WHERE userName = '" . $userName . "';");

        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->username, $u->first_name, $u->last_name, $u->mail, $u->password);
        }

        self::close($res);
        self::closeConnection($con);

        return $user;
    }

    /**
     * @param $username string
     * @param $firstName string
     * @param $lastName string
     * @param $mail string
     * @param $password string
     * @param $channelIds integer[]
     */
    public static function createUser($username, $firstName, $lastName, $mail, $password, $channelIds) {
        $con = self::getConnection();

        $username = $con->real_escape_string($username);
        $firstName = $con->real_escape_string($firstName);
        $lastName = $con->real_escape_string($lastName);
        $mail = $con->real_escape_string($mail);
        $password = $con->real_escape_string($password);

        // salting & hash
        $password = hash('sha1', "$username|$password");

        self::query($con, "
            INSERT INTO users (first_name, last_name, mail, password, username) VALUES (
              '" . $firstName . "',
              '" . $lastName . "',
              '" . $mail . "',
              '" . $password . "',
              '" . $username . "');");

        $userId = self::getUserByUserName($username)->getId();
        $insertId = self::lastInsertId($con);

        self::query($con, 'BEGIN');
        foreach ($channelIds as $channelId) {
            $channelId = intval($channelId);

            self::query($con, "
                INSERT INTO channels_for_users (
                  channel_id, user_id
                ) VALUES (
                  " . $channelId . ",
                  " . $userId . ");");
        }
        self::query($con, 'COMMIT');
        self::closeConnection($con);

        self::logAction('Created user with id=' . $insertId);
    }

    /**
     * @param $postId integer
     * @param $title string
     * @param $text string
     * @throws Exception
     */
    public static function updatePost($postId, $title, $text) {
        $postId = intval($postId);
        $post = self::getPostById($postId);

        if (!self::isPostLastInChannel($post))
            throw new Exception("Post is not the last one in it's channel and therefore can't be updated.");
        if ($post->getUserId() != AuthenticationManager::getAuthenticatedUser()->getId())
            throw new Exception("Post has not been created by the current user and therefore can't be updated.");

        $con = self::getConnection();

        $title = $con->real_escape_string($title);
        $text = $con->real_escape_string($text);

        self::query($con,
            "UPDATE posts SET text = '" . $text . "', title = '" . $title . "'
            WHERE id = " . $postId . ";");

        self::closeConnection($con);

        self::logAction('Updated post with id=' . $postId . 'by username' . AuthenticationManager::getAuthenticatedUser()->getUserName());
    }

    /**
     * @param $postId integer
     * @throws Exception
     */
    public static function deletePost($postId) {
        $postId = intval($postId);
        $post = self::getPostById($postId);
        $userId = AuthenticationManager::getAuthenticatedUser()->getId();

        if (!self::isPostLastInChannel($post))
            throw new Exception("Post is not the last one in it's channel and therefore can't be deleted.");
        if ($post->getUserId() != $userId)
            throw new Exception("Post has not been created by the current user and therefore can't be deleted.");

        $con = self::getConnection();

        self::query($con,
            "UPDATE posts SET active = 0 WHERE id = " . $postId . ";");

        self::closeConnection($con);

        self::logAction('Deleted post with id=' . $postId . ' by user with id=' . $userId);
    }

    /**
     * @param $postId integer
     * @param $isFavorite integer
     * @param $userId integer
     */
    public static function setFavoriteState($postId, $isFavorite, $userId) {
        $con = self::getConnection();

        $postId = intval($postId);
        $isFavorite = intval($isFavorite);
        $userId = intval($userId);

        $query = null;
        if ($isFavorite == 0) {
            $query = "DELETE FROM favorites WHERE user_id = " . $userId . " AND post_id = " . $postId . ";";
        } else {
            $query = "INSERT INTO favorites (user_id, post_id) VALUES (" . $userId . ", " . $postId . ");";
        }

        self::query($con, $query);

        self::logAction('Set favorite state of post with id=' . $postId . ' to ' . intval($isFavorite) . ' by user with id=' . $userId);
    }

    /**
     * @param $username string
     * @return bool
     */
    public static function userNameExists($username) {
        $con = self::getConnection();
        $username = $con->real_escape_string($username);

        $res = self::query($con, "SELECT EXISTS(SELECT 1 FROM users WHERE username = '" . $username . "') AS ex;");

        $exists = false;
        if ($r = self::fetchObject($res)) {
            $exists = $r->ex;
        }

        self::close($res);
        self::closeConnection($con);

        return $exists;
    }

    /**
     * @param $mail string
     * @return bool
     */
    public static function mailAddressExists($mail) {
        $con = self::getConnection();
        $mail = $con->real_escape_string($mail);

        $res = self::query($con, "SELECT EXISTS(SELECT 1 FROM users WHERE mail = '" . $mail . "') AS ex;");

        $exists = false;
        if ($r = self::fetchObject($res)) {
            $exists = $r->ex;
        }

        self::close($res);
        self::closeConnection($con);

        return $exists;
    }

    /**
     * @param $message string
     */
    public static function logAction($message) {
        $con = self::getConnection();

        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $requestUri = $_SERVER['REQUEST_URI'];

        self::query($con, "
                INSERT INTO action_log (
                  ip_address, user_agent, request_uri, message, datetime
                ) VALUES (
                  '" . $ipaddress . "',
                  '" . $userAgent . "',
                  '" . $requestUri . "',
                  '" . $message . "',
                  NOW());"
        );

        self::closeConnection($con);
    }
}
