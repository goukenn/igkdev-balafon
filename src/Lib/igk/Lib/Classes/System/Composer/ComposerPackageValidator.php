<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComposerPackageValidator.php
// @date: 20230414 16:08:43
namespace IGK\System\Composer;

use IGK\Helper\StringUtility;
use IGK\System\Composer\Traits\ComposerPackageFileTrait;
use IGK\System\Regex\Replacement;
use IGK\System\Traits\ObjectFormDataTrait;
use IGK\System\WinUI\Forms\FormData;

///<summary></summary>
/**
* validate json data
* @package IGK\System\Composer
*/
class ComposerPackageValidator extends FormData{
    use ComposerPackageFileTrait;
    use ObjectFormDataTrait;
    protected function getDataValidatorMapper(?array $tab = null)
    {
        $from_mapper = parent::getDataValidatorMapper($tab);
        $replace = new Replacement;
        $replace->add("/_+/","-");
        foreach($from_mapper->mapper as $key){
            if (preg_match('/[A-Z]/', $key)){
                $from_mapper->mapper[] = $replace->replace(strtolower(StringUtility::GetSnakeKebab($key)));
            }
        }
        $from_mapper->mapper =  $this->mergeSecure( $from_mapper->mapper ,$this->getContentSecure() ?? []);  
        $from_mapper->resolvKeys = ['require-dev'=>'requireDev'];
        return $from_mapper;
    }
    protected function getContentSecure(): ?array {
        return [
            'authors'=>function($n, $key, &$errors, $missing, $required){
                if (is_array($n)){
                    return $n;
                }
                if ($missing && $required){
                    $errors = 'missing authors';
                }
                return false;
            },
            'requireDev'=>[$this, 'getRequireDev'] 
        ];
    }
    public function getRequireDev($n, $key, &$errors, $missing, $required){
        if (is_object($n)){
            return $n;
        }
    }
    
    /**
     * expression to check that 
     * @return null|array 
     */
    function getNotRequired(): ?array
    {        
        return [function($a){
            
            return true;
        }]; 
    }
    
}