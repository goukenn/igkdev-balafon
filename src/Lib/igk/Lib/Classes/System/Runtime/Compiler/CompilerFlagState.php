<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerFlagState.php
// @date: 20221019 16:33:19
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class CompilerFlagState{
    const READ_NAMESPACE  = 'namespace';
    const READ_GLOBAL_USE = 'global_use'; 
    const READ_STRUCT = 'read_struct';
    const READ_CLASS = 'read_class';
    const READ_FUNCTION = 'read_function';
    const READ_DESC_COMMENT = 'read_desc_comment';
    const READ_CLASS_USE = 'read_class_use';
    const READ_VARIABLE = 'read_variable';
    const READ_CONST = 'read_const';
    const READ_EXPRESSION = 'read_expression';
    const READ_SKIP_BLOCK = 'read_skip_block';
    const READ_BLOCK = 'read_block';
    const READ_CONDITION_BLOCK = 'read_condition_block';
    private function __construct(){ 
    }
}