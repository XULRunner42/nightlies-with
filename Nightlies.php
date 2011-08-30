<?php

$APPROOT="http://localhost/nightlies-with/";

$consumerKey = '###############';
$consumerSecret = '###############';

require_once 'Dropbox/autoload.php';
require_once 'Template-PHP/Mustache.php';

class Nightlies extends Mustache {
    public $users=array();
    public $contentPath="android-x86";
    function timestamp() {
	return date(DATE_COOKIE);
    }
    public $errorcode="";
    function user_count() {
	return count($this->users);
    }
    public $total_users;
    public $hash;
}

session_start();

$oauth = new Dropbox_OAuth_PHP($consumerKey, $consumerSecret);
unset($consumerKey);
unset($consumerSecret);

?>
