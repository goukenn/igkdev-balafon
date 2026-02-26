<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewLayoutBase.php
// @date: 20220801 08:23:19
// @desc: base view layout loader
namespace IGK\Controllers;
use IGK\System\Configuration\ConfigurationData;
use IGK\System\WinUI\IViewLayoutLoader;
use IGKObject;
/**
 * layout base loader
 * @package IGK\Controllers
 */
abstract class ViewLayoutBase extends IGKObject implements IViewLayoutLoader {
    /**
     * get the controller
     * @var BaseController
     */
    protected $controller;
    /**
     * configuration data used for layout
     * @var ConfigurationData
     */
    protected $m_configs;

    /**
    * auto generate doc.
    */
    public function getConfigs(){
        return $this->m_configs;
    }

    /**
    * auto generate doc.
    * @return BaseController
    */
    public function getController(): BaseController
    {
        return $this->controller;
    }

    /**
    * .ctr
    * @param BaseController $controller
    */
    public function __construct(BaseController $controller)
    {
        $this->controller = $controller;
        $this->m_configs = new ConfigurationData;
        $this->initialize();
    }

    /**
    * auto generate doc.
    */
    protected function initialize(){ 
    }
    /**
     * check if file exists
     * @param mixed $file 
     * @return bool 
     */

    protected function exists($file){
        return !empty($file) && igk_io_file_exists($file, true);
    }
}