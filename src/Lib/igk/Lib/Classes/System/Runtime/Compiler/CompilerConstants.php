<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerConstants.php
// @date: 20221028 20:25:03
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class CompilerConstants{

    /**
    * auto generate doc.
    * @var mixed
    */
    const LOOP_CONTEXT_DATA_VAR = '__igk_loop_context_data__';

    /**
    * auto generate doc.
    * @var mixed
    */
    const BINDING_DATA_CONTEXT_VAR = '___igk_binding_data_context___';

    /**
    * auto generate doc.
    * @var mixed
    */
    const BINDING_CONTEXT_VAR_NAME = '$__igk_data_context__';
}