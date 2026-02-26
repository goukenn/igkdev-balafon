<?php
// @file: IGKCtrlZone.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\ICtrlDirManagement;

/**
* auto generate doc.
*/
final class IGKCtrlZone extends IGKObject implements ICtrlDirManagement{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_filename;

    /**
    * .ctr
    * @param mixed $fname
    */
    public function __construct($fname){
        $this->m_filename=$fname;
    }

    /**
    * auto generate doc.
    */

    public function getContentDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_CONTENT_FOLDER);
    }

    /**
    * auto generate doc.
    */

    public function getDataDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_DATA_FOLDER);
    }

    /**
    * auto generate doc.
    * @return string
    */

    public function getDeclaredDir():string{
        return dirname($this->m_filename);
    }

    /**
    * auto generate doc.
    * @return string
    */

    public function getName(): string{
        return strtolower(__CLASS__."://".$this->m_filename);
    }

    /**
    * auto generate doc.
    */

    public function getResourcesDir(){
        return $this->getDataDir()."/".IGK_RES_FOLDER;
    }

    /**
    * auto generate doc.
    */

    public function getScriptsDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_SCRIPT_FOLDER);
    }

    /**
    * auto generate doc.
    */

    public function getStylesDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_STYLE_FOLDER);
    }

    /**
    * auto generate doc.
    */

    public function getViewDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_VIEW_FOLDER);
    }
}