<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionRequestValidator.php
// @date: 20230126 19:34:34
namespace IGK\Actions;

use IGK\Helper\Activator;
use IGK\System\DataArgs;
use IGK\System\Http\Request;
use IGK\System\Security\Web\RequestValiationMapper;
use IGK\System\WinUI\Forms\RequestValidatorBase;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\Actions
 */
class ActionRequestValidator extends RequestValidatorBase implements IActionRequestValidator
{
    var $action;
    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * validate data
     * @param mixed $data 
     * @param array $mapper mappgin data with validation security if require
     * @param null|array $defaultValues default values for each data 
     * @param null|array $not_required //list of not required field
     * @param mixed $requestData returned data
     * @param null|array $error list of errors
     * @param null|array $resolvKeys resolved keys
     * @return bool 
     * @throws IGKException 
     */
    public function validate($data, array $mapper, ?array $defaultValues=null, ?array $not_required=null, &$requestData = null,  ?array &$error = null,
     ?array $resolvKeys=null): bool    
    {   
        $r = (new RequestValiationMapper(
            $mapper,
            $defaultValues,
            $not_required
        ))->validate($data)->map();
        if (isset($r['__validatation_error__'])) {
            $error = $r['__validatation_error__'];
            return false;
        }
        // update action request data 
        $requestData = new DataArgs($r);
        return true;
    }
}
