<?php

/**
 * Class to load all config files
 * @author: Michiel Vancoillie (michiel@irail.be)
 */

namespace iRail\core;

class Config{
    private static $config = array();

    /**
     * Load each config file into the array
     */
    public static function load(){
        $config_path = APPPATH . 'config/';

        if ($handle = opendir($config_path)) {

            // Loop directory
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    // Get group from filename
                    $group = basename($entry, ".php");

                    // Store in the array
                    self::$config[$group] = require_once($config_path . $entry);
                }
            }
            closedir($handle);
        }
    }

    /**
     * Get a config variable or the whole group
     * Returns null for non-existing items
     */
    public static function get($group, $variable = null){
        if(!empty(self::$config[$group])){
            $group = self::$config[$group];
            if($variable){
                if(!empty($group[$variable])){
                    return $group[$variable];
                }
            }else{
                return $group;
            }
        }
        return null;
    }
}