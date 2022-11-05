<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenEntryTrait.php
// @date: 20221021 08:50:18
namespace IGK\System\Runtime\Compiler\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenEntryTrait{
   
    public function compileFile(string $file): ?string
    {
        if (!empty($file) && is_file($file)) {
            return $this->compileSource(file_get_contents($file));
        }
        return null;
    }
     /**
     * compile source
     * @param string $source 
     * @return null|string 
     * @throws IGKException 
     */
    public function compileSource(string $source): ?string
    {
        $this->parseToken($source);

        return $this->mergeSourceCode();
    }
}