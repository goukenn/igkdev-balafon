<?php

namespace IGK\Ext\Adapters\SQLite3;

use Exception;
use IGK\Database\DbQueryResult;

/**
* auto generate doc.
* @package IGK\Ext\Adapters\SQLite3
*/
class SQLite3Result extends DbQueryResult
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_result;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_info;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_query;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_columns;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_fetch = false;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_rows = [];
    private function __construct() {}

    /**
    * auto generate doc.
    * @param null|mixed $option
    * @param int $flag
    */

    public function to_json($option = null, int $flag = 0)
    {
        igk_die('not implement ' . __METHOD__);
    }

    /**
    * auto generate doc.
    * @return bool
    */

    public function success(): bool
    {
        return true;
    }
    /**
     * get rows definition 
     * @return null|iterable|array 
     */

    public function getRows()
    {
        return $this->m_rows;
    }

    /**
    * auto generate doc.
    * @return ?array
    */

    public function to_array(): ?array
    {
        return $this->getRows();
    }

    /**
    * auto generate doc.
    * @param mixed $result
    * @param mixed $query
    * @param mixed $info
    */

    public static function CreateResult($result, $query, $info)
    {
        $ri = new self;
        $ri->m_result = $result;
        $ri->m_query = $query;
        $ri->m_info = $info;
        $ri->m_columns = $ri->getColumns();
        return $ri;
    }

    /**
    * auto generate doc.
    * @param int $index
    */

    public function getRowAtIndex(int $index)
    {
        if (!$this->m_fetch) {
            $this->fetch();
        }
        if (count($this->m_rows) < $index) {
            while (($index > 0) && ($d = $this->fetch())) {
                $index--;
            }
            return $d;
        }
        return igk_getv($this->m_rows, $index);
    }
    /**
     * fetch all result
     * @return array 
     * @throws Exception 
     */

    public function fetch_all()
    {

        // fech all 
        while ($this->fetch());

        return $this->m_rows;
    }

    /**
    * auto generate doc.
    */

    public function fetch()
    {
        $this->m_fetch = true;
        $res = $this->m_result;
        $b = null;
        if ($res) {
            $b = igk_db_fetch_assoc($res);
            if ($b) {
                $this->m_rows[] = $b;
            }
        }
        return $b;
    }

    /**
    * auto generate doc.
    */

    public function getColumns()
    {
        $res = $this->m_result; //->res;
        if (is_null($this->m_columns)) {
            $g = igk_db_num_fields($res);
            $tb = [];
            while ($g > 0) {
                $cl = igk_sql3lite_fetch_field($res);
                $g--;
                if ($cl) {
                    $tb[$cl->name] = $cl;
                }
            }
            $this->m_columns = (object)$tb;
        }
        return $this->m_columns;
    }
}
