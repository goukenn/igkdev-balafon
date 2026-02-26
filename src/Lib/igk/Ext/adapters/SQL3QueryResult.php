<?php

namespace IGK\Ext\Adapters\SQLite3;

use Exception;
use IGK\Database\DbQueryResult;

/**
* Sqlite3result.
* @package IGK\Ext\Adapters\SQLite3
*/
class SQLite3Result extends DbQueryResult
{

    /**
    * Property: result.
    * @var mixed
    */
    private $m_result;

    /**
    * Property: info.
    * @var mixed
    */
    private $m_info;

    /**
    * Property: query.
    * @var mixed
    */
    private $m_query;

    /**
    * Property: columns.
    * @var mixed
    */
    private $m_columns;

    /**
    * Property: fetch.
    * @var mixed
    */
    private $m_fetch = false;

    /**
    * Property: rows.
    * @var mixed
    */
    private $m_rows = [];
    private function __construct() {}

    /**
    * To json.
    * @param null|mixed $option
    * @param int $flag
    */

    public function to_json($option = null, int $flag = 0)
    {
        igk_die('not implement ' . __METHOD__);
    }

    /**
    * Success.
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
    * To array.
    * @return ?array
    */

    public function to_array(): ?array
    {
        return $this->getRows();
    }

    /**
    * Creates Result.
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
    * Returns Row At Index.
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
    * Fetches.
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
    * Returns Columns.
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
