<?php

/**
 * Projects page for iRail
 * @author: Michiel Vancoillie (michiel@irail.be)
 */


// Load config files


// Start mustache engine
$m = new Mustache_Engine;

// Load main template file
$main_template = file_get_contents(TEMPLATEPATH.'page');
echo $m->render($main_template);