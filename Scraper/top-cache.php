<?php
//Koden är tagen från http://www.catswhocode.com/blog/how-to-create-a-simple-and-efficient-php-cache men är lite modifierad
//för att passa labben.
$url = $_SERVER["SCRIPT_NAME"];
//$break = Explode('/', $url);
//$file = $break[count($break) - 1];
$cachefile = 'cache.json';
$cachetime = 300;

// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    include($cachefile);
    exit;
}
ob_start(); // Start the output buffer