<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderAfterBuildHookOption.php
// @date: 20230309 21:24:02
namespace IGK\System\TamTam;

use IGK\IHookOptions;

///<summary></summary>
/**
* 
* @package IGK\System\TamTam
*/
class ProjectBuilderAfterBuildHookOption implements IHookOptions{
    /**
     * array of errors
     * @var array
     */
    var $errors = [];
    /**
     * entry output 
     * @var mixed
     */
    var $output;
    /**
     * build type
     * @var mixed
     */
    var $type;
    /**
     * build args
     * @var mixed
     */
    var $args;
}