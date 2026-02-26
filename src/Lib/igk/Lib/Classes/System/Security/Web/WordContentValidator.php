<?php
// @author: C.A.D. BONDJE DOUE
// @file: WordContentValidator.php
// @date: 20230126 22:30:18
namespace IGK\System\Security\Web;
use IGK\System\Regex\Replacement;
/**
* remove all unecessay character to make a word sentence
* @package IGK\System\Security\Web
*/
class WordContentValidator extends MapContentValidatorBase{

    /**
    * Property: allow null.
    * @var mixed
    */
    var $allowNull;

    /**
    * Map.
    * @param mixed $value
    * @param mixed $key
    * @param mixed & $error
    * @param bool $missing
    * @param bool $required
    */
    public function map($value, $key, &$error, bool $missing=false, bool $required = true)
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