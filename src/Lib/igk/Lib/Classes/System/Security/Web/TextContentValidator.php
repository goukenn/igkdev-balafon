<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextContentValidator.php
// @date: 20230303 21:32:26
namespace IGK\System\Security\Web;


///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class TextContentValidator extends MapContentValidatorBase{

    protected function validate(& $value, $key):bool{  
        if ($value){
            // remove transform to html content
            $value = htmlentities($value); 
        }
        return true; 
    }

}