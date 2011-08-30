<?php
$MEMCACHE_SERVERS=array(
  "10.8.89.1", //irie-arch
);

global $memcache;
$memcache=new Memcache();
foreach($MEMCACHE_SERVERS as $server) {
    $memcache->addServer($server);
}

// nightlies.timer is minimum time to cache regen
function setNightlies_Timer() {
    global $memcache;
    $memcache->add("nightlies.timer", "1", 0, 3570);
}

// _R stands for read lock
function lockNightlies_R() {
    global $memcache;
    while(!$memcache->add("nightlies.lock", 'r')) {
	sleep(1);
    }
}
function unlockNightlies_R() {
    global $memcache;
    $memcache->delete("nightlies.lock", 0);
}
function testNightlies_R() {
    global $memcache;
    return true && $memcache->get("nightlies.lock");
}

// _W stands for write lock
function lockNightlies_W() {
    global $memcache;
    while(!$memcache->add("nightlies.wlock", 'w')) {
	sleep(1);
    }
}
function tryLockNightlies_W() {
    global $memcache;
    return true && $memcache->add("nightlies.wlock", 'w');
}
function unlockNightlies_W() {
    global $memcache;
    $memcache->delete("nightlies.wlock", 0);
}
function testNightlies_W() {
    global $memcache;
    return true && $memcache->get("nightlies.wlock");
}
/*
echo "<pre>\n";
echo $memcache->delete("test", 0) . "\n";
//echo "success: " . $memcache->add("test", "teststring") . "\n";
echo "test   : " . $memcache->get("test") . "\n";
echo "</pre>";
*/
?>
