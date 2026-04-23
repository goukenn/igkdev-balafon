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
* Igkctrl zone.
*/
final class IGKCtrlZone extends IGKObject implements ICtrlDirManagement{
    /**
    * Name of filename.
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
    * Returns Content Dir.
    */
    public function getContentDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_CONTENT_FOLDER);
    }
    /**
    * Returns Data Dir.
    */
    public function getDataDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_DATA_FOLDER);
    }
    /**
    * Returns Declared Dir.
    * @return string
    */
    public function getDeclaredDir():string{
        return dirname($this->m_filename);
    }
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return strtolower(__CLASS__."://".$this->m_filename);
    }
    /**
    * Returns Resources Dir.
    */
    public function getResourcesDir(){
        return $this->getDataDir()."/".IGK_RES_FOLDER;
    }
    /**
    * Returns Scripts Dir.
    */
    public function getScriptsDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_SCRIPT_FOLDER);
    }
    /**
    * Returns Styles Dir.
    */
    public function getStylesDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_STYLE_FOLDER);
    }
    /**
    * Returns View Dir.
    */
    public function getViewDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_VIEW_FOLDER);
    }
}