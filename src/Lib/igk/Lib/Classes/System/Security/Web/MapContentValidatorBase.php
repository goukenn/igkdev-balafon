<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapContentValidatorBase.php
// @date: 20230125 13:19:41
namespace IGK\System\Security\Web;

use IGK\System\IO\Path;
use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
abstract class MapContentValidatorBase
{
    private static $sm_validators;
    public final function __invoke($value, $key, &$error)
    {
        return $this->map($value, $key, $error);
    }
    /**
     * map value 
     * @param mixed $value value to validate
     * @param mixed $key key of the value
     * @param mixed $error error list 
     * @return mixed 
     */
    public abstract function map($value, $key, &$error);

    /**
     * 
     * @return static 
     */
    public static function Get(string $t)
    {
        $cl = igk_str_ns( Path::Combine(__NAMESPACE__, sprintf('%sContentValidator', $t)));
     
        if (!isset(self::$sm_validators[$cl])) {
            if (!is_subclass_of($cl, self::class)){
                igk_die(sprintf(__("%s class not an subclass of %s "),$cl, self::class));
            }
            $g = new $cl();
            self::$sm_validators[$cl] = $g;
            return $g;
        }
        return self::$sm_validators[$cl];
    }
    /**
     * create a new instance of the validator
     * @return object 
     */
    public function createNewInstance(){
        $cl = static::class;
        return new $cl();
    }
}