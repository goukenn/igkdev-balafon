<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackageValidator.php
// @date: 20230330 12:25:28
namespace IGK\System\Npm;

use IGK\System\Http\Request;
use IGK\System\Npm\Traits\JsonPackagePropertyTrait;
use IGK\System\WinUI\Forms\FormData;

///<summary></summary>
/**
* 
* @package IGK\System\Npm
*/
class JsonPackageValidator extends FormData{
    use JsonPackagePropertyTrait;


    protected function getContentSecureFormRequest(Request $request): ?array
    {
      return $this->getContentSecure();
    }
    /**
     * 
     * @return null|array if assoc and validation : return treated value
     */
    protected function getContentSecure():?array{

        return [
            "author"=>function($n, $key, & $error){
                if (is_object($n))
                    return JsonPackageAuthorInfoValidator::ValidateData($n, null, $error);
                return false;
            },
            "scripts"=>function($n){
                if (is_object($n))
                    return $n;
                return false;
            },
            "dependencies"=>function($n){
                if (is_object($n))
                    return $n;
                return false;
            },
            "devDependencies"=>function($n){
                if (is_object($n))
                    return $n;
                return null;
            }
        ];
    }
    protected function getDataValidatorMapper(?array $tab = null)
    {
        $from_mapper = parent::getDataValidatorMapper($tab);
        $from_mapper->mapper =  $this->mergeSecure( $from_mapper->mapper ,$this->getContentSecure() ?? []);  
        return $from_mapper;
    }
    /**
     * return list of not required field or regex data
     * @return null|array 
     */
    function getNotRequired(): ?array
    {
        $rf = [function($a){          
            return !in_array($a, explode('|', 'name|description|main|keywords|author|license|devDependencies'));
        }]; 
        return $rf; 
    }
}