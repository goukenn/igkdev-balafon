<?php
// @author: C.A.D. BONDJE DOUE
// @file: ObjectContentValidator.php
// @date: 20230418 11:49:02
namespace IGK\System\Security\Web;
/**
* 
* @package IGK\System\Security\Web
*/
class ObjectContentValidator  extends MapContentValidatorBase{

    /**
    * auto generate doc.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected function validate(&$value, $key): bool {
        return true;
    }
}