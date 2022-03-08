<?php
namespace IGK\System\Html\Forms;

use IGKValidator;
use function igk_resources_gets as __;

class UrlValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        $q = parse_url($value);       
        if (!IGKValidator::IsUri($value))
        {
            if ($default  && IGKValidator::IsUri($default)){
                return $default;
            }
            if ($fieldinfo->required)
                $error[] = $fieldinfo->error ?? __("url not valid: {0}", $fieldinfo->name);
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