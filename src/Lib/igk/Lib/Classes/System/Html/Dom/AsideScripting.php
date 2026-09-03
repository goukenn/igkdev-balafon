<?php
// @author: C.A.D. BONDJE DOUE
// @file: AsideScripting.php
// @date: 20260731 16:03:18
namespace IGK\System\Html\Dom;

use IGK\Controllers\ViewLayoutLoader;
use IGKEvents;

/**
 * for render aside scripts
 * @package IGK\System\Html\Dom
 * @author C.A.D. BONDJE DOUE
 */

class AsideScripting
{
    /**
     * composent storage
     * @var ?array
     */
    private $m_components;
    /**
     * 
     * @var mixed
     */
    var $aside;

    /**
     * used components
     * @var mixed
     */
    var $components;

    protected static function _initHooks(&$init)
    {


        igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, function () use (&$init) {
            $init = false;
        });
        igk_reg_hook(IGKEvents::HOOK_HTML_BODY, function ($e) {
            if (!igk_is_ajx_demand()) {
                echo self::getInstance()->render();
            }
        });
        igk_reg_hook(ViewLayoutLoader::HOOK_AFTER_INC, function () {
            if (igk_is_ajx_demand()) {
                echo self::getInstance()->render();
            }
        });
        $init = true;
    }
    /**
     * 
     * @return static 
     */
    public static function getInstance()
    {
        static $init;
        if (is_null($init)) {
            self::_initHooks($init);
        }
        return igk_get_class_instance(self::class);
    }
    public function render($option = null)
    {

        if ($a = $this->aside) {
            return implode('', array_map(function ($i) use ($option) {
                return $i->render($option);
            }, $a));
        }
    }
    /**
     * 
     * @param string $key 
     * @return bool 
     */
    public function contain(string $key): bool
    {
        return isset($this->m_components[$key]);
    }

    /**
     * 
     * @param string $key 
     * @param mixed $script 
     * @return void 
     */
    public function __invoke(string $key, $script)
    {
        if (!isset($this->m_components[$key])) {
            $this->m_components[$key] = $script;
            $this->aside[] = $script;
        }
    }
    public function registerAsBalafonJS(string $key, string $file, $type='balafonjs')
    {
        in_array($type, ['script','balafonjs']) || igk_die('not allowed type');
        $aside = $this;
        if (!$aside->contain($key)) {
            $s = igk_create_node($type);
            $s->content = file_get_contents($file);
            $aside($key, $s);
        }
    }
}
