<?
include "Nightlies.php";
include "user.php";

?>
<pre>
<?
$theuser=Array("Viki Barrett", "Harry Davis", "Kingdon Barrett");

$nightlies=new Nightlies();
$users=User::fetchUsers();

foreach($users as $user) {
    if(!in_array($user->display_name, $theuser)) continue;
    $oauth_tokens=array('token' => $user->token, 'token_secret' => $user->token_secret);
    $oauth->setToken($oauth_tokens);
    $dropbox = new Dropbox_API($oauth);

    try {
	$acct=$dropbox->getAccountInfo();
	print_r($acct);
    }
    catch (Exception $e) {
	echo $e->getMessage();
    }
}
?>
</pre>
