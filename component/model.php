<?php

class model extends component {

    var $_role = null;
    var $_table = null;
    var $_id = array();
    
    

    public function all() {

        return $this -> application -> db($this -> _role) -> createCommand() -> select('*') -> from($this -> _table) -> queryAll();

    }

    public function find($aParam) {

        $fields = array();
        $value = array();

        foreach ($aParam as $paramName => $paramValue) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $paramValue;
        }



        return $this -> application -> db($this -> _role) -> createCommand() -> select('*') -> from($this -> _table) -> where(join(' AND ', $fields), $value)-> queryAll();


    }

    public function get($aParam) {

        $fields = array();
        $value = array();

        foreach ($this->_id as $paramName) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $aParam[$paramName];
        }

        return $this -> application -> db($this -> _role) -> createCommand() -> select('*') -> from($this -> _table) -> where(join(' AND ', $fields), $value) -> queryRow();

    }

    public function update($aParam) {

        $bChange = $this -> _update($aParam);

        $aUpdate = $this->get($aParam);

        $aUpdate['updated'] =$bChange;

        return  $aUpdate;

    }

    public function _update($aParam) {

        $fields = array();
        $value = array();

        foreach ($this->_id as $paramName) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $aParam[$paramName];
        }


        return $this -> application -> db($this -> _role) -> createCommand() -> update($this -> _table, $aParam, join(' AND ', $fields), $value);
    }

    public function updateWhere($aParam, $aWhere) {

        $fields = array();
        $value = array();

        foreach ($aWhere as $paramName => $paramValue) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $paramValue;
        }

        return $this -> application -> db($this -> _role) -> createCommand() -> update($this -> _table, $aParam, join(' AND ', $fields), $value);

    }

    public function deleteWhere($aParam) {

        $fields = array();
        $value = array();

        foreach ($aParam as $paramName => $paramValue) {
            $fields[] = $paramName . '=:' . $paramName;
            $value[':' . $paramName] = $paramValue;
        }

        return $this -> application -> db($this -> _role) -> createCommand() -> delete($this -> _table, join(' AND ', $fields), $value);

    }

    public function create($aParam) {

        $id = $this -> _create($aParam);

        if (!isset($aParam[$this -> _id[0]])) {
            $aParam[$this -> _id[0]] = $id;
        }

        if ($id) {
            return $this -> get($aParam);

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


    public function findLink($aParam,$itemId) {


      $value = array();
      $fields = array();

      $id = $this->_table.'.'.$itemId;

      $command =  $this -> application -> db($this -> _role) -> createCommand() -> select($this -> _table.'.*') -> from($this -> _table);


      foreach ($aParam as $paramName => $paramValue) {

         $sTableName = $this->_table.'_'.str_replace('Id', '', $itemId);
         $fields[] = $sTableName .'.'.$paramName. '=:' . $paramName;
         $value[':' . $paramName] = $paramValue;


         $command->join($sTableName, $id.' = '.' '.$sTableName.'.'.$itemId);
     }    


     return $command->where( join(' AND ', $fields), $value) -> queryRow();


 }


     public function findbyLink($aParam,$itemId,$resultId) {


      $value = array();
      $fields = array();

      $id = $this->_table.'.'.$itemId;

      $sMainTableName = $this->_table.'_'.str_replace('Id', '', $resultId);

      $command =  $this -> application -> db($this -> _role) -> createCommand() -> select('*') -> from($sMainTableName);


      foreach ($aParam as $paramName => $paramValue) {

         $sTableName = $this->_table.'_'.str_replace('Id', '', $resultId);
         $fields[] = $sTableName .'.'.$itemId. '=:' . $paramName;
         $value[':' . $paramName] = $paramValue;

         if($sTableName != $sMainTableName){
            $command->join($sTableName, $id.' = '.' '.$sTableName.'.'.$itemId);
         }
     }    

     return $command->where( join(' AND ', $fields), $value) -> queryRow();

 }

 public function getLinks($aParam){

    $aConfig = $this->application-> config ->controller($this->application->route['controllerName'])->_links();


    $fields = array();
    $value = array();

    foreach ($this->_id as $paramName) {
        $fields[] = $paramName . '=:' . $paramName;
        $value[':' . $paramName] = $aParam[$paramName];
    }

    foreach($aConfig as $link =>$paramConfig){

        if(isset($paramConfig['key']) != true){

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
