<?php
// @file: ControllerInitListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\Helper\IO;
use IGK\IControllerInitListener;
/**
 * represent a listener object used to initialize ontroller environment
 */
class ControllerInitListener implements IControllerInitListener{

    /**
    * Properties: folder, type.
    * @var mixed
    */
    private $m_folder, $m_type;

    /**
    * .ctr
    * @param mixed $folder
    * @param null|mixed $type
    */
    public function __construct($folder, $type=null){
        $this->m_folder=$folder;
        $this->m_type=$type;
        if(!IO::CreateDir($folder)){
            igk_die("can't created dir : ".$folder);
        }
    }

    /**
    * Adds Dir.
    * @param mixed $dir
    */
    public function addDir($dir){
        IO::CreateDir($this->m_folder."/{$dir}");
    }

    /**
    * Adds Source.
    * @param mixed $name
    * @param mixed $content
    * @param mixed $override
    */
    public function addSource($name, $content, $override=true){
        igk_io_w2file($this->m_folder."/".$name, $content, $override);
    }
}