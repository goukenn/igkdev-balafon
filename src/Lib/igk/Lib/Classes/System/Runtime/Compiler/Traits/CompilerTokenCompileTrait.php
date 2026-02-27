<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenCompileTrait.php
// @date: 20221024 00:25:14
namespace IGK\System\Runtime\Compiler\Traits;
/**
* 
* @package IGK\System\Runtime\Compiler
*/

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenCompileTrait{

    /**
    * auto generate doc.
    * @var ?ReadTokenOptions
    */
    var $options;

    /**
    * Compile source.
    * @param string $source
    * @return ?string
    */
    public function compileSource(string $source): ?string
    { 
        $this->parseToken($source); 
        return $this->mergeSourceCode();
    }

    /**
    * Compile file.
    * @param string $file
    * @return ?string
    */
    public function compileFile(string $file): ?string
    {
        if (is_file($file)) {
            return $this->compileSource(file_get_contents($file));
        }
        return null;
    }
}