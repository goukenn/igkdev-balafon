<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InstallerMiddleWareActions.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Installers;

use function igk_resources_gets as __;
///<summary>Installer middel ware storage</summary>
/**
* Installer middel ware storage
*/
class InstallerMiddleWareActions{
    private $_list;
    var $BaseDir;
    var $CacheDir;
    var $LibDir;
    /**
     * install directory
     * @var ?string
     */
    var $installDir;

    /**
     * from uploading
     * @var bool 
     */
    var $fromUpload;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        $this->_list=array(); 
    }
    /**
     * abort list
     * @return void 
     */
    public function abort(){
        // if (count($this->_list)>0){
        //     $this->_list[0]->abort();
        // }
        if (count($this->_list)>0){
            $serv = $this->_list[0]->getServiceInfo();
            if ($serv && ($bserv = $this->_list[$serv->Current])){
                $bserv->abort();
            }
        }
    }
   
    ///<summary></summary>
    ///<param name="middle"></param>
    /**
    * 
    * @param mixed $middle
    */
    public function add(IMiddleWareAction $middle){
        if(!is_object($middle)){
            return;}
        if(get_class($middle) == InstallerEventMessageMiddleWare::class)
            return;
        if($this->isEventStream()){
            $this->_list[]=new InstallerEventMessageMiddleWare($middle);
        }
        $this->_list[]=$middle;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function isEventStream(){
        return igk_server()->HTTP_ACCEPT == "text/event-stream";
    }
    ///<summary></summary>
    /**
    * 
    * @return mixed
    */
    public function process(){
        if(count($this->_list)<=0)
            return false;
        if($this->Success=InstallerActionMiddleWare::Run($this->_list, 0, $this)){
            $this->write(__("Process Complete"));
        }
        return $this->Success;
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    /**
    * 
    * @param mixed $msg
    */
    public function write($msg){
        if($this->isEventStream()){
            igk_flush_write($msg);
            igk_flush_data();
        }
    }
}
