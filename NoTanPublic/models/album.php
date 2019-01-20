<?php

namespace magia\models;

use magia\models\autor as autor;
use magia\core as core;
use \JsonSerializable as JsonSerializable;

class album  implements JsonSerializable{
    public $titulo;
    public $autor;
    public $fechaPublicacion;
    public $materialAdicional;
    
    const TABLE="album";
    
    static function all($filtters=[],$limit=[]){
        $db=core::getDatabase();
        return $db->getAllModells(get_called_class(),$filtters,$limit);
    }
    
    public function dataFromDatabase($data){
        $this->id=intval($data->id);
        $this->titulo=$data->titulo;
        $this->fechaPublicacion=strtotime($data->fechaPublicacion);
        $this->activo=$data->activo>0;
        $this->autor=new autor();
        $this->autor->id=$data->autor;
        $this->pistas=intval($data->pistas);
        $this->fechaAlta=strtotime($data->fechaAlta);
        $this->materialAdicional=$data->contenidoAdicional>0;
        return $this;
    }
    
    public function dataFromPost($post){
        
        $this->titulo=trim($post["titulo"]);
        $this->autor=new autor();
        $this->autor->nombre=trim($post["autor"]);
        $this->fechaPublicacion= strtotime($post["fechaPublicacion"]);
        $this->materialAdicional=$post["materialAdicional"]?true:false;
        
        return $this;
    }
    
    public function save(){
        
        
        $db=core::getDatabase();
        
        
        
        $this->autor->save();
        
        $data=[
          "titulo"=>$this->titulo,
          "autor"=>$this->autor->id,
          "fechaPublicacion"=>date("Y-m-d",$this->fechaPublicacion),
          "contenidoAdicional"=>$this->materialAdicional?"1":"0"
        ];
        
        $db->insertOrUpdate(self::TABLE,$data);
        
        
        if($db->insert_id){
            $this->id=$db->insert_id;
        }
        return $this;
    }
    function jsonSerialize(){
        return [
          "id"=>$this->id,
          "titulo"=>$this->titulo,
          "autor"=>$this->autor,
          "fechaPublicacion"=>date("Y-m-d",$this->fechaPublicacion),
          "fechaAlta"=>date("Y-m-d",$this->fechaAlta),
          "materialAdicional"=>$this->materialAdicional?true:false,
           "activo"=>$this->activo?true:false,
           "pistas"=>$this->pistas,
           "portada"=>$this->portada,            
        ];
    }
}
