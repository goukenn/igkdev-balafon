<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestValidatorBase.php
// @date: 20230303 23:02:59
namespace IGK\System\WinUI\Forms;

use IGK\Actions\IActionRequestValidator;
use IGK\Helper\Activator;
use IGK\System\Data\ObjectDataValidator;
use IGK\System\DataArgs;
use IGK\System\Http\Request;
use IGK\System\Security\Web\RequestValiationMapper;

///<summary></summary>
/**
* request data validator
* @package IGK\System\WinUI\Forms
*/
abstract class RequestValidatorBase extends ObjectDataValidator implements IActionRequestValidator{
    public function validateJSon(Request $request, string $formdata_class, ?array &$error = null)
    {
        if ($data = $request->getJsonData()) {
            $rs = Activator::CreateNewInstance($formdata_class, $data);
            $validation = $rs->getValidationMapperFromRequest($request); 
            // igk_wln_e(__FILE__.":".__LINE__ , "faile ... ", $validation)      ;
            if ($this->validate($data, $validation->mapper, $validation->defaultValues, $validation->not_required, $requestData, $error)) {
                return $requestData;
            }
            igk_ilog([__METHOD__ . " errors ", $error]);
        }
        return false;
    }
    public function validate
    ($data, array $mapper, ?array $defaultValues=null, ?array $not_required=null, &$requestData = null,  ?array &$error = null, ?array $resolvKeys=null): bool
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