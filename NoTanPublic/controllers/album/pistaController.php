<?php

namespace magia\controllers\album;

use magia\models\pista as pistaModel;
use magia\controller as controller;

class pista extends controller{
    
    public function add(){
        $pista = new pistaModel();
        $pista->dataFromPost($_POST)->save();
        $this->responseJSON($pista);
    }
    public function borrar(){
        $pista = new pistaModel();
        $pista->dataFromPost($_GET)->delete();
        $this->responseJSON($pista);
    }
    
    public function listado(){
        $pistas= pistaModel::all([
            "album"=>$_GET["album"]
        ],[30]);
        $this->responseJSON($pistas);
    }
    
}
