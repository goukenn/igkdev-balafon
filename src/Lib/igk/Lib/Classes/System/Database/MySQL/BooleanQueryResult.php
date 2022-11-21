<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BooleanQueryResult.php
// @date: 20221118 01:57:01
// @desc: boolean query sql query result helper 
namespace IGK\System\Database\MySQL;

class BooleanQueryResult{
    private $m_result;
    private $m_srcquery;
    public function __construct(bool $result, ?string $srcquery=null)
    {
        $this->m_result = $result;
        $this->m_srcquery = $srcquery;
    }
    public function success(){
        return $this->m_result;
    }
    public function getRowCount(){
        return 0;
    }
    public function getRows(){
        return [];
    }
}