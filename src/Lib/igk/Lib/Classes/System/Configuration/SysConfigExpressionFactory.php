<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysConfigExpressionFactory.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;
use IGK\System\Html\XML\XmlConfigurationNode;

/**
 * configuration expression class factory
 * @package IGK\System\Configuration
 */
class SysConfigExpressionFactory{
    /**
    * Constant: key.
    * @var mixed
    */
    const KEY = self::class."/factories";
    /**
     * register prefix
     * @param string $prefix prefix name. not sys|app
     * @param string $class class expression to bind
     * @return void 
     */
    public static function Register(string $prefix, string $class){
        igk_environment()->setArray(self::KEY, $prefix, $class);
    }
    /**
     * unregister prefix
     * @param mixed $prefix 
     * @return void 
     */
    public static function UnRegister(string $prefix){
        igk_environment()->unsetInArray(self::KEY, $prefix);
    }
    /**
     * get registration configuration definition 
     * @return string 
     */
    public static function GetRegisterRegex(): string{
        $s = [XmlConfigurationNode::SYS_CONFIG, XmlConfigurationNode::APP_CONFIG];
        if (is_array($t = igk_environment()->get(self::KEY))){
            $s = array_unique(array_merge($s, array_keys($t)));
        }
        return implode("|",$s);
    }
    /**
    * auto generate doc.
    * @param mixed $expression
    * @return object|null
    */
    public static function Create($name, $expression){
        if ($g = igk_environment()->getArray(self::KEY, $name)){
            if (strpos($expression, $name.".") === 0){
                $expression = substr($expression, strlen($name)+1);
            }
            if (class_exists($g) && !empty($expression)){
                return new $g($expression);
            }
        }
    }
}