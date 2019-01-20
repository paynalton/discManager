<?php

namespace magia\models;

use magia\models\autor as autor;
use magia\core as core;
use \JsonSerializable as JsonSerializable;
use magia\models\album as albumModel;

class pista  implements JsonSerializable{
    public $nombre;
    public $id;
    
    const TABLE="pista";
    
    static function all($filtters=[],$limit=[]){
        $db=core::getDatabase();
        return $db->getAllModells(get_called_class(),$filtters,$limit);
    }
    
    public function dataFromDatabase($data){
        $this->id=intval($data->id);
        $this->nombre=$data->nombre;
        $this->album=new albumModel();
        $this->autor->id=$data->album;
        return $this;
    }
    
    public function dataFromPost($post){
        
        $this->nombre=trim($post["nombre"]);
        $this->id=trim($post["id"])?intval($post["id"]):null;
        $this->album=new albumModel();
        $this->album->id=$post["album"];
        
        return $this;
    }
    
    public function delete(){
        $db=core::getDatabase();
        
        $db->delete(self::TABLE,[
            "id"=>$this->id
        ]);
    }
    
    public function save(){
        
        
        $data=[
          "nombre"=>$this->nombre,
          "album"=>$this->album->id
        ];
        
        if($this->id){
            $data["id"]=$this->id;
        }
        
        $db=core::getDatabase();
        
        $db->insertOrUpdate(self::TABLE,$data);
        
        
        if($db->insert_id){
            $this->id=$db->insert_id;
        }
        return $this;
    }
        
    function jsonSerialize(){
        return [
          "id"=>$this->id,
          "nombre"=>$this->nombre,
          "album"=>$this->album
        ];
    }
}
