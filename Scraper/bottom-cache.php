<?php
// Cache the contents to a file
$cached = fopen('cache.json', 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
ob_end_flush(); // Send the output to the browser