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
    * auto generate doc.
    */    public function getCanConfigure()
    {
        return 1;
    }

    /**
    * auto generate doc.
    */    public function getConfigGroup()
    {
        return "administration";
    }

    /**
    * Returns Config Page.
    */
    public function getConfigPage()
    {
        return "console";
    }

    /**
    * auto generate doc.
    */
    public function getConfigImageKey()
    {
        return "";
    }

    /**
    * auto generate doc.
    */
    public function getConfigIndex()
    {
        return 10;
    }
    /**
     * 
     */

    /**
    * auto generate doc.
    */
    public function getIsConfigPageAvailable()
    {
        return !igk_environment()->isOPS();
    }

    /**
    * View.
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
