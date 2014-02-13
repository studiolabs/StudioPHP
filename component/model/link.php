<?php



class model_link extends model {
    
    
    public $_sModel = array();
    
    public $_aModel = array();
    
    
    
    
    public function _init(){
        
        foreach($this->_sModel as $model ){
            
            $this->$_aModel[$model] = $this->application->model($model); 
            
            $this->_id = array_merge($this->$_aModel[$model]->_id,$this->_id);
            
        }
    }


    public function _create($aParam) {

        $db = $this -> application -> db($this -> _role);

        $aParam['insertDate'] = date('Y-m-d H:i:s');

        if ($db -> createCommand() -> insert($this -> _table, $aParam)) {
                
                $keys = array();
                foreach($this->_id  as $index){
                   $keys[$index]  = $aParam[$index];
                }
                
                return $keys;
            
        } else {

            return false;

        }

    }

    public function create($aParam) {

        $ids = $this -> _create($aParam);

        if (count($ids)>0) {
            return $this -> get($ids);

        } else {

            return false;

        }

    }
    
     public function findLinkedId($aParam,$itemId){
        
       
        $value = array();
        $fields = array();
        
        $id = $this->_table.'.'.$itemId;
        
        $command =  $this -> application -> db($this -> _role) -> createCommand() -> select($this -> _table.'.*') -> from($this -> _table);
      
        foreach ($aParam as $paramName => $paramValue) {
            $fields[] = $this->_table.'_'.$paramName.'.'.$paramName. '=:' . $paramName;
            $value[':' . $paramName] = $paramValue;
        }    

        foreach($aParam as $key => $item){
            $table = $this->_table.'_'.$key;
            $command->join($table, $id.' = '.' '.$table.'.'.$itemId);
        }
        
        return $command->where( join(' AND ', $fields), $value) -> queryRow();
        
    }
     
     
    public function getLinks($aParam){
        
        $aConfig = $this->application->config->controller(array(
        'controllerName'=> $this->application->route['controllerName'],
        'actionName'=>'_links'
        ));
        
        $fields = array();
        $value = array();

        foreach ($this->_id as $paramName) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $aParam[$paramName];
        }
        
        foreach($aConfig->param as $link =>$paramConfig){
            
            if($paramConfig['key'] != true){
            
             $result =  $this -> application -> db($this -> _role) -> createCommand() -> select('*') -> from($this -> _table.'_'.$link)->  where(join(' AND ', $fields), $value) -> queryAll();
                      
             foreach($result as $row){
                   
                   $data[$link.'[]'][] = $row[$link];
                   
             } 
             
            }
            
        }
        
              
         return  $data;
        
        
    }
    
    
    
    
    public function links($aParam){
        
        $aKeyParam = array();
        
        foreach($this->_id as $paramName){
            $aKeyParam[$paramName] = $aParam[$paramName];
        }       
        
        foreach($aParam as $table => $params){
            
            if(is_array($params)){
                
                $this->_unlink($table,$aKeyParam);
        
                foreach($params as $value){
                    
                    $this->_link($table,$value,$aKeyParam);
                }
            }
            
        }
        
        
        return  true;
        
    }   


    public function _link($key,$value,$aParam) {

        $db = $this -> application -> db($this -> _role);
        
        $aParam[$key] = $value;

        $aParam['insertDate'] = date('Y-m-d H:i:s');

        if ($db -> createCommand() -> insert($this -> _table.'_'.$key, $aParam)) {

            return $db -> getLastInsertID();

        } else {

            return false;

        }

    }    
     public function _unlink($table, $aKeyParam) {

        $fields = array();
        $value = array();

        foreach ($aKeyParam as $paramName => $paramValue) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $paramValue;
        }

        return $this -> application -> db($this -> _role) -> createCommand() -> delete($this -> _table.'_'.$table, join(' AND ', $fields), $value);

    }

    public function _create($aParam) {

        $db = $this -> application -> db($this -> _role);

        $aParam['insertDate'] = date('Y-m-d H:i:s');

        if ($db -> createCommand() -> insert($this -> _table, $aParam)) {
            return $db -> getLastInsertID();
        } else {

            return false;

        }

    }

    public function delete($aParam) {

        $fields = array();
        $value = array();

        foreach ($this->_id as $paramName) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $aParam[$paramName];
        }

        return $this -> application -> db($this -> _role) -> createCommand() -> delete($this -> _table, join(' AND ', $fields), $value);

    }    
    

}