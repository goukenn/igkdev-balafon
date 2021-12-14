<?php
// @file: IGKCtrlZone.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKCtrlZone extends IGKObject implements IIGKCtrlDirManagement{
    private $m_filename;
    ///<summary></summary>
    ///<param name="fname"></param>
    public function __construct($fname){
        $this->m_filename=$fname;
    }
    ///<summary></summary>
    public function getContentDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_CONTENT_FOLDER);
    }
    ///<summary></summary>
    public function getDataDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_DATA_FOLDER);
    }
    ///<summary></summary>
    public function getDeclaredDir(){
        return dirname($this->m_filename);
    }
    ///<summary></summary>
    public function getName(){
        return strtolower(__CLASS__."://".$this->m_filename);
    }
    ///<summary></summary>
    public function getResourcesDir(){
        return $this->getDataDir()."/".IGK_RES_FOLDER;
    }
    ///<summary></summary>
    public function getScriptDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_SCRIPT_FOLDER);
    }
    ///<summary></summary>
    public function getStylesDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_STYLE_FOLDER);
    }
    ///<summary></summary>
    public function getViewDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_VIEW_FOLDER);
    }
}
