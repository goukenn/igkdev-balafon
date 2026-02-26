<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InstallerMiddleWareActions.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Installers;
use IGK\System\Http\AcceptMimeTypes;
use function igk_resources_gets as __;
/**
* Installer middel ware storage
*/
class InstallerMiddleWareActions{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_list;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $BaseDir;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $CacheDir;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $LibDir;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Success;
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
    /**
     * is event stream request
    * @return bool
    */

    public function isEventStream():bool{
        return igk_server()->eventStreamRequest();
    }
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