<?php
/**
 * Yii bootstrap file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @version $Id$
 * @package system
 * @since 1.0
 */

define('YII_PATH', ROOT_PATH.'lib/yii/framework/'); 

define('YII_DEBUG',false);
define('YII_ENABLE_EXCEPTION_HANDLER',false);
define('YII_ENABLE_ERROR_HANDLER',false);

require(YII_PATH.'YiiBase.php');

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It encapsulates {@link YiiBase} which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of YiiBase.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system
 * @since 1.0
 */


class yii extends YiiBase
{

   public static $_logger;    
   function __construct(&$parent)
   {

      $__YII =$this;   
      $this->core = $parent;

  }

        /**
     * Writes a trace message.
     * This method will only log a message when the application is in debug mode.
     * @param string $msg message to be logged
     * @param string $category category of the message
     * @see log
     */
        public static function trace($msg,$category='application'){
           getCore()->log($msg,$category) ;
       }

    /**
     * Logs a message.
     * Messages logged by this method may be retrieved via {@link CLogger::getLogs}
     * and may be recorded in different media, such as file, email, database, using
     * {@link CLogRouter}.
     * @param string $msg message to be logged
     * @param string $level level of the message (e.g. 'trace', 'warning', 'error'). It is case-insensitive.
     * @param string $category category of the message (e.g. 'system.web'). It is case-insensitive.
     */
    public static function log($msg,$level=CLogger::LEVEL_INFO,$category='application')
    {

        getCore()->log($msg,$level,$category) ;
        
    }

    /**
     * Marks the begin of a code block for profiling.
     * This has to be matched with a call to {@link endProfile()} with the same token.
     * The begin- and end- calls must also be properly nested, e.g.,
     * <pre>
     * Yii::beginProfile('block1');
     * Yii::beginProfile('block2');
     * Yii::endProfile('block2');
     * Yii::endProfile('block1');
     * </pre>
     * The following sequence is not valid:
     * <pre>
     * Yii::beginProfile('block1');
     * Yii::beginProfile('block2');
     * Yii::endProfile('block1');
     * Yii::endProfile('block2');
     * </pre>
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see endProfile
     */
    public static function beginProfile($token,$category='application')
    {

     getCore()->log('begin:'.$token,CLogger::LEVEL_PROFILE,$category) ;


 }

    /**
     * Marks the end of a code block for profiling.
     * This has to be matched with a previous call to {@link beginProfile()} with the same token.
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see beginProfile
     */
    public static function endProfile($token,$category='application')
    {
        getCore()->log('end:'.$token,CLogger::LEVEL_PROFILE,$category) ;
        
        
    }

    /**
     * @return CLogger message logger
     */
    public static function getLogger()
    {
        if(self::$_logger!==null)
            return self::$_logger;
        else
            return self::$_logger=new CLogger;
    }

    /**
     * Sets the logger object.
     * @param CLogger $logger the logger object.
     * @since 1.1.8
     */
    public static function setLogger($logger)
    {
        self::$_logger=$logger;
    }
    
    
    /**
     * Sets the logger object.
     * @param CLogger $logger the logger object.
     * @since 1.1.8
     */
    public static function app()
    {

      return getCore()->framework('yii');
      
  }


 /*   public static function registerAutoloader($callback, $append=true)
    {
        if($append)
        {
            self::$enableIncludePath=false;
            spl_autoload_register($callback);
        }
        else
        {
            spl_autoload_unregister(array('YiiBase','autoload'));
            spl_autoload_register($callback);
            spl_autoload_register(array('YiiBase','autoload'));
        }
    }
  
  */
    /**
     * Sets the logger object.
     * @param CLogger $logger the logger object.
     * @since 1.1.8
     */
    public static function getId()
    {
       return 1;
   }


}


