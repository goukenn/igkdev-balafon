<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenCompileTrait.php
// @date: 20221024 00:25:14
namespace IGK\System\Runtime\Compiler\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
trait CompilerTokenCompileTrait{
 /**
     * 
     * @var ?ReadTokenOptions
     */
    var $options;

    public function compileSource(string $source): ?string
    {
        $this->parseToken($source);

        return $this->mergeSourceCode();
    }

    public function compileFile(string $file): ?string
    {
        if (is_file($file)) {
            return $this->compileSource(file_get_contents($file));
        }
        return null;
    }
}