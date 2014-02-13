<?php

class debug_component {

    public $_behavior;

    public $sClassName;

    public $core;

    public $application;

    var $_debug = true;

    public function __construct(&$pCore, &$pApplication, &$pBehavior, $sClassName) {


        $this -> core = $pCore;

        $this -> application = $pApplication;

        $this -> _behavior = $pBehavior;

        $this -> sClassName = $sClassName;

     /*    $this -> core -> log( $sClassName,strpos($sClassName, 'controller') ) ;


        if(strpos($sClassName, 'controller') !== false){

            $this->_debug =true;

        }*/

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

        return $this -> _behavior -> $name;

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

        return  $this -> _behavior -> $name = $value;

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
        
        $aResult =  call_user_func_array(array($this -> _behavior, $name), $parameters);

         $this -> core -> log(' [  ' . $this -> sClassName . "->" . $name . "()  ]" , $aResult);

         return $aResult;
        

    }

    function cleanParams($aParams) {

        $aNewParams = array();

        if (is_array($aParams) || is_object($aParams)) {

            foreach ($aParams as $Nkey => $Nvalue) {

                if (is_object($Nvalue) && (strpos('debug',get_class($Nvalue))!== false)) {
                    $Nvalue = $Nvalue -> _behavior;
                    $Nkey = get_class($Nvalue);

                }

                if ($Nkey === 'core' || $Nkey === 'application') {
                    continue;
                }

                if (is_string($Nvalue)) {
                    $oNewValue = $Nvalue;

                } elseif (is_numeric($Nvalue)) {

                    $oNewValue = (string)$Nvalue;
                } elseif (is_bool($Nvalue)) {

                    $oNewValue = $Nvalue;
                } elseif (is_array($Nvalue)) {
                    $oNewValue = array();
                    foreach ($Nvalue as $key => $value) {

                        if ($key != 'core' || $key != 'application') {

                            if (count($value) > 1) {
                                unset($value);

                            } else {

                                $oNewValue[$key] = $value;

                            }
                        }

                    }

                } elseif (is_object($Nvalue)) {

                    $oNewValue = clone $Nvalue;

                    if (isset($oNewValue -> core)) {
                        unset($oNewValue -> core);
                    }

                    if (isset($oNewValue -> application)) {
                        unset($oNewValue -> application);
                    }

                    foreach ($oNewValue as $newinvalue) {
                        if (count($newinvalue) > 1) {
                            unset($newinvalue);
                        }
                    }

                } else {

                    $oNewValue = $Nvalue;

                }

                $aNewParams[] = $oNewValue;

            }

            return $aNewParams;
        } else {

            return $aParams;

        }

    }

}
