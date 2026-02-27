<?php
// @author: C.A.D. BONDJE DOUE
// @file: ObjectContentValidator.php
// @date: 20230418 11:49:02
namespace IGK\System\Security\Web;

/**
* auto generate doc.
* @package IGK\System\Security\Web
*/
class ObjectContentValidator  extends MapContentValidatorBase{

    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected function validate(&$value, $key): bool {
        return true;
    }
}