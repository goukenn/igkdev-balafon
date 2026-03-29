<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettingValidationData.php
// @date: 20230309 21:30:59
namespace IGK\System\TamTam;
use IGK\System\Configuration\ProjectSettings;
use IGK\System\Data\ObjectDataValidator;
use IGK\System\WinUI\Forms\FormData;
/**
* auto generate doc.
* @package IGK\System\TamTam
*/
class ProjectSettingValidationData extends FormData{
    /**
    * Creates Validator Instance.
    */
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
    /**
    * Returns Not Required.
    * @return ?array
    */
    function getNotRequired(): ?array
    {
        return ['version','name', 'required'];
    }
}