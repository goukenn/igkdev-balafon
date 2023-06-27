<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BooleanQueryResult.php
// @date: 20221118 01:57:01
// @desc: boolean query sql query result helper 
namespace IGK\System\Database\MySQL;

class BooleanQueryResult{
    private $m_result;
    private $m_srcquery;
    private $m_last_error;

    /**
     * get stored last error 
     * @return mixed 
     */
    public function getLastError(){
        return $this->m_last_error;
    }
    public function getSrcQuery(){
        return $this->m_srcquery;
    }

    public function __toString(){
        return $this->m_result;
    }
    public function __construct(bool $result, ?string $srcquery=null, ?string $last_error=null)
    {
        $this->m_result = $result;
        $this->m_srcquery = $srcquery; 
        $this->m_last_error = $last_error;
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
    public function getRowAtIndex(int $index){
        if ($index != 0)
            igk_die('not available');
        return [$this->success()];
    }
}