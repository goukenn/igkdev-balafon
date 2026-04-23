<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationPageViewLoader.php
// @date: 20250123 08:34:44
namespace IGK\System\Configuration\WinUI;
use IGK\Controllers\BaseController;
use IGK\Controllers\ViewLayoutBase;
use IGK\System\Configuration\Controllers\ConfigureController;
use IGK\System\WinUI\IViewLayoutLoader;

/**
* auto generate doc.
* @package IGK\System\Configuration\WinUI
* @author C.A.D. BONDJE DOUE
*/
class ConfigurationPageViewLoader extends ViewLayoutBase implements IViewLayoutLoader{
    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;
    /**
    * .ctr
    * @param ConfigureController $ctrl
    */
    public function __construct(ConfigureController $ctrl)
    {
        $this->m_ctrl = $ctrl;
    }
    /**
    * Returns Controller.
    * @return BaseController
    */
    public function getController(): BaseController {
        return $this->m_ctrl;
    }
    /**
    * Includes.
    * @param string $file
    * @param null|array $args
    */
    public function include(string $file, ?array $args) {  
        igk_include_view_file($this->getController(), $file, true, $args);   
    }
    /**
    * Returns View Loader.
    */
    public function getViewLoader(){
        return null;
    }
}