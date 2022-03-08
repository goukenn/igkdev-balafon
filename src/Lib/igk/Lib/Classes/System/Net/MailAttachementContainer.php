<?php
// @file: MailAttachementContainer.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Net;

use IGKObject;
use IIGKMailAttachmentContainer;

final class MailAttachementContainer extends IGKObject implements IIGKMailAttachmentContainer{
    private $m_files, $m_ids;
    ///<summary></summary>
    public function __construct(){
        $this->m_files=array();
    }
    ///<summary></summary>
    ///<param name="content"></param>
    ///<param name="contentType" default="IGK_CT_PLAIN_TEXT"></param>
    ///<param name="cid" default="null"></param>
    public function attachContent($content, $contentType=IGK_CT_PLAIN_TEXT, $cid=null){
        $attach=new MailAttachement();
        $attach->Content=$content;
        $attach->ContentType=$contentType;
        $attach->Type="Content";
        $attach->CID=$cid ? $cid: $this->generate_cid();
        $this->m_files[]=$attach;
        return $attach;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    ///<param name="contentType" default="IGK_CT_PLAIN_TEXT"></param>
    ///<param name="cid" default="null"></param>
    public function attachFile($file, $contentType=IGK_CT_PLAIN_TEXT, $cid=null){
        if(!file_exists($file))
            return null;
        $attach=new MailAttachement();
        $attach->Link=$file;
        $attach->ContentType=$contentType;
        $attach->Type="File";
        $attach->CID=$cid ? $cid: $this->generate_cid();
        $this->m_files[]=$attach;
        return $attach;
    }
    ///<summary></summary>
    private function generate_cid(){
        $this->m_ids++;
        return "idcall_".$this->m_ids;
    }
    ///<summary></summary>
    public function getList(){
        return $this->m_files;
    }
}
