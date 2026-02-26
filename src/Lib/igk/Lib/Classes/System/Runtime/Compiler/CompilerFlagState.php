<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerFlagState.php
// @date: 20221019 16:33:19
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class CompilerFlagState{

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_NAMESPACE  = 'namespace';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_GLOBAL_USE = 'global_use';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_STRUCT = 'read_struct';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_CLASS = 'read_class';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_FUNCTION = 'read_function';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_DESC_COMMENT = 'read_desc_comment';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_CLASS_USE = 'read_class_use';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_VARIABLE = 'read_variable';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_CONST = 'read_const';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_EXPRESSION = 'read_expression';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_SKIP_BLOCK = 'read_skip_block';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_BLOCK = 'read_block';

    /**
    * auto generate doc.
    * @var mixed
    */
    const READ_CONDITION_BLOCK = 'read_condition_block';
    private function __construct(){ 
    }
}