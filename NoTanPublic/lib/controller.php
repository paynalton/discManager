<?php

namespace magia;

use magia\parsers\file as File;
use magia\parsers\configFile as ConfigFile;
use magia\database as database;

class controller{
    protected $config;
    protected $content_type="text/html";
    
    
    function __construct($config){
        $this->config=$config;
    }
    
    
    public function runHeaders(){
        //header("content-type:".$this->content_type);
    }

    protected function responseJSON($objeto){
        header("content-type:application/json");
        echo json_encode($objeto);
    }
    public function run(){
        
        $route=$this->config[$_SERVER["REQUEST_METHOD"]];
        if(method_exists($this, $route)){
            $this->$route();
        }
    } 
    
}
