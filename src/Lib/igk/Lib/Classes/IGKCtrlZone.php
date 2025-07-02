<?php
// @file: IGKCtrlZone.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKCtrlZone extends IGKObject implements IIGKCtrlDirManagement{
    private $m_filename;
    public function __construct($fname){
        $this->m_filename=$fname;
    }
    public function getContentDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_CONTENT_FOLDER);
    }
    public function getDataDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_DATA_FOLDER);
    }
    public function getDeclaredDir():string{
        return dirname($this->m_filename);
    }
    public function getName(){
        return strtolower(__CLASS__."://".$this->m_filename);
    }
    public function getResourcesDir(){
        return $this->getDataDir()."/".IGK_RES_FOLDER;
    }
    public function getScriptsDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_SCRIPT_FOLDER);
    }
    public function getStylesDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_STYLE_FOLDER);
    }
    public function getViewDir(){
        return igk_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_VIEW_FOLDER);
    }
}
