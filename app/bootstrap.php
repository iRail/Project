<?php

/**
 * Projects page for iRail
 * @author: Michiel Vancoillie (michiel@irail.be)
 */

use irail\core\Config;
use irail\github\Project;

// Load config files
Config::load();

// Get the projects
$projects = Config::get('projects');

// Start mustache engine
$m = new Mustache_Engine;


// Load project template file
$project_template = file_get_contents(TEMPLATEPATH.'project');

// Variable for projects HTML
$projectsHTML = "";


// Loop projects
foreach($projects as $project){
    foreach($project['subprojects'] as $key => $subproject){
        $project['subprojects'][$key] = new Project($subproject);
    }

    $projectsHTML .= $m->render($project_template, $project);
}


// Load main template file
$main_template = file_get_contents(TEMPLATEPATH.'page');

// Set data for main template
$data['projects'] = $projectsHTML;

// Go ahead an display all
echo $m->render($main_template, $data);