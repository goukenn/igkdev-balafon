<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewLayout.php
// @date: 20240915 09:53:22
namespace IGK\System\WinUI;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\System\WinUI
* @author C.A.D. BONDJE DOUE
*/
class ViewLayout{
    private $m_controller;
    private $m_styleloaded = [];

    var $defaultThemeStyle = 'default.pcss';

    /**
     * get base controller 
     * @return BaseController
     */
    public function getController(){
        return $this->m_controller;
    }
    /**
     * 
     * @param BaseController $value 
     * @return void 
     */
    public function setController(BaseController $value){
        $this->m_controller = $value;
    }

    public function isStyleLoaded(string $file){
        return $this->m_styleloaded && key_exists($file, $this->m_styleloaded);
    }
    public function styleLoaded(string $file){
        $this->m_styleloaded[$file] = 1;
    }
    public function clearLoadedStyles(){
        $this->m_styleloaded = [];
    }
}