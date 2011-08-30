<?php

class User {
    public $token, $token_secret;
    public $referral_link;
    public $display_name;
    public $uid, $email;
    private static $_db;
    public $files=Array();

    public static function pdo() {
        if (is_null(self::$_db)) {
            self::$_db=new PDO("sqlite:/var/www/nightlies-with/db/users.sq3");
            self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$_db->exec("CREATE TABLE IF NOT EXISTS Users (Id INTEGER
                PRIMARY KEY, token TEXT, token_secret TEXT, referral_link TEXT,
                display_name TEXT, uid INTEGER, email TEXT)");
        }

        return self::$_db;
    }

    public function __construct($token, $token_secret, $referral_link,
        $display_name, $uid, $email) {

            $this->token=$token;
            $this->token_secret=$token_secret;
            $this->referral_link=$referral_link;
            $this->display_name=$display_name;
            $this->uid=$uid;
            $this->email=$email;

            return $this;
        }

    public function display() {
        echo "<p><pre>\ttoken: $this->token, token_secret: $this->token_secret,
            referral_link: $this->referral_link, display_name: 
            $this->display_name, uid: $this->uid, email: 
            $this->email</pre></p>";
    }

    public function save() {
        $db=self::pdo();

        $qry="UPDATE Users SET token=:token, token_secret=:token_secret,
            referral_link=:referral_link, display_name=:display_name,
            email=:email WHERE uid LIKE :uid";
        $user=self::fetchByUid($this->uid);
        if($user===NULL) {
            $qry="INSERT INTO Users (token, token_secret, referral_link, 
                display_name, uid, email) VALUES (:token, :token_secret, 
                :referral_link, :display_name, :uid, :email)";
        }

        $stmt=$db->prepare($qry);
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":token_secret", $this->token_secret);
        $stmt->bindParam(":referral_link", $this->referral_link);
        $stmt->bindParam(":display_name", $this->display_name);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":email", $this->email);

        $stmt->execute();
    }

    public static function fetchByUid($uid) {
        $db=self::pdo();

        // Should only get one user, 
        try {
            $qry="SELECT token, token_secret, referral_link, display_name, uid, 
                email FROM Users where uid LIKE :uid";
            $stmt=$db->prepare($qry);
            $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User',
                array('token', 'token_secret', 'referral_link', 'display_name', 
                    'uid', 'email'));

            $stmt->bindParam(":uid", $uid);

            $user=NULL;
            if($stmt->execute()) {
                if(count($result=$stmt->fetchAll()))  {
                    foreach($result as $oneUser) {
                        $user=$oneUser;
                    }
                    // return the last matching user
                    return $user;
                }
                else {
                    return NULL;
                }
            }
        }
        catch (PDOException $e) {
            echo 'Error: ', $e->__toString();
        }
    }

    public static function fetchUsers() {
        $db=self::pdo();

        try {
            $qry="SELECT token, token_secret, referral_link, display_name, uid, 
                email FROM Users";
            $stmt=$db->prepare($qry);
            $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User', 
                array('token', 'token_secret', 'referral_link', 'display_name', 
                    'uid', 'email'));

            $users=NULL;

            if($stmt->execute()) {
                $users=$stmt->fetchAll();
                return $users;
            }
            else {
                return NULL;
            }
        }
        catch (PDOException $e) {
            echo 'Error: ', $e->__toString();
        }
    }
}

?>
