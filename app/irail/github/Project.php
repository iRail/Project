<?php

/**
 * Class for Github projects
 * @author: Michiel Vancoillie (michiel@irail.be)
 */

namespace irail\github;

class Project{
    public $title;
    public $github;

    public function __construct($data){
        $this->title = $data['title'];
        $this->github = $data['github'];
    }

    /**
     * Form Github URL
     */
    public function githubRepo(){
        return GITHUB . $this->github;
    }
}