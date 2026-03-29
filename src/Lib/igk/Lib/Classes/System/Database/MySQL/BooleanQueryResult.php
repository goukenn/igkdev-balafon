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
    /**
    * Property: result.
    * @var mixed
    */
    private $m_result;
    /**
    * Property: srcquery.
    * @var mixed
    */
    private $m_srcquery;
    /**
    * Property: last error.
    * @var mixed
    */
    private $m_last_error;
    /**
     * get stored last error 
     * @return mixed 
     */
    public function getLastError(){
        return $this->m_last_error;
    }
    /**
    * Returns Src Query.
    */
    public function getSrcQuery(){
        return $this->m_srcquery;
    }
    /**
    * get string presentation.
    */
    public function __toString(){
        return $this->m_result;
    }
    /**
    * .ctr
    * @param bool $result
    * @param null|string $srcquery
    * @param null|string $last_error
    */
    public function __construct(bool $result, ?string $srcquery=null, ?string $last_error=null)
    {
        $this->m_result = $result;
        $this->m_srcquery = $srcquery; 
        $this->m_last_error = $last_error;
    }
    /**
    * Returns Value.
    */
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
    /**
    * Returns Row Count.
    */
    public function getRowCount(){
        return 0;
    }
    /**
    * Returns Rows.
    */
    public function getRows(){
        return [];
    }
    /**
    * Returns Row At Index.
    * @param int $index
    */
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