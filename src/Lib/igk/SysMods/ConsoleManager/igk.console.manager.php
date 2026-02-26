<?php
// @file: igk.console.manager.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\ConfigControllerBase;


use function igk_resources_gets as __;

/**
 * Represent IGKConsoleToolManager class
 */
final class IGKConsoleToolManager extends ConfigControllerBase
{
    /**
     * 
     */
    public function getCanConfigure()
    {
        return 1;
    }
    /**
     * 
     */
    public function getConfigGroup()
    {
        return "administration";
    }

    /**
    * auto generate doc.
    */
    public function getConfigPage()
    {
        return "console";
    }

    /**
     * 
     */

    public function getConfigImageKey()
    {
        return "";
    }
    /**
     * 
     */

    public function getConfigIndex()
    {
        return 10;
    }
    /**
     * 
     */

    /**
     * 
     */

    public function getIsConfigPageAvailable()
    {
        return !igk_environment()->isOPS();
    }

    /**
    * auto generate doc.
    * @return BaseController
    */
    public function View(): BaseController
    {
        $t = $this->getTargetNode();
        if (!$this->getIsVisible()) {
            $t->remove();
        } else {
            $cnf = $this->getConfigNode();
            $cnf->add($t);
            $t->clearChilds();
            $t = $this->viewConfig($t, __("Admin Console"), ".help/console.manager.desc");
            $frm = $t->div()->addPanelBox()->addForm();
            $frm->div()->Content = __("In Development");
        }
        return $this;
    }
}
