<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbRegisterProfileTrait.php
// @date: 20260621 18:03:05
namespace IGK\System\Database\Traits;

use IGK\Helper\Authorization;

/**
* 
* @package IGK\System\Database\Traits
* @author C.A.D. BONDJE DOUE
*/
trait DbRegisterProfileTrait{
    /**
     * 
     * @return mixed 
     */
    protected function registerProfile()
    {
        /**
         * @var mixed $q
         */
        $q = $this;
        $model = $q->model();
        $ctrl = $q->getController();
        if ($model->notRegisterToAProfile($ctrl) && ($profile = self::GetDefaultProfile())){            
            Authorization::BindUserToGroup($ctrl, $model, $profile); 
        }
    }

    /**
     * get default namespace profile
     * @return ?string
     */
    static function GetDefaultProfile(){
                
        if ($ns = igk_get_class_namespace(static::class)){
            $ns .='\\';
        }
        $cl = $ns."Profiles";
        if (class_exists($cl) && method_exists($cl, $fc = 'getDefaultProfile')){
            return call_user_func([$cl, $fc],[]);
        }
        return null;
    }
}