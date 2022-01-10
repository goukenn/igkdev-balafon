<?php
// @file: IGKMailAttachement.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Net;

use IGKObject;

class MailAttachement extends IGKObject{
    private $m_content;
    var $CID, $ContentType, $Link, $Name, $Type, $Visible;
    ///<summary></summary>
    public function __construct(){
        $this->ContentType=IGK_CT_PLAIN_TEXT;
        $this->Visible=false;
    }
    ///<summary></summary>
    public function getContent(){
        return $this->m_Data;
    }
    ///get data used
    public function getData(){
        if($this->Type == "Content")
            return chunk_split(base64_encode($this->m_Data), 76, IGK_CLF);
        $data="";
        if(file_exists($this->Link))
            $data=igk_io_read_allfile($this->Link);
        return chunk_split(base64_encode($data), 76, IGK_CLF);
    }
    ///<summary></summary>
    ///<param name="content"></param>
    public function setContent($content){
        $this->m_content=$content;
        return $this;
    }
}
