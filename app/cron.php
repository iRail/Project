<?php

/**
 * File to call in a cronjob
 * Mind the GitHub API rate limits
 *
 * @author: Michiel Vancoillie (michiel@irail.be)
 */

// Define constants
define(APPPATH, __DIR__ . '/../app/');
define(TEMPLATEPATH, APPPATH . 'templates/');

define(GITHUBAPI, 'https://api.github.com/repos/');
define(GITHUB, 'https://github.com/');

// Github URI's to fetch
$endpoints = array('', '/issues', '/milestones');

// Composer autoloading
require '../vendor/autoload.php';

use iRail\core\Config;

// Load config files
Config::load();

// Get the projects
$projects = Config::get('projects');

// Get the API user
$github_account = Config::get('api');

// Open or create DB
$db = new SQLite3(APPPATH . '../db/projects.db');

// Construct table when it doesn't exist
$db->exec('CREATE TABLE IF NOT EXISTS endpoint (id INTEGER PRIMARY KEY AUTOINCREMENT, uri STRING, data TEXT)');


// Setup cURL
$ch = curl_init();
// User agent
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Timeout in seconds
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Don't return header
curl_setopt($ch, CURLOPT_HEADER, 0);


if(!empty($github_account['user']) && !empty($github_account['password'])){
    // Authenticate for max rate limits
    curl_setopt($ch, CURLOPT_USERPWD, $github_account['user'].":".$github_account['password']);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
}


// Loop projects
foreach($projects as $project){

    foreach($project['subprojects'] as $subproject){
        if(!empty($subproject['github'])){
            $repo_uri = GITHUBAPI . $subproject['github'];

            // Fetch data for all endpoints
            foreach($endpoints as $endpoint){
                // Set URL to download
                curl_setopt($ch, CURLOPT_URL, $repo_uri . $endpoint);

                // Download the given URL, and return output
                $repo_data = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if($http_status == 200){

                    $results = $db->query('SELECT id FROM endpoint WHERE uri="' . $repo_uri . $endpoint . '"');
                    $results = $results->fetchArray();

                    // Put information in database (update or insert)
                    if(!empty($results['id'])){
                        $id = $results['id'];

                        $stmt = $db->prepare('UPDATE endpoint SET data=:data  WHERE id=:id');
                        $stmt->bindValue(':data', $repo_data, SQLITE3_TEXT);
                        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                        $stmt->execute();
                    }else{
                        $stmt = $db->prepare('INSERT INTO endpoint (uri, data) VALUES (:uri,:data)');
                        $stmt->bindValue(':uri', $repo_uri . $endpoint, SQLITE3_TEXT);
                        $stmt->bindValue(':data', $repo_data, SQLITE3_TEXT);
                        $stmt->execute();
                    }

                }
            }
        }
    }

}

// Close the cURL resource
curl_close($ch);

// Close db connection
$db->close();