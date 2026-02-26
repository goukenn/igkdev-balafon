<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbModelImporterMap.php
// @date: 20240918 16:38:56
namespace IGK\System\Database\Import;
use Exception;
use IGK\Database\DbColumnInfo;
use IGK\Models\ModelBase;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Database\DbReverseMappingLink;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\NotImplementException;
/**
 * 
 * @package IGK\System\Database\Import
 * @author C.A.D. BONDJE DOUE
 */
class DbModelImporterMap
{

    /**
    * auto generate doc.
    * @var mixed
    */
    const MappingClassSuffix = EntryClassResolution::ImportMappingSuffix;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $p_inserted;
    /**
     * autoregister link value
     * @var bool
     */
    var $autoregister;
    /**
     * value to transform field
     * @var mixed
     */
    var $transformField;
    /**
     * string
     * @var handle error
     */
    var $handleError;
    /**
     * 
     * @var ModelBase
     */
    private $m_model;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_reversal_definition;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_fieldListener;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_resolved_values;

    /**
    * auto generate doc.
    * @param ModelBase $model
    */
    public static function CreateFrom(ModelBase $model)
    {
        $n = $model::name();
        $cl = $model->getController()->resolveClass(sprintf(
            '%s\\%s%s',
            EntryClassResolution::ModelMappingNS,
            $n,
            self::MappingClassSuffix
        ));
        if ($cl && is_subclass_of($cl, self::class)) {
            return new $cl($model);
        }
        return new static($model);
    }
    /**
     * 
     * @param string $field_name 
     * @param null|callable $callable 
     * @return void 
     */

    public function addFieldListener(string $field_name, ?callable $callable)
    {
        if (is_null($callable)) {
            unset($this->m_fieldListener[$field_name]);
            return;
        }
        $this->m_fieldListener[$field_name] = $callable;
    }

    /**
    * .ctr
    * @param ModelBase $model
    */
    public function __construct(ModelBase $model)
    {
        $this->m_model = $model ?? igk_die('required model');
        $this->m_reversal_definition = DbUtility::GetReversalMappingLink($model, true);
        $this->m_fieldListener = [];
        $this->m_resolved_values = [];
        $this->autoregister = false;
        $this->p_inserted = 0;
        $this->transformField = true;
    }
    /**
     * 
     */

    public function __invoke($data)
    {
        $this->_onImportData((array)$data);
    }
    /**
     * 
     * @param array $data 
     * @return mixed raw
     * @throws Exception 
     */

    protected function _onImportData(array $data)
    {
        $cl = get_class($this->m_model);
        $tab = (array)$data;
        $row = null;
        if ($this->m_fieldListener) {
            foreach ($this->m_fieldListener as $key => $value) {
                if (key_exists($key, $tab)) {
                    $value($tab, $key, $this);
                }
            }
        }
        if ($this->transformField && $this->m_reversal_definition) {
            // + | retrieve link data 
            foreach ($this->m_reversal_definition as $k => $v) {
                if (key_exists($k, $tab)) {
                    // $this->_get_reversal_value();
                    $nv = $tab[$k];
                    $found = true;
                    $lv = $this->_resolveLinkValue($k, $v, $nv, $found);
                    if (!$found) return false;
                    $tab[$k] = $lv;
                }
            }
        }
        try {
            if (!$this->_onLoadData($tab, $cl, $row)) {
                if ($row = $cl::insertIfNotExists($tab)) {
                    if ($row === true) {
                        $irow = $cl::last();
                        $this->_onRowInserted($irow);
                        $this->p_inserted++;
                    }
                }
            }
        } catch (Exception $ex) {
            if ($this->handleError) {
                return null;
            }
            throw $ex;
        }
        return $row;
    }
    /**
     * override this to handle 
     * @param mixed $tab 
     * @param mixed $model_classe 
     * @return bool must return true to handle
     */

    protected function _onLoadData(array $data, string $model_classe, & $row):bool{
        return false;
    }
    /**
     * get number of inserted/exported db entries
     * @return int 
     */

    public function count()
    {
        return $this->p_inserted;
    }

    /**
    * auto generate doc.
    * @param ModelBase $model
    */
    protected function _onRowInserted(ModelBase $model) {}
    /**
     * 
     * @param string $columnName 
     * @return mixed 
     * @throws Exception 
     */

    protected function getReservalMapping(string $columnName)
    {
        if ($this->m_reversal_definition) {
            return igk_getv($this->m_reversal_definition, $columnName);
        }
        return null;
    }
    /**
     * resolve link data
     * @param string $column_name 
     * @param DbReverseMappingLink $v 
     * @param mixed $nv 
     * @param bool &$found 
     * @return mixed 
     * @throws Exception 
     */

    protected function _resolveLinkValue(string $column_name, DbReverseMappingLink $v,  $nv, &$found = true)
    {
        $found = true;
        $s = igk_getv($this->m_resolved_values, $column_name);
        if ($s && ($lv = igk_getv($s, $nv))) {
            return $lv;
        }
        if (!$s) {
            $s = [];
        }
        $found = false;
        $cond = new DbConditionExpressionBuilder(DbConditionExpressionBuilder::OP_OR);
        $row = null;
        if (is_numeric($nv)) {
            $row = $v->model->GetCache($column_name, $nv);
        } else {
            foreach ($v->columns as $tk => $ts) {
                if (DbColumnInfo::IsNumber($ts) && !is_numeric($nv)) {
                    continue;
                }
                $cond->add($tk, $nv);
            }
            $row = $v->model->select_row([$cond]);
        }
        if (!$row) {
            if (!$this->autoregister) {
                return false;
            } else {
                $r = $this->_registerColumn($v, $nv);
                $row = $v->model->insert($r);
            }
        }
        if (!$row) return false;
        $lv = $row->{$row->getPrimaryKey()};
        $s[$nv] = $lv;
        $this->m_resolved_values[$column_name] = $s; // update value
        $found = true;
        return $lv;
    }

    /**
    * auto generate doc.
    */
    function _get_reversal_value()
    {
        throw new NotImplementException(__METHOD__);
    }
    private function _registerColumn($v, $s)
    {
        $tab = [];
        foreach ($v->columns as $tk => $ts) {
            if ($ts->clAutoIncrement) continue;
            $tab[$tk] = $ts;
        }
        if (count($tab) == 1) {
            return [key($tab) => $s];
        }
        null;
    }
    /**
     * get mapping resolved values
     * @return array 
     */

    public function getResolvedValues()
    {
        return $this->m_resolved_values;
    }
    /**
     * export structured model data 
     * by default, select all model data
     * @return array 
     */

    public function export(): array{
        return $this->m_model->select_all();        
    }
}