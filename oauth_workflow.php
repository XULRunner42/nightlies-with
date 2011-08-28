<?php

require_once("Nightlies.php");
require_once("Nigh_top.php");

require_once("user.php");

if (! isset($_SESSION['acct']) && isset($_SESSION['oauth_tokens'])) {
    $oauth->setToken($_SESSION['oauth_tokens']);
    try {
        $tokens = $oauth->getAccessToken();

        $dropbox = new Dropbox_API($oauth);
        $acct=$dropbox->getAccountInfo();

        $user = new User($tokens['token'], $tokens['token_secret'],
            $acct['referral_link'], $acct['display_name'], $acct['uid'],
            $acct['email']);

        $user->save();

        $_SESSION['acct'] = $acct;
        $_SESSION['oauth_tokens'] = $tokens;

    } catch (OAuthException $e) {
        echo "<p>We do not have a valid access token yet.  Visit Dropbox to
        log in and connect your account to the Android-x86 Nightlies Tool, 
        so we can index your builds.</p>\n";
        echo "<p>Visit <code><a href='";
        echo $oauth->getAuthorizeUrl();

        $return_path=urlencode($APPROOT . "oauth_workflow.php");
        echo "&oauth_callback=" . $return_path . "'>";
        echo "this link</a></code> and log in to try the authorization again.";
    }
}

if (!isset($_SESSION['acct']) && !isset($_SESSION['oauth_tokens'])) {

    echo "<p>Following the next link, you can log in with your Dropbox account 
	and authorize the android-x86 nightlies tool to index your 
	<code>android-x86/</code> folder.</p>\n";
    $tokens = $oauth->getRequestToken();

    echo "<p>Visit Dropbox to connect your account:\n";
    echo "<code><a href='";
    echo $oauth->getAuthorizeUrl();

    $return_path=urlencode($APPROOT . "oauth_workflow.php");
    echo "&oauth_callback=" . $return_path . "'>";
    echo "this link</a></code> should get it done.</p>\n";
    $_SESSION['oauth_tokens'] = $tokens;
}
if(isset($_SESSION['acct']) && $_SESSION['acct'] != null) {
    echo "<p>You are logged in, and the Android-x86 Nightlies tool has been 
        authorized.  Thanks!</p>\n";
    echo "<p>Go to <a href='nightlies.php'>the index</a>, and see your 
        files!</p>\n";
    echo "<p>The server will index your files when you do, and the contents of 
        your <code>android-x86/</code> folder should show up in the listing 
        immediately.  Future updates will not show up for a half hour due to 
        caching, so just be patient.</p>";
    $oauth->setToken($_SESSION['oauth_tokens']);

    $dropbox = new Dropbox_API($oauth);
}
?></p>
<?php include 'Nigh_bottom.php'; ?>
