<?php
// @file: MailAttachementContainer.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Net;
use IGKObject;
use IGK\IMailAttachmentContainer;
final class MailAttachementContainer extends IGKObject implements IMailAttachmentContainer{
    private $m_files, $m_ids;
    public function __construct(){
        $this->m_files=array();
    }
    public function attachContent($content, $contentType=IGK_CT_PLAIN_TEXT, $cid=null){
        $attach=new MailAttachement();
        $attach->Content=$content;
        $attach->ContentType=$contentType;
        $attach->Type="Content";
        $attach->CID=$cid ? $cid: $this->generate_cid();
        $this->m_files[]=$attach;
        return $attach;
    }
    public function attachFile($file, $contentType=IGK_CT_PLAIN_TEXT, $cid=null){
        if(!igk_io_file_exists($file))
            return null;
        $attach=new MailAttachement();
        $attach->Link=$file;
        $attach->ContentType=$contentType;
        $attach->Type="File";
        $attach->CID=$cid ? $cid: $this->generate_cid();
        $this->m_files[]=$attach;
        return $attach;
    }
    private function generate_cid(){
        $this->m_ids++;
        return "idcall_".$this->m_ids;
    }
    public function getList(){
        return $this->m_files;
    }
}