<?php
// @author: C.A.D. BONDJE DOUE
// @file: ObjectFormDataTrait.php
// @date: 20230414 16:21:02
namespace IGK\System\Traits;

use IGK\System\Http\Request;

///<summary></summary>
/**
* 
* @package IGK\System\Traits
*/
trait ObjectFormDataTrait{
    protected function getContentSecureFormRequest(Request $request): ?array
    {
      return $this->getContentSecure();
    }
    protected function getDataValidatorMapper(?array $tab = null)
    {
        $from_mapper = parent::getDataValidatorMapper($tab);
        $from_mapper->mapper =  $this->mergeSecure( $from_mapper->mapper ,$this->getContentSecure() ?? []);  
        return $from_mapper;
    }
        /**
     * 
     * @return null|array if assoc and validation : return treated value
     */
    protected abstract function getContentSecure():?array;
}