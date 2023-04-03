<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettingValidationData.php
// @date: 20230309 21:30:59
namespace IGK\System\TamTam;

use IGK\System\Data\ObjectDataValidator;
use IGK\System\WinUI\Forms\FormData;

///<summary></summary>
/**
* 
* @package IGK\System\TamTam
*/
class ProjectSettingValidationData extends FormData{
    
    var $version;

    protected static function CreateValidatorInstance(){
        return new ObjectDataValidator();
    }
}