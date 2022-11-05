<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICompiler.php
// @date: 20221019 16:08:07
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
interface ICompiler{
    /**
     * compile source code 
     * @param string $source 
     * @return null|string 
     */
    function compileSource(string $source): ?string;

    /**
     * compile file 
     * @param string $file 
     * @return null|string 
     */
    function compileFile(string $file): ?string;
}