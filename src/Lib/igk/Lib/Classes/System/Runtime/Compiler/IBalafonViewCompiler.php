<?php
// @author: C.A.D. BONDJE DOUE
// @file: IBalafonViewCompiler.php
// @date: 20221014 13:28:25
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
interface IBalafonViewCompiler{
    /**
     * ouput result 
     * @return ?string 
     */
    function output(): ?string;

    /**
     * append compiler result
     * @param string $result 
     * @return mixed 
     */
    function append(string $result);

    /**
     * compile blocks
     * @param array $codeblocks 
     * @param bool $extract 
     * @param bool $header code block header 
     * @return mixed 
     */
    function compile( array $codeblocks,bool $extract = true, ?string $header =null);
}