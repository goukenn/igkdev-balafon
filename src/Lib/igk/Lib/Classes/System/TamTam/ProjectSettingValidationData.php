<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettingValidationData.php
// @date: 20230309 21:30:59
namespace IGK\System\TamTam;

use IGK\System\Configuration\ProjectSettings;
use IGK\System\Data\ObjectDataValidator;
use IGK\System\WinUI\Forms\FormData;

///<summary></summary>
/**
* 
* @package IGK\System\TamTam
*/
class ProjectSettingValidationData extends FormData{
     
    protected static function CreateValidatorInstance(){
        return new ObjectDataValidator();
    }
    /**
     * override class reference 
     * @return string 
     */
    protected function getValidationClassReference(){
        return ProjectSettings::class;
    }
    function getNotRequired(): ?array
    {
        return ['version','name', 'required'];
    }
}