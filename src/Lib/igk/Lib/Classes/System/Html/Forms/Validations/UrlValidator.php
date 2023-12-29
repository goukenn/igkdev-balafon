<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UrlValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\IFormFieldOptions;
use IGKValidator;
use function igk_resources_gets as __;

/**
 * validator for url
 * @package IGK\System\Html\Forms
 */
class UrlValidator extends FormFieldValidatorBase implements IFormValidator{

   

    public function assertValidate($value): bool { 
        return false;
    }

    public function validate($value, $default=null, ?IFormFieldOptions $fieldinfo=null, & $error=[]){ 
        $q = parse_url($value);       
        if (!IGKValidator::IsUri($value))
        {
            if ($default  && IGKValidator::IsUri($default)){
                return $default;
            }
            $is_required = $fieldinfo ? $fieldinfo->required : false;
            $name = $fieldinfo ? $fieldinfo->name : false;
            $v_error = ($fieldinfo ? $fieldinfo->error : null) ?? __("url not valid: {0}", $name);
            if ($is_required)
                $error[] = $v_error;  
            return null;           
        }
        if (isset($q["query"])){
            parse_str($q["query"], $tab);
            array_map(function($a, $b)use(& $tab){
                // $tab[$b] = htmlentities($a);
                $tab[$b] = urldecode($a);
            }, $tab, array_keys($tab));
            
            $value = explode("?", $value)[0]."?".http_build_query($tab);
        }
        return $value;
    }

}