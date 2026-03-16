<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleInclusionMethod
// @date: 20260228 13:59:29
namespace IGK\System\Modules;

use IGK\System\Console\Logger;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use JsonSerializable;

/**
* auto generate doc.
* @package IGK
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Modules
*/
class ModuleInclusionMethod implements JsonSerializable{
    use JsonSerializableTrait;
    private $m_callback;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    private $m_file;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    private $m_at;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    private $m_name;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    private $m_src;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    private $m_params;

    /**
    * .ctr
    * @param string $file
    * @param string $name
    * @param callable $callback
    * @param int $at
    * @param mixed $params
    * @param string $source
    * @return
    */
    public function __construct(string $file, string $name, callable $callback, int $at, $params, string $source)
    {
        $this->m_name = $name;
        $this->m_callback = $callback;
        $this->m_file = $file;
        $this->m_at = $at;
        $this->m_src = $source;
        $this->m_params = $params;
    }

    /**
    * auto generate doc.
    * @return mixed
    */
    public function _json_serialize(){
        return [
            'file'=>$this->m_file,
            'src'=>$this->m_src,
            'at'=>$this->m_at,
            'params'=>$this->m_params
        ];
    }
    /**
     * wake up from cache
     * @param mixed $data 
     * @return ModuleInclusionMethod 
     */
    public static function WakeUpFromCache($data){
        list($file, $code, $at, $params, $name, $line) = igk_extract($data, 'file|src|at|params|name|line');
        $g = ModuleIncludeDefinitionUtility::CreateMethodHandle($params, $code);
        $inf = new ModuleInclusionMethod(
            $file, 
            $name, 
            $g, 
            $line ?? -1,
            $params,
            $code);
        return $inf; 
    }

    /**
    * auto generate doc.
    * @return array{file: string}
    */
    public function getInfo(){
        return [
            'name'=>$this->m_name,
            'file'=>$this->m_file.':'.$this->m_at
        ];
    }

    /**
    * Used by var_dump() to customize debug output.
    * @return
    */
    public function __debugInfo()
    {
        return [
            'module-inclusion-method'=>$this->getInfo()
        ];
    }

    /**
    * auto generate doc.
    * @param mixed $o
    * @return
    */
    public function bindTo($o){
        $this->m_callback = $this->m_callback->bindTo($o);
        return $this;
    }

    /**
    * Called when an object is used as a function.
    * @return
    */
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