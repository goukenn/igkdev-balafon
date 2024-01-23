<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlDocMetadataTrait.php
// @date: 20231221 14:20:45
namespace IGK\System\Html\Metadatas\Traits;

use IGK\System\Html\Metadatas\MetaDataHost;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas\Traits
*/
trait HtmlDocMetadataTrait{
    private $m_metadata;
    /**
     * get method data binding 
     * @return mixed 
     */
    public function getMetadatas(){
        if (is_null($this->m_metadata)){
            $this->m_metadata = new MetaDataHost($this);
        }
        return $this->m_metadata;
    }
}