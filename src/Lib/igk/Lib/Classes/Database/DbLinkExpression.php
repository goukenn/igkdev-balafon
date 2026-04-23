<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbLinkExpression.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Database;
use IGKException;
use Exception;

/**
* Db link expression.
* @package IGK\Database
*/
class DbLinkExpression extends DbExpression{
    /**
    * Map of link table.
    * @var mixed
    */
    public $linkTable;
    /**
    * Name of column name.
    * @var mixed
    */
    public $columnName;
    /**
    * Property: column value.
    * @var mixed
    */
    public $columnValue;
    /**
    * Property: primary column.
    * @var mixed
    */
    public $primaryColumn;
    /**
    * .ctr
    * @param mixed $linkTable
    * @param mixed $columnName
    * @param mixed $columnValue
    * @param mixed $primaryColumn
    */
    public function __construct($linkTable, $columnName, $columnValue, $primaryColumn="clId"){
        parent::__construct("link.expression");
        $this->linkTable = $linkTable;
        $this->columnName = $columnName;
        $this->columnValue = $columnValue; 
        $this->primaryColumn = $primaryColumn;
    }
    /**
    * auto generate doc.
    * @param IGrammarOptions|object $grammarOptions
    * @return null|string|void
    */
    public function getValue($grammarOptions=null){
        if ($grammarOptions==null){
            if (igk_environment()->isDev()){
                igk_trace();
                igk_wln_e("grammar is null value ::: ", $grammarOptions);
            }
            return null;
        }        
        switch($grammarOptions->type){
            case "where":
                return "`{$grammarOptions->column}`=(SELECT {$this->primaryColumn} FROM {$this->linkTable} where {$this->columnName}='{$this->columnValue}')";
            case "insert":
                return "(SELECT {$this->primaryColumn}  FROM {$this->linkTable} where {$this->columnName}='{$this->columnValue}')";
        } 
    }
}