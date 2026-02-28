<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleInclusionMethod
// @date: 20260228 13:59:29
namespace IGK\System\Modules;

use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use Kreait\Firebase\Util\JSON;

/**
 * 
 * @package IGK
 * @author C.A.D. BONDJE DOUE
 */
class ModuleInclusionMethod{
    private $m_callback;
    private $m_file;
    private $m_at;
    private $m_name;
    public function __construct(string $file, string $name, callable $callback, int $at)
    {
        $this->m_name = $name;
        $this->m_callback = $callback;
        $this->m_file = $file;
        $this->m_at = $at;
    }
    /**
     * 
     * @return array{file: string} 
     */
    public function getInfo(){
        return [
            'name'=>$this->m_name,
            'file'=>$this->m_file.':'.$this->m_at
        ];
    }
    public function __debugInfo()
    {
        return [
            'module-inclusion-method'=>$this->getInfo()
        ];
    }
    public function bindTo($o){
        $this->m_callback = $this->m_callback->bindTo($o);
        return $this;
    }
    public function __invoke(){
        try{
            return call_user_func_array($this->m_callback, func_get_args());
        }
        catch(\TypeError $ex){
            throw new \IGKException(implode("\n", ['Inclusion method failed', 
            'sourceMessage:'.$ex->getMessage(),
            json_encode($this->getInfo(), JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES)]));
        }
    }
}