<?php
// @author: C.A.D. BONDJE DOUE
// @file: StepperFormDefinition.php
// @date: 20260726 14:58:03
namespace IGK\System\Html\Dom;


/**
* 
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/

class StepperFormDefinition
{
    /**
     * stepper definition title
     * @var mixed
     */
    var $title;

    /**
     * stepper definition hint
     * @var mixed
     */
    var $hint;
    /**
     * @var ?array
     */
    var $fields;

    /**
     * check on validity
     * @var array
     */
    var $matchValidityFields;
}