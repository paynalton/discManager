<?php

namespace magia\parsers;

use magia\parsers\file as File;

class configFile extends File{
    private $loaded=false;
    private $valores;
    
    private function load(){
        $this->valores= parse_ini_file($this->filePath,true);
        $this->loaded=true;
    }
    
    public function __get($name){
        if(!$this->loaded){
            $this->load();
        }
        return $this->valores[$name];
    }
}
