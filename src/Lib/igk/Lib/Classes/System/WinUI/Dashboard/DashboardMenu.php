<?php
// @author: C.A.D. BONDJE DOUE
// @file: DashboardMenu.php
// @date: 20260819 10:25:01
namespace IGK\System\WinUI\Dashboard;

use IGK\Helper\Activator;
use function igk_resources_gets as __;

/**
 * 
 * @package IGK\System\WinUI\Dashboard
 * @author C.A.D. BONDJE DOUE
 */
class DashboardMenu
{
    /**
     * group to register 
     * @var mixed
     */
    var $group;
    /**
     * href to reference 
     * @var mixed
     */
    var $href;
    /**
     * autorisation required to view the menu
     * @var ?string|string[]
     */
    var $auth;
    /**
     * display label 
     * @var mixed
     */
    var $label;
    /**
     * icons to use 
     * @var mixed
     */
    var $icon;
    /**
     * mark is active. default is true
     * @var ?bool
     */
    var $active = true;

    /**
     * @var mixed
     */
    var $class;

    /**
     * define icons to attach to 
     * @var mixed
     */
    var $iconClass;

    public static function Build($ul, $menus)
    {
        $group = null;
        /**
         * @var string|DashboardMenu $i
         */
        foreach ($menus as $k => $i) {
            $content = $i;
            if (is_numeric($k)) {
                if ($i == '-') {
                    $ul->li()->setClass('separator');
                    continue;
                }
                $content = $i;
            } else {
                $i = Activator::CreateNewInstance(DashboardMenu::class, $i);
                $content = $k;
                $group = igk_getv($i, 'group');
                $content = $i->label ?? __($content);
            }
            $li = $ul->li()->setClass('menu-i');
            $a = $li->a($i->href);
            $a->setClass(['no-wrap dispflex']);
            if ($i->icon) {
                $r = igk_svg_use($i->icon);
                if ($i->iconClass){
                    $r->setClass($i->iconClass);
                }
                $a->span()->setContent($r); 
            }
            $a->span()->content = $content;
        }
    }
}
