<?php


class __debug__  extends __application__  {

 
    public function _createObject($className, $aOption) {

        $oObject = new $className($this->core,$this, $aOption);

        $pObject = new debug_component($this->core, $this, $oObject, $className);

        return $pObject;

    }

       /**
     * Returns a property value, an event handler list or a behavior based on its name.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using the following syntax to read a property or obtain event handlers:
     * <pre>
     * $value=$component->propertyName;
     * $handlers=$component->eventName;
     * </pre>
     * @param string $name the property name or event name
     * @return mixed the property value, event handlers attached to the event, or the named behavior
     * @throws CException if the property or event is not defined
     * @see __set
     */
    public function __get($name) {

        return $this -> $name;

    }

    /**
     * Sets value of a component property.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using the following syntax to set a property or attach an event handler
     * <pre>
     * $this->propertyName=$value;
     * $this->eventName=$callback;
     * </pre>
     * @param string $name the property name or the event name
     * @param mixed $value the property value or callback
     * @return mixed
     * @throws CException if the property/event is not defined or the property is read only.
     * @see __get
     */
    public function __set($name, $value) {   

        return  $this  -> $name = $value;

    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method. This is a PHP magic method that we override
     * to implement the behavior feature.
     * @param string $name the method name
     * @param array $parameters method parameters
     * @return mixed the method return value
     */
    public function __call($name, $parameters) {
        
        $oResult =  call_user_func_array(array($this, $name), $parameters);

         $this -> core -> log(array(' [  ' . $this -> sClassName . "->" . $name . "()  ]" => $this -> cleanParams($parameters)),$oResult);

         return $oResult;
        

    }
    
}
