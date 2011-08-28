<pre>
<?
include("memcache.php");
echo $memcache->delete("nightlies.lock", 0);
echo $memcache->delete("nightlies.wlock", 0);
?>
</pre>
