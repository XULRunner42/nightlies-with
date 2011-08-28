<?php
require_once("Nightlies.php");
require_once("user.php");
require_once("memcache.php");

lockNightlies_R();
$nightlies=$memcache->get("nightlies");
$nightTimer=$memcache->get("nightlies.timer");

//echo "acquiring read lock\n";
if($nightlies && tryLockNightlies_W()) {
    //echo "checking for expired cache\n";
    if(!$nightTimer) {
	echo "expired cache detected\n";
	unlockNightlies_R();
	echo "regenerating... ";
	$output=renderNightlies($oauth);
	lockNightlies_R();

	$memcache->delete("nightlies", 0);
	$memcache->add("nightlies", $output);

	setNightlies_Timer();
    }
    unlockNightlies_W();
    unlockNightlies_R();
    //echo "complete, locks cleared.\n";
} elseif(!$nightlies){
    echo "trying to generate first cache\n";
    if(tryLockNightlies_W()) {
	echo "acquired write lock on nightlies cache... ";
	unlockNightlies_R();
	echo "gave up read lock\n";
	$output=renderNightlies($oauth);

	lockNightlies_R();
	echo "writing cache... ";
	$memcache->add("nightlies", $output);

	setNightlies_Timer();
	unlockNightlies_W();
	unlockNightlies_R();
	echo "done, locks cleared\n";
    }
}
unlockNightlies_R();
//echo "gave up read lock\n";

// heavy lifting
function renderNightlies($oauth) {
    $errorcode="";
    $nightlies=new Nightlies();
    $template=file_get_contents('/home/yebyen/public_html/android-x86/nightlies.mustache');

    $users=User::fetchUsers();
    $nightlies->total_users=count($users);

    foreach($users as $user) {
	if($user->uid == 25204506) continue;
	if($user->uid == 22575230) continue;
	$oauth_tokens=array('token' => $user->token, 'token_secret' => $user->token_secret);
	$oauth->setToken($oauth_tokens);
	$dropbox = new Dropbox_API($oauth);

	//$dropbox->createFolder("Public/android-x86");
	try {
	    $androidDir=null;
	    $androidDir=$dropbox->getMetaData("Public/" . $nightlies->contentPath);

	    $acct=$dropbox->getAccountInfo();
	    $uid=$acct['uid'];

	    if($androidDir['contents'] == NULL) {
		throw new Exception("NOUPLOAD");
	    }

	    foreach($androidDir['contents'] as $content) {
		$dPath=preg_replace("/^\/Public\//", "", $content['path']);
		$dFile=basename($dPath);
		array_push($user->files, Array('content' => $dFile));
	    }
	    array_push($nightlies->users, $user);
	} catch (Dropbox_Exception_NotFound $e) {
    //	echo "New user must create <code>Public/android-x86/</code>...\n";
    //	echo "<em>$user->display_name</em>, please read the documentation!<br/>\n";


	} catch (OAuthException $e) {
	    $errorcode.="Luser has de-authed the app...\n";
	    $errorcode.="$user->display_name, goodbye :-(<br/>\n";
	} catch (Exception $e) {
	    if($e->getMessage()=="NOUPLOAD") {
    //	    echo "New user must upload something...\n";
    //	    echo "<em>$user->display_name</em>, please make a build!<br/>\n";
	    }
	    else {
    //	    echo "Unhandled exception!<br/>\n";
	    }
	}
    }

    $nightlies->errorcode=$errorcode;
    $nightlies->hash=hash("sha256",serialize($nightlies));
    $output=$nightlies->render($template);
    return $output;
}
?>
