<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotifyHelper.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Helper;

use IGKException;
use Exception;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

class NotifyHelper{

    /**
     * 
     * @param string $name notification name
     * @param bool|IResponse $condition bool o handle 
     * @param string $success success message 
     * @param string $error error message 
     * @return mixed condition passed
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Notify(string $name, $condition, $success, $error){
        if ($condition){
            if (igk_is_ajx_demand()){
                igk_ajx_toast($success, 'igk-success');
            }else {
                igk_notifyctrl($name)->addSuccess($success);
            }
        } else {
            if (igk_is_ajx_demand()){
                igk_ajx_toast($error, "danger");
            }
            else 
                igk_notifyctrl($name)->addDanger($error);
        }
        return $condition;
    }
}