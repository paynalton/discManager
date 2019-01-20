<?php

namespace magia\parsers;

class file{
   public $content_type="text/plain";
   protected $filePath;
   
   function __construct($fileName) {
       $this->filePath=$fileName;
       $finfo = finfo_open(FILEINFO_MIME_TYPE);
       $this->content_type=finfo_file($finfo,$this->filePath);
       if($this->content_type=="text/plain"||$this->content_type=="text/x-c++"){
           switch(explode(".",$this->filePath)[count(explode(".",$this->filePath))-1]){
               case "js":
                   $this->content_type="application/javascript";
                   break;
               case "css":
                   $this->content_type="text/css";
                   break;
           }
       }
       
   }
   
   public function runHeaders(){
       header("content-type:".$this->content_type);
   }
   
   public function run(){
       echo file_get_contents($this->filePath);
   } 
}