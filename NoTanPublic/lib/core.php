<?php

namespace magia;

use magia\parsers\file as File;
use magia\parsers\configFile as ConfigFile;
use magia\database as database;

class core{
    
    private $path;
    private $resource;
    
    public $config=[];
    
    const PATH_LIB="../NoTanPublic/lib";
    const PATH_CONTROLLERS="../NoTanPublic/controllers";
    const PATH_CONFIG="../NoTanPublic/config";
    const PATH_MODELS="../NoTanPublic/models";
    
    static function autoloadLib($className){
        $path=self::PATH_LIB.str_replace(["\\","magia"],["/",""],$className).".php";
        //echo $path;exit;
        if(file_exists($path)){
            include_once($path);
        }
    }
    static function autoloadControllers($className){
        $path=self::PATH_CONTROLLERS.str_replace(["\\","magia","controllers/","Controller"],["/","","",""],$className)."Controller.php";
        //echo $path;exit;
        if(file_exists($path)){
            include_once($path);
        }
    }
    static function autoloadModels($className){
        $path=self::PATH_MODELS.str_replace(["\\","magia","models/"],["/","",""],$className).".php";
        //echo $path;exit;
        if(file_exists($path)){
            include_once($path);
        }
    }
    
    static function getDatabase(){
        return $GLOBALS["core"]->getDatabaseST();
    }
    
    static function initAutoloads(){
        spl_autoload_register(__NAMESPACE__ ."\core::autoloadLib");
        spl_autoload_register(__NAMESPACE__ ."\core::autoloadControllers");
        spl_autoload_register(__NAMESPACE__ ."\core::autoloadModels");
    }
    
    public function getDatabaseST(){
        if(!$this->database){
            $this->database=new database(
                    $this->config["database"]->host,
                    $this->config["database"]->user,
                    $this->config["database"]->password,
                    $this->config["database"]->database
                    );
        }
        return $this->database;
    }
    
    public function initConfigFiles(){
        foreach(scandir(self::PATH_CONFIG) as $file){
            $path=self::PATH_CONFIG."/".$file;
            if(file_exists($path)&&!is_dir($path)){
                $this->config[array_shift(explode(".",$file))]=new ConfigFile($path);
            }
        }
    }
    
    static function init(){
        self::initAutoloads();
        $core= new core();
        $core->initConfigFiles();
        $path=trim(str_replace([getcwd(),".."],"",$_SERVER["DOCUMENT_ROOT"].$_SERVER["REDIRECT_URL"]),"/.");
        $core->setPath($path);
        $GLOBALS["core"]=$core;
        return $core;
    }
    
    public function setPath($path){
        $this->path=$path;
        $this->defineResource();
    }
    
    public function defineResource(){
        $this->wissResource();
        if(!$this->resource){
            $this->setResourceFile("index.html");
        }
    }
    
    public function wissResource(){
        if(file_exists($this->path)&&!is_dir($this->path)){
            $this->setResourceFile($this->path);
            return;
        }else if($this->config["routes"]->route[$this->path]){
            $this->setResourceController($this->config["routes"]->route[$this->path]);
            return;
        }
        
    }
    
    public function setResourceController($config){
        $class=$config["className"];
        if(class_exists($class)){
            $this->resource=new $class($config);
        }
    }
    
    public function setResourceFile($filePath){
        if(file_exists($filePath)){
            $this->resource=new File($filePath);
        }
    }
    
    public function run(){
        if($this->resource){
            $this->resource->runHeaders();
            return $this->resource->run();
        }
    }
}
