<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BooleanQueryResult.php
// @date: 20221118 01:57:01
// @desc: boolean query sql query result helper 
namespace IGK\System\Database\MySQL;
 
use IGK\System\Database\IDbResultType;
use IGKObject;

/**
 * is boolean result type 
 * @package IGK\System\Database\MySQL
 */
class BooleanQueryResult extends IGKObject implements IDbResultType{
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
    public function getValue(){
        return $this->m_result;
    }
    /**
     * get the result 
     * @return bool 
     */
    public function success(): bool{
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
    /**
     * query is result type
     * @return bool 
     */
    public function resultTypeIsBoolean():bool{
        return true;
    }
}