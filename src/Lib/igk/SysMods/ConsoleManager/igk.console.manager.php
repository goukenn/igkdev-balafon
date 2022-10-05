<?php
// @file: igk.console.manager.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\ConfigControllerBase;


use function igk_resources_gets as __;

///<summary>Represente class: IGKConsoleToolManager</summary>
/**
 * Represente IGKConsoleToolManager class
 */
final class IGKConsoleToolManager extends ConfigControllerBase
{
    ///<summary></summary>
    /**
     * 
     */
    public function getCanConfigure()
    {
        return 1;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigGroup()
    {
        return "administration";
    }
    public function getConfigPage()
    {
        return "console";
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getConfigImageKey()
    {
        return "";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigIndex()
    {
        return 10;
    }
    ///<summary></summary>
    /**
     * 
     */

    ///<summary></summary>
    /**
     * 
     */
    public function getIsConfigPageAvailable()
    {
        return !igk_environment()->is("production");
    }
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
