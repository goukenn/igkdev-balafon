<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbLinkExpression.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database;

use IGKException;
use Exception;

class DbLinkExpression extends DbExpression{

    public $linkTable;
    public $columnName;
    public $columnValue;
    public $primaryColumn;

    public function __construct($linkTable, $columnName, $columnValue, $primaryColumn="clId"){
        parent::__construct("link.expression");
        $this->linkTable = $linkTable;
        $this->columnName = $columnName;
        $this->columnValue = $columnValue; 
        $this->primaryColumn = $primaryColumn;
    }
    /**
     * 
     * @param IGrammarOptions|object $grammarOptions 
     * @return null|string|void 
     * @throws IGKException 
     * @throws Exception 
     */
    public function getValue($grammarOptions=null){
        //link value 
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