<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InstallerActionMiddleWare.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Installers;

use Exception;

///<summary>Represente class: InstallerActionMiddleWare</summary>
/**
* Represente InstallerActionMiddleWare class
*/
class InstallerActionMiddleWare implements IMiddleWareAction{
    private $_next;
    private $_service;

    public function invoke() {

    }
    ///<summary></summary>
    /**
    * abort action independly
    */
    public function abort(){
        if ($this->_service->Current > 0){
            $this->_service->Current--;
            if ($bserv = igk_getv($this->_service->List, $this->_service->Current)){
                $bserv->abort();
            }
        }
        $this->_service->Success = false;
    }
    
    ///<summary></summary>
    /**
    * get current message title
    */
    public function getMessage(){
        return get_class($this);
    }
    ///<summary></summary>
    /**
    * get service info
    * @var object
    */
    public function getServiceInfo(){
        return $this->_service;
    }
    ///<summary></summary>
    /**
    * go to next action and invoke
    */
    public final function next(){
        if($this->_next){
            $this->_service->Current++;
            $this->_next->invoke();
        }
    }
    ///<summary></summary>
    ///<param name="list"></param>
    ///<param name="index"></param>
    ///<param name="service" default="null"></param>
    /**
    * 
    * @param mixed $list
    * @param mixed $index
    * @param mixed $service the default value is null
    */
    public static function Run($list, $index, ?InstallerMiddleWareActions $service){
        $c=$list[$index]; 
        $_service=(object)array(
            "Success"=>0,
            "Start"=>$index,
            "List"=>$list,
            "Current"=>$index,
            "BaseDir"=> $service ? $service->BaseDir: igk_io_basedir(),
            "LibDir"=> $service ? $service->LibDir: IGK_LIB_DIR,
            "CoreZip"=> $service ? igk_getv($service, "CoreZip") : null,
            "CacheDir"=>$service && !empty($cd=$service->CacheDir) ? $cd: igk_io_cachedir(),
            "Version"=>IGK_VERSION,
            "Listener"=>$service
        );
        // + | bind services list
        while($index < (count($list)-1)){
            $list[$index]->_service=$_service;
            $list[$index]->_next=$list[$index + 1];
            $index++;
        }
        $list[$index]->_service=$_service;
        try{
            $c->invoke();
        } catch (Exception $ex) {
            if ($_service->Current>0){
                $_service->Current--;
                if ($bsrv = igk_getv($_service->List, $_service->Current)){
                    $bsrv->abort();
                }
            } 
        }
        return $_service->Success;
    }
}