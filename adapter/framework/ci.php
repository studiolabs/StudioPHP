<?php
global $CFG, $EXT, $BM, $UNI, $URI, $OUT, $RTR, $SEC, $IN, $LANG, $CI;




/**
 * CodeIgniter Version
 *
 * @var string
 *
 */
    define('CI_VERSION', '2.1.4');

/**
 * CodeIgniter Branch (Core = TRUE, Reactor = FALSE)
 *
 * @var boolean
 *
 */
    define('CI_CORE', FALSE);
/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
$application_folder = 'application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';

// The controller class file name.  Example:  Mycontroller
// $routing['controller'] = '';

// The controller function you wish to be called.
// $routing['function']	= '';

/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder

define('BASEPATH', ROOT_PATH . 'lib/CodeIgniter/');

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', ROOT_PATH . 'lib/CodeIgniter/' . $system_path . '/');

// The path to the "application" folder
define('APPPATH', ROOT_PATH . 'lib/CodeIgniter/' . $application_folder . '/');

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
 
function log_message() {
    getCore() -> log(func_get_args());
}

require_once BASEPATH . 'Loader.php';

class ci extends CI_Loader {


    /**
     * Constructor
     *
     * Sets the path to the view files and gets the initial output buffering level
     *
     * @access	public
     */
    function __construct(&$parent) {

        $this -> _parent = $parent;
        $this -> _ci_is_php5 = TRUE;
        $this -> _ci_view_path = $this -> core -> config -> sApplicationPath.'views/';
        $this -> _ci_ob_level = ob_get_level();
        $this -> _classes = array();
        $this -> _is_loaded = array();
        $this -> _config_item = array();

        $GLOBALS['CI'] = $this;            
        log_message('debug', "Loader Class CI Initialized");

    }

    /**
     * Determines whether we should use the CI instance or $this
     *
     * @access	private
     * @return	bool
     */
    function _ci_is_instance() {
        return TRUE;
    }

    function & load_class($class, $directory = 'libraries', $prefix = 'CI_', $option = array()) {

        // Does the class exist?  If so, we're done...
        if (isset($this -> _classes[$class])) {
            return $this -> _classes[$class];
        }

        $name = FALSE;

        // Look for the class first in the local application/libraries folder
        // then in the native system/libraries folder

        if (file_exists(SYSDIR . $directory . '/' . $class . '.php')) {
            $name = $prefix . $class;

            if (class_exists($name) === FALSE) {
                require (SYSDIR . $directory . '/' . $class . '.php');
            }
        }

        $subclass_prefix = $this -> config_item('subclass_prefix');

        // Is the request a class extension?  If so we load it too
        if (file_exists(SYSDIR . $directory . '/' . $subclass_prefix . $class . '.php')) {
            //&& $subclass != ''
            $name = $subclass_prefix . $class;

            if (class_exists($name) === FALSE) {
                require (SYSDIR . $directory . '/' . $subclass_prefix . $class . '.php');
            }
        }

        // Did we find the class?
        if ($name === FALSE) {
            // Note: We use exit() rather then show_error() in order to avoid a
            // self-referencing loop with the Excptions class
            exit('Unable to locate the specified class: ' . $class . '.php');
        }

        // Keep track of what we just loaded
        $this -> is_loaded($class);

        $newClass = new $name($option);

        //special case

        switch ($class) {
            case 'Benchmark' :
                $GLOBALS['BM'] = &$newClass;
                $GLOBALS['BM'] -> mark('total_execution_time_start');
                $GLOBALS['BM'] -> mark('loading_time:_base_classes_start');

                break;

            case 'Config' :

                $GLOBALS['CFG'] = &$newClass;

                break;

            case 'Utf8' :
                $GLOBALS['UNI'] = &$newClass;

                break;

            case 'URI' :

                $GLOBALS['URI'] = &$newClass;

                break;

            case 'Router' :
                $GLOBALS['RTR'] = &$newClass;
                $GLOBALS['RTR'] -> _set_routing();

                break;

            case 'Output' :
                $GLOBALS['OUT'] = &$newClass;

                break;

            case 'Lang' :

                $GLOBALS['LANG'] = &$newClass;

                break;

            case 'Input' :

                $GLOBALS['IN'] = &$newClass;

                break;

            case 'Security' :

                $GLOBALS['SEC'] = &$newClass;

                break;
        }

        $this -> _classes[$class] = $newClass;

        return $this -> _classes[$class];
    }

    function is_loaded($class = '') {

        if ($class != '') {
            $this -> _is_loaded[strtolower($class)] = $class;
        }

        return $this -> _is_loaded;
    }

    function & get_config($replace = array()) {

        if (isset($this -> _config)) {
            return $this -> _config;
        }

        $file_path = APPPATH . 'config/config.php';

        // Fetch the config file
        if (!file_exists($file_path)) {
            exit('The configuration file does not exist.');
        }

        require ($file_path);

        // Does the $config array exist in the file?
        if (!isset($config) OR !is_array($config)) {
            exit('Your config file does not appear to be formatted correctly.');
        }

        // Are any values being dynamically replaced?
        if (count($replace) > 0) {
            foreach ($replace as $key => $val) {
                if (isset($config[$key])) {
                    $config[$key] = $val;
                }
            }
        }

        $this -> _config = &$config;

        return $this -> _config;
    }

    function config_item($item) {

        if (!isset($this -> _config_item[$item])) {
            $config = get_config();

            if (!isset($config[$item])) {
                return FALSE;
            }
            $this -> _config_item[$item] = $config[$item];

        }

        return $this -> _config_item[$item];
    }

    /**
     * Loads the main config.php file
     *
     * @access	private
     * @return	array
     */
    function & get_config_CI() {

        if (!isset($this -> main_conf)) {

            if (!file_exists(APPPATH . 'config/config' . EXT)) {
                exit('The configuration file config' . EXT . ' does not exist.');
            }

            require (APPPATH . 'config/config' . EXT);

            if (!isset($config) OR !is_array($config)) {
                exit('Your config file does not appear to be formatted correctly.');
            }

            $this -> main_conf = &$config;
        }
        return $this -> main_conf;
    }

}


/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require(BASEPATH.'Common.php');


class fakeConfig {

    public function item($name) {
        switch ($name) {
            case 'charset' :
                return 'UTF-8';

                break;

            case 'compress_output' :
                return TRUE;

                break;

            default :
                return null;

                break;
        }
    }

}

$CFG = new fakeConfig();

class fakeBenchmark {

    public function elapsed_time($point1 = '', $point2 = '', $decimals = 4) {

        return null;
    }

}

$BM = new fakeBenchmark();

// END SF_Config class

/* End of file Config.php */
/* Location: ./system/libraries/Config.php */
