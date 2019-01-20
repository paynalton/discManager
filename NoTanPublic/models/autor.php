<?php

namespace magia\models;

use magia\core as core;
use \JsonSerializable as JsonSerializable;

class autor implements JsonSerializable{
    private $table="autor";
    
    public function save(){
        $db=core::getDatabase();
        $db->insertOrUpdate($this->table,[
            "nombre"=>$this->nombre
        ]);
        
        if($db->insert_id){
            $this->id=$db->insert_id;
        }
        
        return $this;
    }
    
    function __get($name) {
        if($name=="id"&&!$this->id){
            $db=core::getDatabase();
            return $this->id=$db->getData($this->table,"id","nombre","like",$this->nombre,"integer");
        }
        if($name=="nombre"&&$this->id){
            $db=core::getDatabase();
            return $this->nombre=$db->getData($this->table,"nombre","id","=",$this->id,"string");
        }
    }
    
    
    function jsonSerialize(){
        return [
          "id"=>$this->id,
          "nombre"=>$this->nombre          
        ];
    }
}
