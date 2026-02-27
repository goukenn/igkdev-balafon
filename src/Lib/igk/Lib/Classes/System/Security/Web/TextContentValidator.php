<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextContentValidator.php
// @date: 20230303 21:32:26
namespace IGK\System\Security\Web;
/**
* 
* @package IGK\System\Security\Web
*/

/**
* auto generate doc.
* @package IGK\System\Security\Web
*/
class TextContentValidator extends MapContentValidatorBase{

    /**
    * auto generate doc.
    * @param mixed $key
    * @return bool
    */
    protected function validate(& $value, $key):bool{  
        if ($value){ 
            $value = htmlentities($value); 
        }
        return true; 
    }
}