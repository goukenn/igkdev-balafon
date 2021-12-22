<?php
// @file: ControllerInitListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\Helper\IO;
use IIGKControllerInitListener;

/**
 * represent a listener object used to initialize ontroller environment
 */
class ControllerInitListener implements IIGKControllerInitListener{
    private $m_folder, $m_type;
    ///<summary></summary>
    ///<param name="folder"></param>
    ///<param name="type" default="null"></param>
    public function __construct($folder, $type=null){
        $this->m_folder=$folder;
        $this->m_type=$type;
        if(!IO::CreateDir($folder)){
            igk_die("can't created dir : ".$folder);
        }
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    public function addDir($dir){
        IO::CreateDir($this->m_folder."/{$dir}");
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="content"></param>
    public function addSource($name, $content, $override=true){
        igk_io_w2file($this->m_folder."/".$name, $content, $override);
    }
}
