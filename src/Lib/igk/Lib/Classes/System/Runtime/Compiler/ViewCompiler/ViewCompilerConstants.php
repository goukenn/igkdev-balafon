<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerConstants.php
// @date: 20221027 11:14:15
namespace IGK\System\Runtime\Compiler\ViewCompiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
abstract class ViewCompilerConstants{
    const OPERATOR_SYMBOL =  "+,-,->,?->,<=>,+=,-=,/=,%=,*=,&&,||,(,[";
    const BLOCK_TRIM_CHAR = "\t\n\r\0\x0B; ";
}