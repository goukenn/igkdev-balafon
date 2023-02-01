<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionRequestValidator.php
// @date: 20230126 19:34:34
namespace IGK\Actions;

use IGK\System\DataArgs;
use IGK\System\Security\Web\RequestValiationMapper;

///<summary></summary>
/**
* 
* @package IGK\Actions
*/
class ActionRequestValidator implements IActionRequestValidator{
    var $action;
    public function __construct($action){
        $this->action = $action;
    }
    public function validate($data, array $mapper, & $requestData=null,  ?array & $error = null): bool { 
        // igk_wln_e($mapper);
        $r = (new RequestValiationMapper($mapper
        ))->validate($data)->map();        
        if (isset($r['__validatation_error__'])){
            $error = $r['__validatation_error__'];
            return false;
        }
        // update action request data
        $this->action->requestData = $r;
        $requestData = new DataArgs($r);
        return true;
    }

}