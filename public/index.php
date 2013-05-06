<?php

/**
 * Projects page for iRail
 * @author: Michiel Vancoillie (michiel@irail.be)
 */

// Define constants
define(APPPATH, __DIR__ . '/../app/');
define(TEMPLATEPATH, APPPATH . 'templates/');

define(GITHUBAPI, 'https://api.github.com/repos/');
define(GITHUB, 'https://github.com/');

// Composer autoloading
require '../vendor/autoload.php';

// Launch the project
require APPPATH . 'bootstrap.php';