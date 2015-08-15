<?php
SessionContext::create();

class AuthenticationManager extends BaseObject {

    public static function getAuthenticatedUser() {
        return self::isAuthenticated() ? DataManager::getUserById($_SESSION['user']) : null;
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public static function authenticate($userName, $password) {
        $user = DataManager::getUserByUserName($userName);

        if($user != null && $user->getPasswordHash() == hash('sha1', "$userName|$password")) {
            $_SESSION['user'] = $user->getId();
            DataManager::logAction("Login succeeded by user with username=" . $userName);
            return true;
        }

        DataManager::logAction("Login failed by user with username=" . $userName);
        self::signOut();
        sleep(2); // make bruteforcing unfunny
        return false;
    }

    public static function signOut() {
        DataManager::logAction("Logout by user with username=" . $_SESSION['user']);
        unset($_SESSION['user']);
    }
}

?>