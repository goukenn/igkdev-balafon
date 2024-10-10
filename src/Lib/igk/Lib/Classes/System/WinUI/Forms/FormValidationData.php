<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationData.php
// @date: 20230304 13:33:15
namespace IGK\System\WinUI\Forms;


///<summary></summary>
/**
* validation data info 
* @package IGK\System\WinUI\Forms
*/
class FormValidationData{
    /**
     * mapper
     * @var mixed
     */
    var $mapper;
    /**
     * is not required
     * @var mixed
     */
    var $not_required;
    /**
     * the default value
     * @var mixed
     */
    var $defaultValues;
    /**
     * array of resolved keys
     * @var ?array 
     */
    var $resolvKeys;
}