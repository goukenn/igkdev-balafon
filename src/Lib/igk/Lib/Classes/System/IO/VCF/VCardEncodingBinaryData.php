<?php
// @author: C.A.D. BONDJE DOUE
// @file: VCardEncodingBinaryData.php
// @date: 20250503 12:27:03
namespace IGK\System\IO\VCF;
/**
* 
* @package IGK\System\IO\VCF
* @author C.A.D. BONDJE DOUE
*/
class VCardEncodingBinaryData
{

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getValue()
    {
        return 'ENCODING=b:' . chunk_split(base64_encode($this->m_data), 76, "\n ");
    }
}