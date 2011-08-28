<?php
require_once "Template-PHP/Mustache.php";
$template=file_get_contents('nightlies.mustache');

class User extends Mustache {
    public $display_name;
    public $uid;
    public $files;
}
class Nightlies extends Mustache {
    public $users=array();
    public $contentPath;
}

// Main content container:
// "users" or "nightlies"
$users=new Nightlies();
$users->contentPath="Public/android-x86";

// Single user public data
$u=new User();
$u->uid=1000;
$u->display_name="Zaphod Beeblebrox";

// Each file user exposed
$u->files=array();
array_push($u->files, Array('content' => "file"));
array_push($u->files, Array('content' => "file"));

// Put user into envelope
array_push($users->users, $u);

// Final step, apply caching here.
echo $users->render($template);

?>
