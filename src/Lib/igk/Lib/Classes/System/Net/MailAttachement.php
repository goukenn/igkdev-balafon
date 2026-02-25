<?php
// @file: MailAttachement.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Net;
use IGKObject;
class MailAttachement extends IGKObject{
    private $m_content;
    var $CID, $ContentType, $Link, $Name, $Type, $Visible;
    /**
     * Constructor.
     */
    public function __construct(){
        $this->ContentType=IGK_CT_PLAIN_TEXT;
        $this->Visible=false;
    }
    /**
     * Gets the raw content of the attachment.
     *
     * @return string|null
     */
    public function getContent(){
        return $this->m_content;
    }
    ///get data used
    /**
     * Gets the base64-encoded data for the attachment.
     *
     * @return string|null
     */
    public function getData(){
        if($this->Type == "Content")
            return $this->m_content ? chunk_split(base64_encode($this->m_content), 76, IGK_CLF) : null;
        $data="";
        if(igk_io_file_exists($this->Link))
            $data=igk_io_read_allfile($this->Link);
        return chunk_split(base64_encode($data), 76, IGK_CLF);
    }
    /**
     * Sets the raw content of the attachment.
     *
     * @param string $content The content to set.
     * @return static
     */
    public function setContent($content){
        $this->m_content=$content;
        return $this;
    }
}
