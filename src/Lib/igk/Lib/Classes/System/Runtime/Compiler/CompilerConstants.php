<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerConstants.php
// @date: 20221028 20:25:03
namespace IGK\System\Runtime\Compiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
abstract class CompilerConstants{
    /**
    * Constant: loop context data var.
    * @var mixed
    */
    const LOOP_CONTEXT_DATA_VAR = '__igk_loop_context_data__';
    /**
    * Constant: binding data context var.
    * @var mixed
    */
    const BINDING_DATA_CONTEXT_VAR = '___igk_binding_data_context___';
    /**
    * Constant: binding context var name.
    * @var mixed
    */
    const BINDING_CONTEXT_VAR_NAME = '$__igk_data_context__';
}