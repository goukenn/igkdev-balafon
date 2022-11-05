<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvControllerCacheDataBase.php
// @date: 20220906 11:19:26
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Controllers\SysDbControllerManager;
use IGK\Database\DbSchemaDefinitions;
use IGK\Helper\Utility;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class EnvControllerCacheDataBase{
    const FILE = DbSchemaDefinitions::CACHE_FILE;
    var $serie = [];
    var $file;
    private $m_sysdb;
    public function __construct(?string $file, ?SysDbController $sysdb=null)
    {
        if (is_null($sysdb)){
            $sysdb =igk_getctrl(SysDbController::class);     
        }
        $this->file = $file;
        $this->m_sysdb = $sysdb;
    }
    public function update(BaseController $controller){ 
        if ($def = $controller::getDataTableDefinition()){
            $cl = get_class($controller);
            $n = $controller->getDataAdapterName();
            if (!isset($this->serie[$n])){
                $this->serie[$n] = [];
            }
            $to_seri = (object)["tables"=>[]];
            // unset controller for storage
            foreach($def->tables as $k=>$v){
                $m = (array)$v;
                unset($m["controller"]);
                unset($m["entries"]);
                unset($m["tableRowReference"]);
                $to_seri->tables[$k] = $m;
            }       
            $this->serie[$n][$cl] = json_decode(Utility::TO_JSON($to_seri, [
                'ignore_empty'=>1
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));           
        }
    }
    /**
     * complete
     * @return void 
     * @throws IGKException 
     */
    public function complete(){   
        if ($this->file)
            igk_io_w2file($this->file, serialize($this->serie));        
    }
}