<?php
/**
 * Square-Framework
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Square-Framework
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://Square-Framework.com/user_guide/license.html
 * @link		http://Square-Framework.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		Square-Framework
 * @subpackage	Libraries
 * @category	Input
 * @author		ExpressionEngine Dev Team
 * @link		http://Square-Framework.com/user_guide/libraries/input.html
 */
class request extends component {

    public $aParam;
    
    public function extractParam($config,$data){

      $aParam = array();


               // vérification des paramètres
      foreach ($config as $x => $options) {

       $value=null;

       switch($options['type']) {

        case 'date' :
        {

            $value = $this -> getDate($x, $data, $options);

        }
        break;

        case 'localisation' :
        {

            $value = $this -> getLocalisation($x, $data, $options);

        }
        break;

        case 'integer' :
        {

            $value = $this -> getInt($x, $data, $options);


        }
        break;

        case 'timestamp' :
        {

            $value = $this -> getTimestamp($x, $data, $options);


        }
        break;

        case 'boolean' :
        {


          $value = $this -> getBoolean($x, $data, $options,$value);


      }
      break;

      case 'string' :
      {

        $value = $this -> getString($x, $data, $options);


    }
    break;

    case 'raw' :
    {

        $value = $this -> getRaw($x, $data, $options);


    }
    break;

    case 'array' :
    {

        $value = $this -> getArray($x, $data, $options);

    }
    break;

    case 'limit' :
    {

        if (isset($aParam[$x]) && $aParam[$x] == 'max') {
            if (isset($options["max"])) {
                $aParam[$x] = $options["max"];
            }
        }

        $value = $this -> getLimit($x, $data, $options);

    }
    break;

    case 'start' :
    {

        if (isset($aParam[$x]) && $aParam[$x] == 'min') {
            if (isset($options["min"])) {
                $aParam[$x] = $options["min"];
            }
        }

        $value = $this -> getStart($x, $data, $options);

    }
    break;


    case 'file' :
    {
        $value = $this -> getFile($x, $data, $options);

    }
    break;

    case 'dir' :
    {

        $value = $this -> getDir($x, $data, $options);

    }
    break;



    case 'email' :
    {

        $value = $this -> getRegexp($x, '/^([a-zA-Z0-9_.-+])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z]{2,4})$/',$data,  $options);

    }
    break;

    case 'territory' :
    {

        $value = $this -> getLocalisation($x, $data, $options);

    }
    break;

    default :
    {

        throw new Exception("Missing Type " . $options['type'], 400);

    }
    break;

}


if(is_null($value)){

 if (isset($options["allowBlank"]) && $options["allowBlank"] == false) {

    throw new Exception("$x can't be blank", 404);

}

}else{


    $aParam[$x] = $value;

    if( isset($options["copy"])){
        $aParam[$options["copy"]] = $value;

    }




}



}



return $aParam;
}


public function getFakeParam($config,$data){

  $aParam = array();


               // vérification des paramètres
  foreach ($config as $x => $options) {

     $value=null;


     if(isset($data[$x])){
        $value = $data[$x];
    }elseif(isset($options["default"])){
        $value = $options["default"];

    }else{

        switch($options['type']) {

            case 'date' :$value = date('j/n/Y');;

            case 'localisation' : $value = $this -> getLocalisation($x, $data, $options);break;

            case 'integer' :$value = rand(1, 20);break;

            case 'boolean' :$value = true;break;

            case 'string' :$value = "aaaaaa";break;

            case 'array' : $value= array('aaaa','bbbb','cccc');break;

            case 'limit' :$value = rand(1, 20); break;

            case 'start' :$value = rand(1, 20);break;                 

            case 'email' :$value = "steed.monteiro@gmail.com"; break;

            default :
            {

                throw new Exception("Missing Type " . $options['type'], 400);

            }
            break;

        }

    }


    if(is_null($value)){

     if (isset($options["allowBlank"]) && $options["allowBlank"] == false) {

        throw new Exception("$x can't be blank", 404);

    }

}else{


    $aParam[$x] = $value;

}
}

return $aParam;
}




    /**
     * Vérification des paramètres d'entrées reçu à partir d'une variable de config
     * @param object $config
     * @return
     */
    public function getParam($aConfig,$aData) {


        $this->param = $this->extractParam($aConfig,$aData);
        
        return $this->param;
    }

    public function getInc($data, $options = array()) {

        if (isset($data["inc"])) {
            $data = $data["inc"];
            $inc = array();

            if (is_string($data) && !empty($data)) {

                $inc = explode('+', $data);

            }

            return $inc;


                /*if (isset($options['attributes']) && is_array($options['attributes'])) {

                    foreach ($inc as $value) {

                        if (!in_array($value, $options['attributes'])) {

                            unset($value);
                        }
                    }
                }*/


            } else if (isset($options["default"])) {

                if (is_string($options["default"])) {

                    $data["inc"] = $options["default"];
                    return $this -> getInc($data,$options);

                } else if (is_array($options["default"])) {

                    $data["inc"] = $options["default"];

                    return $data["inc"];

                }
            }

            return null;

        }

