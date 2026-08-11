<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProfileDefinition.php
// @date: 20260803 17:34:55
namespace IGK\System\Security;


/**
* profile definition 
* @package IGK\System\Security
* @author C.A.D. BONDJE DOUE
*/
class ProfileDefinition{
    /**
     * name of the profile 
     * @var mixed
     */
    var $name;

    /**
     * the is use to display
     * @var ?string
     */
    var $display;
    /**
     * list of permission
     * @var ?array
     */
    var $permission;
    /**
     * description
     * @var mixed
     */
    var $description;
}