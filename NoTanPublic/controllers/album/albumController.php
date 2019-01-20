<?php

namespace magia\controllers\album;

use magia\models\album as albumModel;
use magia\controller as controller;
use \Exception as Exception;

class album extends controller{
    
    public function add(){
        $album = new albumModel();
        try{
            $previo= albumModel::all([
                "fechaAlta"=>[
                    "operador"=>">",
                    "valor"=>date("Y-m-d H:i:s",time()-(600))
                    ]
                ],[1]);
            if(count($previo)){
                throw new Exception("No han pasado 10 minutos");
            }
            $album->dataFromPost($_POST)->save();
            $this->responseJSON($album);
        }catch(Exception $e){
            $this->responseJSON([
                "error"=>1,
                "errorMessage"=>$e->getMessage()
            ]);
        }
    }
    
    public function listado(){
        $albums= albumModel::all([],[30]);
        $this->responseJSON($albums);
    }
    
}