        public function getStart($x, $data, $options = array()) {

            $value = $this -> getInt($x, $data, $options);

            if($value ==null){
                return $this -> getInt('start', $data, $options);
            }

            return $value;

        }

        public function getLimit($x, $data, $options = array()) {

            $value = $this -> getInt($x, $data, $options);

            if($value ==null){
                return $this -> getInt('limit', $data, $options);
            }

            return $value;


        }

        public function getInt($x, $data, $options = array()) {

            if (isset($data[$x]) && is_numeric($data[$x])) {
                $value = intval($data[$x]);


                if (isset($options["min"]) && $data < $options["min"]) {
                    $value = $options["min"];
                    throw new Exception("Param $x should be greater than " . $options["min"] . " in ", 0, "Invalid param $x");
                }

                if (isset($options["max"]) && $data > $options["max"]) {
                    $value = $options["max"];
                    throw new Exception("Param $x should be less than " . $options["max"] . " in ", 0, "Invalid param $x");

                }

                return  $value;


            } else if (isset($options["default"])) {
                return  $options["default"];
            }

            return null;
        }

        public function getTimestamp($x, $data, $options = array()) {


            if (isset($data[$x]) && is_numeric($data[$x])) {
                $value = intval($data[$x]);

                return  $value;


            } else if (isset($options["default"])) {

                if($options["default"] ==true ){
                    return  time();

                }
                return  $options["default"];
            }

            return null;
        }


        

        public function getDate($x, $data, $options = array()) {
            return $this -> getString($x, $data, $options);
        }

        public function getFile($x, $data, $options = array()) {
          if (isset($_FILES[$x])) {
            if($_FILES[$x]['size']>0){
                return $_FILES[$x];
            }
        }
        

        if (isset($data[$x]) && !empty($data[$x] )) {

         if (file_exists($data[$x])) {
            return $data[$x];
        }

        return $data[$x];
    }

    return null;

}

public function getLocalisation($x, $data, $options = array()) {
    if (isset($data[$x]) && ($data[$x] !== "")) {

        return $data[$x];
    }
    require_once "libs/Blues/GeoIP.php";
    list($ip, $countryCode) = GeoIP::countryCode();
    return  $countryCode;

}

public function getDir($x, $data, $options = array()) {
    if (isset($data[$x]) &&  ($data[$x] == 'DESC' || $data[$x] == 'ASC') ) {
        return $data[$x];
    } else if (isset($options["default"])) {
        return  $options["default"];
    }

    return null;
}

public function getBoolean($x, $data, $options = array()) {



    if (isset($data[$x]) && ($data[$x] !== "")) {


        $value = $data[$x];

        if ($value === false || $value === 0 || $value === "false"|| $value === "off") {

            return false;
        }

        if ($value === true || $value === 1 || $value === "true"|| $value === "on") {

            return true;
        }

    } else if (isset($options["default"])) {
        return  $options["default"];
    }


    return null;
}

public function getString($x, $data, $options = array()) {

    if (isset($data[$x]) && ($data[$x] !== "") && is_string($data[$x]) ) {
        $value = htmlspecialchars($data[$x],ENT_COMPAT,'UTF-8');

        if (is_string($value)) {

            if (isset($options['attributes']) && is_array($options['attributes'])) {

                if (!in_array($value, $options['attributes'])) {
                    return null;
                }

            }
            return $value;

        }

    } else if (isset($options["default"])) {

        return  $options["default"];
    }

    return null;
}


public function getRaw($x, $data, $options = array()) {

    if (isset($data[$x])) {
        $value = $data[$x];

        if (is_string($value)) {


            return $value;

        }

    } else if (isset($options["default"])) {

        return  $options["default"];
    }

    return null;
}


    /**
     *
     * @return an array
     * @param $x String Name of the attribute
     * @param $options Object[optional]
     * - regexpMember:
     * - default:
     */
    public function getArray($x, $data, $options = array()) {


        if (isset($data[$x]) && ($data[$x] !== "") && is_array($data[$x])) {

            return $data[$x];

        } else if (isset($options["default"])) {

            return  is_array($options["default"]) ? $options["default"] : null;
        }

        return null;
    }

    public function getRegexp($x, $regexp, $data, $options = array()) {
        if (isset($data[$x]) && preg_match($regexp, $data[$x])) {
            return $data[$x];
            
        } else if (isset($options["default"])) {
            return  $options["default"];
        }

        return null;
    }

    

}

// END Input class

/* End of file Input.php */
/* Location: ./system/libraries/Input.php */
