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

/**
* auto generate doc.
* @package IGK\Actions
*/
class ActionRequestValidator extends RequestValidatorBase implements IActionRequestValidator
{
    /**
    * Property: action.
    * @var mixed
    */
    var $action;
    /**
    * .ctr
    * @param mixed $action
    */
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
    * @param mixed & $requestData
    * @param ?array & $error
    * @param mixed $requestData returned data
    * @throws IGKException
    * @return bool
    */
    public function validate($data, array $mapper, ?array $defaultValues=null, ?array $not_required=null, 
     & $requestData = null,  ?array &$error = null,
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
        $requestData = new DataArgs($r);
        return true;
    }
}