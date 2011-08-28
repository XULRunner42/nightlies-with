<?php

/* Please supply your own consumer key and consumer secret */
$consumerKey = '###############';
$consumerSecret = '###############';

include 'Dropbox/autoload.php';

session_start();

$oauth = new Dropbox_OAuth_PHP($consumerKey, $consumerSecret);

header('Content-Type: text/html');

session_destroy();

echo "Account info:\n";

echo "<pre>The session has been deinitialized:\n\n";
if(isset($dropbox)) {
    if(method_exists($dropbox, "getAccountInfo")) {
        $acct=$dropbox->getAccountInfo();
        print_r($acct);
    }
}
echo "[[conspicuous lack of any account info]]</pre>\n";
echo "<a href='oauth_workflow.php'>back to oauth workflow</a>";

?>
