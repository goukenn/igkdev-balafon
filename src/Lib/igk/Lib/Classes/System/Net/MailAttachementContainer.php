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

/**
* auto generate doc.
* @package IGK\System\Net
*/
final class MailAttachementContainer extends IGKObject implements IMailAttachmentContainer{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_files, $m_ids;
    /**
     * Constructor.
     */

    public function __construct(){
        $this->m_files=array();
    }
    /**
     * Attaches raw content as a mail attachment.
     *
     * @param string      $content     The content to attach.
     * @param string      $contentType The MIME content type.
     * @param string|null $cid         Optional content ID.
     * @return MailAttachement
     */

    public function attachContent($content, $contentType=IGK_CT_PLAIN_TEXT, $cid=null){
        $attach=new MailAttachement();
        $attach->Content=$content;
        $attach->ContentType=$contentType;
        $attach->Type="Content";
        $attach->CID=$cid ? $cid: $this->generate_cid();
        $this->m_files[]=$attach;
        return $attach;
    }
    /**
     * Attaches a file as a mail attachment.
     *
     * @param string      $file        The path to the file to attach.
     * @param string      $contentType The MIME content type.
     * @param string|null $cid         Optional content ID.
     * @return MailAttachement|null
     */

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
    /**
     * Generates a unique content ID for an attachment.
     *
     * @return string
     */
    private function generate_cid(){
        $this->m_ids++;
        return "idcall_".$this->m_ids;
    }
    /**
     * Returns the list of attachments.
     *
     * @return array
     */

    public function getList(){
        return $this->m_files;
    }
}
