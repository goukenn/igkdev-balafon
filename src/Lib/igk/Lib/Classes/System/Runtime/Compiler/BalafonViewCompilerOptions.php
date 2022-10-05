<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompilerOptions.php
// @date: 20220909 17:05:56
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompilerOptions{
    /**
     * layout 
     * @var mixed
     */
    var $layout;

    /**
     * the controller to use
     * @var ?BaseController
     */
    var $controller;

    /**
     * the document to use
     * @var ?IGKHtmlDoc
     */
    var $document;
}