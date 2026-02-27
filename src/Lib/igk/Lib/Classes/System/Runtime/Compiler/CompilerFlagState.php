<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerFlagState.php
// @date: 20221019 16:33:19
namespace IGK\System\Runtime\Compiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
abstract class CompilerFlagState{

    /**
    * Constant: read namespace.
    * @var mixed
    */
    const READ_NAMESPACE  = 'namespace';

    /**
    * Constant: read global use.
    * @var mixed
    */
    const READ_GLOBAL_USE = 'global_use';

    /**
    * Constant: read struct.
    * @var mixed
    */
    const READ_STRUCT = 'read_struct';

    /**
    * Constant: read class.
    * @var mixed
    */
    const READ_CLASS = 'read_class';

    /**
    * Constant: read function.
    * @var mixed
    */
    const READ_FUNCTION = 'read_function';

    /**
    * Constant: read desc comment.
    * @var mixed
    */
    const READ_DESC_COMMENT = 'read_desc_comment';

    /**
    * Constant: read class use.
    * @var mixed
    */
    const READ_CLASS_USE = 'read_class_use';

    /**
    * Constant: read variable.
    * @var mixed
    */
    const READ_VARIABLE = 'read_variable';

    /**
    * Constant: read const.
    * @var mixed
    */
    const READ_CONST = 'read_const';

    /**
    * Constant: read expression.
    * @var mixed
    */
    const READ_EXPRESSION = 'read_expression';

    /**
    * Constant: read skip block.
    * @var mixed
    */
    const READ_SKIP_BLOCK = 'read_skip_block';

    /**
    * Constant: read block.
    * @var mixed
    */
    const READ_BLOCK = 'read_block';

    /**
    * Constant: read condition block.
    * @var mixed
    */
    const READ_CONDITION_BLOCK = 'read_condition_block';
    private function __construct(){ 
    }
}