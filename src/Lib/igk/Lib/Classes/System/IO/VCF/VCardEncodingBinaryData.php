<?php
// @author: C.A.D. BONDJE DOUE
// @file: VCardEncodingBinaryData.php
// @date: 20250503 12:27:03
namespace IGK\System\IO\VCF;
/**
* auto generate doc.
* @package IGK\System\IO\VCF
* @author C.A.D. BONDJE DOUE
*/
class VCardEncodingBinaryData
{
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * .ctr
    * @param mixed $data
    */
    public function __construct($data)
    {
        $this->m_data  = $data;
    }
    /**
    * Returns Value.
    */
    public function getValue()
    {
        return 'ENCODING=b:' . chunk_split(base64_encode($this->m_data), 76, "\n ");
    }
}