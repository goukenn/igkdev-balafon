<?php
// @author: C.A.D. BONDJE DOUE
// @file: WordContentValidator.php
// @date: 20230126 22:30:18
namespace IGK\System\Security\Web;

use IGK\System\Regex\Replacement;

///<summary></summary>
/**
* remove all unecessay character to make a word sentence
* @package IGK\System\Security\Web
*/
class WordContentValidator extends MapContentValidatorBase{
    var $allowNull;
    public function map($value, $key, &$error)
    { 
        if (!is_string($value)){
            if ($this->allowNull){
                return null;
            }
            $error[$key] = 'not allowed value';
            return;
        }
        $rp = new Replacement;
        $rp->add('/[^0-9a-z\. ]/i', ' ');
        $rp->add('/\s+/i', ' ');
        return trim($rp->replace($value));
    }
}