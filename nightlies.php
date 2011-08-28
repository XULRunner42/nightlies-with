<?php
require_once("Nightlies.php");
require_once("user.php");
require_once("memcache.php");

$users=User::fetchUsers();

if(isset($_SESSION['acct']) && $_SESSION['acct']['uid']==23042584) {
    echo "<pre>";
    print_r( $users );
    echo "</pre>";
}

lockNightlies_R();
$nightlies=$memcache->get("nightlies");
$nightTimer=$memcache->get("nightlies.timer");
unlockNightlies_R();

if($nightlies) {
    echo $nightlies;
}

lockNightlies_R();
if(tryLockNightlies_W()) {
    
    if(!$nightTimer) {
	$cmd="php /home/yebyen/public_html/android-x86/nightlies_regen.php";
	$cmd.=' >/dev/null 2>/dev/null & echo $! & disown';
	exec($cmd);
    }
    unlockNightlies_W();
}
unlockNightlies_R();

?>
