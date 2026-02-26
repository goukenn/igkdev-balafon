<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewLayout.php
// @date: 20240915 09:53:22
namespace IGK\System\WinUI;
use IGK\Controllers\BaseController;
use IGK\Constants;
/**
* 
* @package IGK\System\WinUI
* @author C.A.D. BONDJE DOUE
*/
class ViewLayout{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_controller;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_styleloaded = [];
    /**
     * default theme file 
     * @var ?string
     */
    var $defaultThemeStyle = Constants::DEFAULT_THEME_STYLE;
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

    /**
    * auto generate doc.
    * @param string $file
    */
    public function isStyleLoaded(string $file){
        return $this->m_styleloaded && key_exists($file, $this->m_styleloaded);
    }

    /**
    * auto generate doc.
    * @param string $file
    */
    public function styleLoaded(string $file){
        $this->m_styleloaded[$file] = 1;
    }

    /**
    * auto generate doc.
    */
    public function clearLoadedStyles(){
        $this->m_styleloaded = [];
    }
}