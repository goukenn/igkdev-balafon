<?php
// @author: C.A.D. BONDJE DOUE
// @file: NumberContentValidator.php
// @date: 20230125 13:49:42
namespace IGK\System\Security\Web;


///<summary></summary>
/**
 * number content validator 
 * @package IGK\System\Security\Web
 */
class NumberContentValidator extends MapContentValidatorBase
{
    protected $notvalid_msg = 'not a valid number.';
    var $missingDefaultValue = null;
    var $defaultValue = 0;
   
    protected function validate(& $value, $key):bool{ 
        if ($r = is_numeric($value)){
            $value =  floatval($value);
        }
        return $r;
    }
}
