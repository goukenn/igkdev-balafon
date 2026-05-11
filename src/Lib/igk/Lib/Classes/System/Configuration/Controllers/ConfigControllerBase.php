<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigControllerBase.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration\Controllers;
use IGK\Controllers\BaseController;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\Path;
use IGK\System\WinUI\Menus\MenuItem;
use IGKEvents;
use IGKException;
use ReflectionException;
use function igk_resources_gets as __;

require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/Controllers/IConfigController.php";
/**
 * Represent ConfigControllerBase class
 */
abstract class ConfigControllerBase extends BaseController implements IConfigController
{
    use NoDbActiveControllerTrait;
    /**
    * auto generate doc.
    * @return string
    */
    public function getName(): string
    {
        return strtolower(static::class);
    }
    /**
    * Returns Use Data Schema.
    * @return bool
    */
    public function getUseDataSchema(): bool
    {
        if (self::IsSysController(static::class)) {
            return false;
        }
        return false;
    }
    /**
    * Returns View Dir.
    */
    public function getViewDir()
    {
        if (Path::IsInLibrary($this->getDeclaredDir())) {
            return IGK_LIB_DIR . "/" . IGK_VIEW_FOLDER;
        }
        return parent::getViewDir();
    }
    /**
    * Returns Articles Dir.
    */
    public function getArticlesDir()
    {
        if (Path::IsInLibrary($this->getDeclaredDir())) {
            return IGK_LIB_DIR . "/" . IGK_ARTICLES_FOLDER;
        }
        return parent::getViewDir();
    }
    /**
    * Returns Data Dir.
    */
    public function getDataDir()
    {
        if (Path::IsInLibrary($this->getDeclaredDir())) {
            return IGK_LIB_DIR . "/" . IGK_DATA_FOLDER;
        }
        return parent::getDataDir();
    }
    /**
     * allways no no_auto_cache_view
     * @param mixed $name 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function getConfig($name, $default = null)
    {
        return $this->getConfigs()->get($name, $default);
    }
    /**
    * auto generate doc.
    * @param mixed $node
    * @param mixed $title
    */
    protected function addTitle($node, $title)
    {
        $d = $node->div();
        $d["class"] = "igk-cnf-title";
        $d->Content = __($title);
    }
    /**
    * auto generate doc.
    */
    public function getConfigCtrl()
    {
        return igk_getctrl(IGK_CONF_CTRL, false);
    }
    /**
    * auto generate doc.
    */
    public function getConfigNode()
    {
        return $this->getConfigCtrl()->getConfigNode();
    }
    /**
    * auto generate doc.
    */
    public function getConfigPage()
    {
        return "default";
    }
    /**
    * auto generate doc.
    */
    protected function getGlobalHelpArticle()
    {
        return "./help/help." . $this->Name;
    }
    /**
    * auto generate doc.
    */
    public function getIsConfigPageAvailable()
    {
        return igk_is_conf_connected();
    }
    /**
    * auto generate doc.
    */
    public function getIsVisible(): bool
    {
        $app = igk_app();
        $cnf = $this->ConfigCtrl;
        $v = $cnf->getIsConnected();
        return $v;
    }
    /**
    * auto generate doc.
    * @param mixed $context
    */
    protected function initComplete($context = null)
    {
        parent::initComplete($context);
        if ($c = $this->getConfigCtrl()) {
            $c->registerConfig($this);
        }
    }
    /**
    * auto generate doc.
    */
    public function initConfigMenu()
    {
        if (!$this->getIsConfigPageAvailable()) {
            return null;
        }
        $c = $this->getConfigPage();
        $error_msg = "No config menu found for : " . $c . " = " . $this->Name . " : " . get_class($this) . " " . $this->getDeclaredFileName();
        $conf = igk_get_configs_menu_settings();
        if (isset($conf->$c)) {
            $cp = $conf->$c;
            if ($cp) {
                return array(
                    new MenuItem(
                        $cp->menuname,
                        $cp->pagename,
                        $this->getUri("showConfig"),
                        $cp->menuindex,
                        $cp->imagekey,
                        $cp->group
                    )
                );
            } else {
                igk_ilog($error_msg, __METHOD__);
            }
        } else {
            return array(
                new MenuItem(
                    $c,
                    $c,
                    $this->getUri("showConfig"),
                    $this->ConfigIndex ?? -1,
                    $this->ConfigImageKey,
                    $this->getConfigGroup()
                )
            );
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param string $function
    */
    protected function IsFunctionExposed(string $function)
    {
        if (!igk_is_conf_connected() || igk_configs()->get("no_web_configuration")) {
            return false;
        }
        return true; 
    }
    /**
     * base show Configuration of the controller
     */
    public function showConfig()
    {
        $_t = $this->getTargetNode();
        $_handled = $this->getEnvParam('handled');
        if (!$_handled) {
            $e_key  = "sys://config/selectedview";
            $this->ConfigCtrl->setSelectedConfigCtrl($this, get_class($this) . "::showConfig");
            if (!$this->getIsVisible()) {
                $_t->remove();
                igk_set_env($e_key, null);
            } else {
                $this->View();
                if ($_cnf_node = $this->getConfigNode()) {
                    $_cnf_node->clearChilds();
                    $_cnf_node->add($_t); 
                    igk_set_env($e_key, $this);
                }
            }
            $this->setEnvParam('handled', true);
        }  
    }
    /**
    * used to initialize the config view node
    * @param mixed $target
    * @param mixed $titlekey
    * @param mixed $descfile
    */
    protected function viewConfig($target, $titlekey, $descfile)
    {
        return igk_html_ctrl_view_config($this, $target, $titlekey, $descfile);
    }
    /**
    * Select config view.
    * @param mixed $ctrl
    */
    protected function _selectConfigView($ctrl)
    {
        igk_environment()->set('sys://config/selectedview', $ctrl);
    }
}