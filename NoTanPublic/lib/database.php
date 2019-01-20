<?php

namespace magia;

use \mysqli as mysqli;

class database extends mysqli{
    
    public function getAllModells($class,$filters=[],$limit=[]){
        
        $where=[];
        
        foreach($filters as $k=>$v){
            if(is_array($v)){
                $operador="=";
                switch($v["operador"]){
                    case "<":
                        $operador="<";
                        break;
                    case ">":
                        $operador=">";
                        break;
                    case "like":
                        $operador="like";
                        break;
                            
                }
                $where[]="`".str_replace("`","\\`",$this->escape_string($k))."`".$operador."'".str_replace("'","\\'",$this->escape_string($v["valor"]))."'";
            }else{
                $where[]="`".str_replace("`","\\`",$this->escape_string($k))."`='".str_replace("'","\\'",$this->escape_string($v))."'";
            }
        }
        
        $query=sprintf("select * from `%s` %s %s",
                $class::TABLE,
                count($where)?"where ".implode(" and ",$where):"",
                count($limit)?"limit ".implode(",",$limit):""
                );
        $smt=$this->query($query);
        
        $resp=[];
        
        while($data = $smt->fetch_object()){
            $o=new $class();
            $o->dataFromDatabase($data);
            $resp[]=$o;
        }
        
        return $resp;
    }
    
    public function delete($table,$filters=[],$limit=[]){
        $where=[];
        
        foreach($filters as $k=>$v){
            $where[]="`".str_replace("`","\\`",$this->escape_string($k))."`='".str_replace("'","\\'",$this->escape_string($v))."'";
        }
        
        $query= sprintf("delete from %s %s %s;",
                $table,
                count($where)?"where ".implode(" and ",$where):"",
                count($limit)?"limit ".implode(",",$limit):""
                );
        
        
        $this->query($query);
    }
    
    public function insertOrUpdate($table,$data){
        $pdata=[];
        $pdata2=[];
        foreach($data as $k=>$v){
            $k=str_replace("`","\\`",$this->escape_string($k));
            $v=str_replace("'","\\'",$this->escape_string($v));
            $pdata[$k]=$v;
            $pdata2[]="`".$k."`='".$v."'";
        }
        $query= sprintf("insert into `%s`(`%s`) values('%s') on duplicate key update %s;",
                $this->escape_string($table),
                implode("`,`",array_keys($pdata)),
                implode("','",$pdata),
                implode(",",$pdata2)
                );
        //echo $query;
        $this->query($query);
        //print_r($this);exit;
        return $this;
    }
    
    public function getData($table,$column,$field,$operation="=",$value="",$type="string"){
        
        $operador="=";
        switch($operation){
            case "like":
                $operador="like";
                break;
        }
        
        $query=sprintf("select `%s` as 'data' from `%s` where %s %s '%s' limit 1;",
            str_replace("`","\\`",$this->escape_string($column)),
            str_replace("`","\\`",$this->escape_string($table)),
            str_replace("`","\\`",$this->escape_string($field)),
                $operador,
            str_replace("'","\\'",$this->escape_string($value))
            );
        
       $d=$this->query($query)->fetch_assoc()["data"];
       
       switch($type){
           case "integer":
               return intval($d);
           default:
               return $d;
       }
        
    }
}