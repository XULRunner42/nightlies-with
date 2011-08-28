<?php
include("memcache.php");
?>
<pre>
<?php
echo "nightlies.lock: " . $memcache->get("nightlies.lock") . "\n";
echo "nightlies.wlock: " . $memcache->get("nightlies.wlock") . "\n";
echo "nightlies.timer: " . $memcache->get("nightlies.timer") . "\n";
echo "nightlies: ";
?>
</pre>
<?
echo $memcache->get("nightlies");
?>
