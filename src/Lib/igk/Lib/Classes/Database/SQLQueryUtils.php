<?php
// @file: IGKSQLQueryUtils.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Database;

use IGK\System\Html\IHtmlGetValue;
use IGK\System\Database\QueryBuilderConstant as queryConstant;
use IGKException;
use IGKQueryResult;
use IGKSysUtil;
/**
 * 
 * @package IGK\Database
 * @method object sendQuery() send query
 * @method object lastId() get last id
 * @method object dieNotConnect() if query no ok die
 */
class SQLQueryUtils{
    const AVAIL_FUNC=['IGK_PASSWD_ENCRYPT', 'AES_ENCRYPT', 'BIN', 'CHAR', 'COMPRESS', 'CURRENT_USER', 'AES_DECRYPTDATABASE', 'DAYNAME', 'DES_DECRYPT', 'DES_ENCRYPT', 'ENCRYPT', 'HEX', 'INET6_NTOA', 'INET_NTOA', 'LOAD_FILE', 'LOWER', 'LTRIM', 'MD5', 'MONTHNAME', 'OLD_PASSWORD', 'PASSWORD', 'QUOTE', 'REVERSE', 'RTRIM', 'SHA1', 'SOUNDEX', 'SPACE', 'TRIM', 'UNCOMPRESS', 'UNHEX', 'UPPER', 'USER', 'UUID', 'VERSION', 'ABS', 'ACOS', 'ASCII', 'ASIN', 'ATAN', 'BIT_COUNT', 'BIT_LENGTH', 'CEILING', 'CHAR_LENGTH', 'CONNECTION_ID', 'COS', 'COT', 'CRC32', 'CURRENT_DATE', 'CURRENT_TIME', 'DATE', 'DAYOFMONTH', 'DAYOFWEEK', 'DAYOFYEAR', 'DEGREES', 'EXP', 'FLOOR', 'FROM_DAYS', 'FROM_UNIXTIME', 'HOUR', 'INET6_ATON', 'INET_ATON', 'LAST_DAY', 'LENGTH', 'LN', 'LOG', 'LOG10', 'LOG2', 'MICROSECOND', 'MINUTE', 'MONTH', 'NOW', 'OCT', 'ORD', 'PI', 'QUARTER', 'RADIANS', 'RAND', 'ROUND', 'SECOND', 'SEC_TO_TIME', 'SIGN', 'SIN', 'SQRT', 'SYSDATE', 'TAN', 'TIME', 'TIMESTAMP', 'TIME_TO_SEC', 'TO_DAYS', 'TO_SECONDS', 'UNCOMPRESSED_LENGTH', 'UNIX_TIMESTAMP', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 'UUID_SHORT', 'WEEK', 'WEEKDAY', 'WEEKOFYEAR', 'YEAR', 'YEARWEEK'];
    protected static $sm_adapter;
    public static $LENGTHDATA=array("varchar"=>"VarChar", "char"=>"Char");
    ///<summary>Represente AllowedDefValue function</summary>
    public static function AllowedDefValue(){
        static $defvalue=null;
        if($defvalue === null){
            $defvalue=["TIMESTAMP"=>["CURRENT_TIMESTAMP"=>1, "NOW()"=>"CURRENT_TIMESTAMP", "NULL"=>1], "DATETIME"=>["CURRENT_TIMESTAMP"=>1, "NOW()"=>1, "CURDATE"=>1, "CURTIME()"=>1, "NULL"=>1], "JSON"=>["{}"=>"(JSON_OBJECT())", "[]"=>"((JSON_ARRAY())"]];
        }
        return $defvalue;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="columninfo"></param>
    ///<param name="desc" default="null"></param>
    ///<param name="noengine"></param>
    ///<param name="nocomment"></param>
    /**
     * 
     * @param mixed $tbname 
     * @param mixed $columninfo 
     * @param mixed $desc 
     * @param mixed $adapter 
     * @param int $noengine 
     * @param int $nocomment 
     * @return string 
     * @throws IGKException 
     * @deprecated use SQLGrammar 
     */
    public static function CreateTableQuery($tbname, $columninfo, $desc=null, $adapter=null, $noengine=0, $nocomment=0){
        $query="CREATE TABLE IF NOT EXISTS `".igk_mysql_db_tbname($tbname)."`(";
        $tb=false;
        $primary="";
        $unique="";
        $funique="";
        $findex="";
        $fautoindex="";
        $uniques=array();
        $primkey="";
        $tinf=array();
        $defvalue=self::AllowedDefValue();
        foreach($columninfo as $v){
            if(($v == null) || !is_object($v)){
                igk_die(__CLASS__." :::Error table column info is not an object error for ".$tbname);
            }
            $primkey="noprimkey://".$v->clName;
            if($tb)
                $query .= ",";
            $v_name=igk_db_escape_string($v->clName);
            $query .= "`".$v_name."` ";
            $type=igk_getev(static::ResolvType($v->clType), "Int");
            if($adapter && !$adapter->isTypeSupported($type)){
                $type=static::fallbackType($type, $adapter);
            }
            $query .= igk_db_escape_string($type);
            $s=strtolower($type);
            $number=false;
            if(isset(self::$LENGTHDATA[$s])){
                if($v->clTypeLength > 0){
                    $number=true;
                    $query .= "(".igk_db_escape_string($v->clTypeLength).")";
                }
            }
            else if($type == "Enum"){
                $query .= "(". implode(",", array_map(function($i){
                    return "'".igk_db_escape_string($i)."'";
                }
                , array_filter(explode(',', $v->clEnumValues), function($c){
                    return (strlen(trim($c)) > 0);
                }))).")";
            }
            $query .= " ";
            if($v->IsUnsigned()){
                $query .= "unsigned ";
            }
            if(!$number){
                if(($v->clNotNull) || ($v->clAutoIncrement))
                    $query .= "NOT NULL ";
                else
                    $query .= "NULL ";
            }
            else if($v->clNotNull){
                $query .= "NOT NULL ";
            }
            if($v->clAutoIncrement){
                $query .= DbQueryDriver::GetValue("auto_increment_word", $v, $tinf)." ";
                if($idx=igk_getv($v, "clAutoIncrementStartIndex")){
                    $fautoindex=DbQueryDriver::GetValue("auto_increment_word", $v, $tinf)."={$idx} ";
                }
            }
            $tb=true;
            if ($v->clDefaultNull){
                $query .= "DEFAULT NULL ";
            }else if(static::supportDefaultValue($type) && $v->clDefault || $v->clDefault === '0'){
                $_ktype=strtoupper($type);
                $_def=
                $r_v=isset($defvalue[$_ktype][$v->clDefault]) ? (is_int($defvalue[$_ktype][$v->clDefault]) ? $v->clDefault: $defvalue[$_ktype][$v->clDefault]): "'".igk_db_escape_string($v->clDefault)."'";
                $query .= "DEFAULT {$_def} ";
                if($r_v && $v->clUpdateFunction){
                    $_def=!isset($defvalue[$_ktype][$v->clUpdateFunction]) ? $v->clDefault: "".igk_db_escape_string($v->clUpdateFunction)."";
                    $query .= " ON UPDATE {$_def}";
                }
            }
            if($v->clDescription && !$nocomment){
                $query .= " COMMENT '".igk_db_escape_string($v->clDescription)."' ";
            }
            if($v->clIsUnique){
                if(!empty($unique))
                    $unique .= ",";
                $unique .= "UNIQUE KEY `".$v_name."` (`".$v_name."`)";
            }
            if($v->clIsUniqueColumnMember){
                if(isset($v->clColumnMemberIndex)){
                    $tindex=explode("-", $v->clColumnMemberIndex);
                    $indexes=array();
                    foreach($tindex as $kindex){
                        if(!is_numeric($kindex) || isset($indexes[$kindex]))
                            continue;
                        $indexes[$kindex]=1;
                        $ck='unique_'. $kindex;
                        $bf="";
                        if(!isset($uniques[$ck])){
                            $bf .= "UNIQUE KEY `clUC_".$ck."_index`(`".$v_name."`";
                        }
                        else{
                            $bf=$uniques[$ck];
                            $bf .= ", `".$v_name."`";
                        }
                        $uniques[$ck]=$bf;
                    }
                }
                else{
                    if(empty($funique)){
                        $funique="UNIQUE KEY `clUnique_Column_".$v_name."_index`(`".$v_name."`";
                    }
                    else
                        $funique .= ", `".$v_name."`";
                }
            }
            if($v->clIsPrimary && !isset($tinf[$primkey])){
                if(!empty($primary))
                    $primary .= ",";
                $primary .= "`".$v_name."`";
            }
            if ($v->clIsIndex ){

                if( ($v->clLinkType) && !$v->clIsUnique && !$v->clIsUniqueColumnMember && $v->clIsPrimary){
                    if(!empty($findex))
                    $findex .= ",";
                    $findex .= "KEY `".$v_name."_index` (`".$v_name."`)";
                }
            }
            unset($tinf[$primkey]);
        }
        if(!empty($primary)){
            $query .= ", PRIMARY KEY  (".$primary.") ";
        }
        if(!empty($unique)){
            $query .= ", ".$unique." ";
        }
        if(!empty($funique)){
            $funique .= ")";
            $query .= ", ".$funique." ";
        }
        if(igk_count($uniques) > 0){
            foreach($uniques as $v){
                $v .= ")";
                $query .= ", ".$v." ";
            }
        }
        if(!empty($findex))
            $query .= ", ".$findex;
        $query .= ")";
        if(!$noengine)
            $query .= ' ENGINE=InnoDB';
        if(!empty($fautoindex)){
            $query .= " ". $fautoindex;
        }
        if($desc){
            $query .= " COMMENT='".igk_db_escape_string($desc)."' ";
        }
        $query=rtrim($query).";";
        return $query;
    }
    ///<summary>Represente fallbackType function</summary>
    ///<param name="t"></param>
    ///<param name="adapter"></param>
    public static function fallbackType($t, $adapter){
        switch(strtolower($t)){
            case "json":
            if($adapter->isTypeSupported('longtext')){
                return "longtext";
            }
            break;
            case "date":
            if($adapter->isTypeSupported('datetime')){
                return "datetime";
            }
            break;
        }
        return "text";
    }
    ///<summary>get column query definition</summary>
    public static function GetColumnDefinition($v, $nocomment=0){
        $query="";
        $type=igk_getev($v->clType, "Int");
        $query .= igk_db_escape_string($type);
        $s=strtolower($type);
        $number=false;
        if(isset(self::$LENGTHDATA[$s])){
            if($v->clTypeLength > 0){
                $number=true;
                $query .= "(".igk_db_escape_string($v->clTypeLength).") ";
            }
            else
                $query .= " ";
        }
        else
            $query .= " ";
        if(!$number){
            if(($v->clNotNull) || ($v->clAutoIncrement))
                $query .= "NOT NULL ";
            else
                $query .= "NULL ";
        }
        else if($v->clNotNull){
            $query .= "NOT NULL ";
        }
        if($v->clAutoIncrement){
            $query .= DbQueryDriver::GetValue("auto_increment_word", $v, $tinf)." ";
        }
        $tb=true;
        if($v->clDefault || $v->clDefault === '0'){
            $query .= "DEFAULT '".igk_db_escape_string($v->clDefault)."' ";
        }
        if($v->clDescription && !$nocomment){
            $query .= "COMMENT '".igk_db_escape_string($v->clDescription)."' ";
        }
        return $query;
    }
    ///<summary></summary>
    ///<param name="options"></param>
    ///<param name="tbname"></param>
    public static function GetColumnList($options, $tbname){
        die(__METHOD__.":: obselete : use GetExtrasOptions instead");
    }
    ///<summary>get query condition string</summary>
    ///<param name="tab"></param>
    ///<param name="operator" default="'AND'"></param>
    public static function GetCondString($tab, $operator='AND', $adapter=null, $grammar=null){
        $query="";
        $t=0;
        $fc="getValue";
        $to="obj:type";
        $grammar=$grammar ?? new static();
        if($adapter === null){
            $adapter=igk_db_current_data_adapter();
            if(!$adapter){
                die("adapter not found");
            }
        }
        $op=$adapter->escape_string($operator);
        $c_exp="IS NULL";
        if(is_numeric($tab)){
            return "`clId`='{$tab}'";
        }
        if(is_object($tab)){
            if(get_class($tab) instanceof DbExpression){
                return $tab->getValue($grammar);
            }
        }
        $qtab=[["tab"=>$tab, "operator"=>$op, "query"=>& $query]];
        $loop=0;
        $tquery=[];
        while($ctab=array_shift($qtab)){
            if(!$loop){
                $loop=1;
            }
            else{
                $t=0;
            }
            $tab=$ctab["tab"];
            $op=$ctab["operator"];
            $query=& $ctab["query"];
            $tquery[]=& $query;
            foreach($tab as $k=>$v){
                $c="=";
                if(is_object($v)){
                    if($v instanceof DbExpression){
                        if($t == 1)
                            $query .= " $op ";
                        $query .= $v->getValue((object)["grammar"=>$grammar, "type"=>"where", "column"=>$k]);
                        $t=1;
                        continue;
                    }
                    $tb=igk_get_robjs("operand|conditions", 0, $v);
                    if($tb->operand && $tb->conditions && preg_match("/(or|and)/i", $tb->operand)){
                        if($t){
                            $t=0;
                        }
                        array_unshift($qtab, ["tab"=>$tb->conditions, "operator"=>strtoupper($tb->operand)]);
                        continue;
                    }
                }
                if($t == 1)
                    $query .= " $op ";
                if(is_object($v)){
                    $query .= "`".igk_obj_call($v, $fc)."`";
                }
                else{
                    if(preg_match("/^(!|@@|@&|(<|>)=?|#|\||&)/", $k, $tab)){
                        $ch=substr($k, 0, $ln=strlen($tab[0]));
                        $k=substr($k, $ln);
                        switch($ch){
                            case '!':
                            $c="!=";
                            $c_exp="IS NOT NULL";
                            break;
                            case "@@";
                            $c=" Like ";
                            break;
                            case "@&":
                            $query .= "(".self::GetKey($k, $adapter)." & ".$adapter->escape_string($v).") = ".$adapter->escape_string($v);
                            $t=1;
                            continue 2;
                            break;default: 
                            $c=$ch;
                            break;
                        }
                    }
                    $query .= self::GetKey($k, $adapter);
                    if($v !== null){
                        if(is_array($v)){
                            $query .= $c.implode(" ", $v);
                        }
                        else{
                            $query .= "{$c}'".$adapter->escape_string($v)."'";
                        }
                    }
                    else
                        $query .= " ".$c_exp;
                }
                $t=1;
            }
        }
        $tquery=array_filter($tquery);
        if(count($tquery) > 1){
            $query="(".implode(") {$operator} (", $tquery).")";
        }
        return $query;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    public static function GetDeleteQuery($tbname, $values){
        $query="";
        $query .= "DELETE FROM `".igk_mysql_db_tbname($tbname)."`";
        if(is_numeric($values)){
            return $query. " WHERE `clId`={$values}";
        }
        if(is_array($values)){
            $query .= " WHERE ". self::GetCondString($values);
        }
        else{
            if(is_string("values")){
                $query .= " WHERE ".igk_db_escape_string($values);
            }
            else{
                $id=igk_getv($values, IGK_FD_ID);
                if($id){
                    $query .= " WHERE `clId`='".igk_db_escape_string($id)."'";
                }
            }
        }
        return $query;
    }
    ///<summary>Represente GetExpressQuery function</summary>
    ///<param name="express"></param>
    ///<param name="tinf"></param>
    private static function GetExpressQuery($express, $tinf){
        $b=explode(".", $express);
        $sl=[$b[0]=>$b[1]];
        if($b=self::GetSelectQuery(self::$sm_adapter, $tinf->clLinkType, $sl, ["Columns"=>[$tinf->clLinkColumn ?? IGK_FD_ID]])){
            return 
            $b="(".rtrim(trim($b), ";").")";
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="options"></param>
    public static function GetExtraOptions($options, $ad){
        $defOrder="ASC";
        $q="";
        $options=!is_object($options) ? (object)$options: $options;
        $optset=[];
        $columns="*";
        $query="";
        $flag="";
        $join="";
        $_express=function($v, & $query) use ($defOrder){
            $a=0;
            foreach($v as $s){
                $s_t=explode("|", $s);
                if($a)
                    $query .= ",";
                $query .= $s_t[0]. " ".strtoupper(igk_getv($s_t, 1, $defOrder));
                $a=1;
            }
        };
        $_buildjoins=function($v, & $join){
            if(!is_array($v)){
                die("join options not an array");
            }
            foreach($v as $m){
                $t="INNER JOIN";
                if(!is_array($m)){
                    die("expected array list in joint: ".$m);
                }
                $tab=array_keys($m)[0];
                $vv=array_values($m)[0];
                if(isset($vv["type"])){
                    $t=$vv["type"];
                }
                $join .= $t. " ";
                $join .= $tab;
                if(isset($vv[0]))
                    $join .= " on (".$vv[0].") ";
            }
        };
        foreach(igk_array_extract($options, "Distinct|GroupBy|OrderBy|OrderByField|Columns|Limit|Joins") as $k=>$v){
            if(!$v)
                continue;
            switch($k){
                case queryConstant::Distinct:
                $flag .= "DISTINCT ";
                break;
                case queryConstant::Limit:$optset[$k]=1;
                $h=1;
                if(is_array($v)){
                    if(isset($v["start"]) && isset($v["end"])){
                        $s=$v["start"];
                        $e=$v["end"];
                        $h=$s.", ".$e;
                    }
                    else if(count($v) == 1){
                        $h=$v[0];
                    }
                    else if(count($v) == 2){
                        $h=$v[0].",".$v[1];
                    }
                }
                else{
                    if(is_numeric($v))
                        $h=$v;
                }
                $query .= " Limit ".$h;
                break;
                case queryConstant::Joins:
                $_buildjoins($v, $join);
                break;
                case queryConstant::GroupBy:$optset[$k]=1;
                if($ad->supportGroupBy()){
                    $query .= " GROUP BY ";
                    $a=0;
                    foreach($v as $s){
                        $s_t=explode("|", $s);
                        if($a)
                            $query .= ",";
                        $query .= $s_t[0];
                        $a=1;
                    }
                }
                break;
                case "OrderByField":
                break;
                case "OrderBy":
                if(is_array($v)){
                    $torder="";
                    $c="";
                    foreach($v as $s){
                        $g=explode("|", $s);
                        $type=igk_getv($g, 1, $defOrder);
                        $c=self::Key($g[0], $ad, "".$type.", ");
                        if(!empty($torder))
                            $torder .= ", ";
                        $torder .= $c." ".$type;
                    }
                    $torder .= " ";
                    $optset[$k]=$torder;
                }
                else{
                    igk_die("OrderBy must be an array ['Colum,...|Type']");
                }
                break;
                case "Columns":
                $a=0;
                $columns="";
                foreach($v as $s){
                    if($a){
                        $columns .= ", ";
                    }
                    if(is_string($s)){
                        $columns .= $ad->escape_string($s);
                    }
                    else if(is_object($s)){
                        if($s instanceof DbExpression){
                            $columns .= $s->getValue();
                        }
                        else{
                            throw new IGKException(__("objet not a DB Expression"));
                        }
                    }
                    else if(isset($s["key"])){
                        $columns .= $ad->escape_string($s["key"]);
                    }
                    else if(isset($s["func"]) && isset($s["args"])){
                        if(is_array($s["args"])){
                            $columns .= $s["func"]. "(". implode(', ', $s["args"]). ")";
                        }
                        else{
                            $columns .= $s["func"]. "(". $s["args"]. ")";
                        }
                    }
                    else if(is_array($s) && (count($s) == 1) && is_string($s[0])){
                        $columns .= $s[0];
                    }
                    if($c=igk_getv($s, "as")){
                        $columns .= " As ".$c;
                    }
                    $a=1;
                }
                break;
            }
        }
        if(!isset($optset["OrderBy"])){
            if(isset($options->Sort) && isset($options->SortColumn)){
                $v=strtoupper($options->Sort);
                if(strpos("ASC|DESC", $v) !== false){
                    $q .= " ORDER BY `".igk_db_escape_string($options->SortColumn)."` ".$v;
                    $optset["OrderBy"]=1;
                }
            }
            else{
                if(isset($options->SortColumn) && @is_array($options->SortColumn)){
                    foreach($options->SortColumn as $r=>$v){
                        $v=strtoupper($v);
                        if(strpos("ASC|DESC", $v) !== false){
                            $q .= " ORDER BY `".igk_db_escape_string($r)."` ".$v;
                            $optset["OrderBy"]=1;
                        }
                    }
                }
            }
        }
        else{
            $q .= "ORDER BY ".$optset["OrderBy"];
        }
        if(!isset($optset["Limit"])){
            if(is_numeric($limit=igk_getv($options, "Limit"))){
                $lim=igk_db_escape_string($limit);
                if(is_numeric($offset=igk_getv($options, "LimitOffset"))){
                    $lim=igk_db_escape_string($offset).", ".$lim;
                }
                $q .= " Limit ".$lim;
            }
        }
        return (object)["columns"=>$columns, "join"=>$join, "extra"=>$q. $query, "flag"=>$flag];
    }
    ///<summary></summary>
    ///<param name="b"></param>
    public static function GetFCN($b){
        return strtoupper($b);
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="tableInfo" default="null"></param>
    public static function GetInsertQuery($tbname, $values, $tableInfo=null){
        if(!$values)
            return null;
        $rtbname=igk_mysql_db_tbname($tbname);
        $query="INSERT INTO `".$rtbname."`(";
        $v_v="";
        $v_c=0;
        $tvalues=self::GetValues($values, $tableInfo);
        foreach($tvalues as $k=>$v){
            if($v_c != 0){
                $query .= ",";
                $v_v .= ",";
            }
            else
                $v_c=1;
            $query .= "`".igk_db_escape_string($k)."`";
            if($tableInfo){
                $v_v .= self::GetValue($rtbname, $tableInfo, $k, $v);
            }
            else{
                if($v === null){
                    $v_v .= "NULL ";
                }
                else if(is_object($v) && method_exists($v, "getValue")){
                    $v_v .= "".$v->getValue();
                }
                else
                    $v_v .= "'".igk_db_escape_string($v)."'";
            }
        }
        $query .= ") VALUES (".$v_v.");";
     
        return $query;
    }
    ///<summary>Represente GetKey function</summary>
    ///<param name="k"></param>
    ///<param name="adapter"></param>
    private static function GetKey($k, $adapter){
        return "`".implode("`.`", array_map([$adapter, "escape_string"], explode(".", $k)))."`";
    }
    ///<summary>get sql select query</summary>
    public static function GetSelectQuery($ad, $tbname, $where=null, $options=null){
        $q="";
        if($options == null){
            $options=igk_db_create_opt_obj();
        }
        else if(is_callable($options)){
            $g=igk_db_create_opt_obj();
            $c=IGKQueryResult::CALLBACK_OPTS;
            $g->$c=$options;
            $options=$g;
        }
        if($where != null){
            if(!is_numeric($where) && is_string($where)){
                $q .= " WHERE ".$where;
            }
            else{
                $operand=igk_getv($options, "Operand", "AND");
                $q .= " WHERE ".SQLQueryUtils::GetCondString($where, $operand, $ad);
            }
        }
        $tq=SQLQueryUtils::GetExtraOptions($options, $ad);
        $column=$tq->columns;
        if(!empty($tq->join)){
            $q=" ".$tq->join. " ".$q;
        }
        if(isset($tq->extra)){
            $q .= " ".$tq->extra;
        }
        $flag="";
        if(igk_environment()->querydebug){
            $flag = igk_getv($tq, "flag");
            igk_dev_wln_e(__FILE__.":".__LINE__,  "flag : ".$flag);
        }
        $q="SELECT {$flag}{$column} FROM `".igk_mysql_db_tbname($tbname)."`".rtrim($q).";";
        return $q;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="condition" default="null"></param>
    ///<param name="tableInfo" default="null"></param>
    public static function GetUpdateQuery($tbname, $values, $condition=null, $tableInfo=null){
        $rtbname=igk_mysql_db_tbname($tbname);
        $out="";
        $out .= "UPDATE `".$rtbname."` SET ";
        $t=0;
        $v_condstr="";
        $id=$condition == null ? igk_getv($values, IGK_FD_ID): null;
        if(($id == null) && is_integer($condition)){
            $id=$condition;
        }
        $tableInfo=$tableInfo ??  DbSchemas::GetTableColumnInfo($tbname);
        $tvalues=self::GetValues($values, $tableInfo, 1);
        foreach($tvalues as $k=>$v){
            if($id && ($k == IGK_FD_ID) || (strpos($k, ":") !== false))
                continue;
            if($t == 1)
                $out .= ",";
            if($tableInfo){
                $out .= "`".igk_db_escape_string($k)."`=".self::GetValue($rtbname, $tableInfo, $k, $v, "u");
            }
            else{
                $out .= "`".igk_db_escape_string($k)."`=";
                if(!empty($v) && is_integer($v)){
                    $out .= $v;
                }
                else
                    $out .= "'".igk_db_escape_string($v). "'";
            }
            $t=1;
        }
        
        if($condition){
            if(is_array($condition)){
                $v_condstr .= self::GetCondString($condition);
            }
            else if(is_string($condition) && !preg_match(\IGK\System\Regex\RegexConstant::INT_REGEX, $condition))
                $v_condstr .= $condition;
            else if(is_integer($condition) || preg_match(\IGK\System\Regex\RegexConstant::INT_REGEX, $condition))
                $v_condstr .= "`clId`='".igk_db_escape_string($condition)."'";
            else{
                igk_dev_wln("data is ".$condition. " ".strlen($condition). " ::".is_integer((int)$condition));
            }
        }
        else if($id){
            $v_condstr .= "`clId`='".igk_db_escape_string($id)."'";
        }
        if(!empty($v_condstr)){
            $out .= " WHERE ".$v_condstr;
        }
        return $out;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="tableInfo"></param>
    ///<param name="columnName"></param>
    ///<param name="value"></param>
    ///<param name="type" default="i"></param>
    public static function GetValue($tbname, $tableInfo, $columnName, $value, $type="i"){
        $tinf=igk_getv($tableInfo, $columnName);
        $def=static::AllowedDefValue();
        if($tinf === null){
            igk_die("can't get column: {$columnName} info in table: {$tbname}");
        }
        if(!empty($tinf->clLinkType) && is_string($value) && (strpos($value, ".") !== false)){
            if($v=self::GetExpressQuery($value, $tinf)){
                return $v;
            }
        }
        if((is_integer($value))){
            if(($value === 0) && !empty($tinf->clLinkType) && !$tinf->clNotNull){
                return 'NULL';
            }
            if(($value === 0) && !empty($tinf->clLinkType) && $tinf->clNotNull){
                if($express=$tinf->clDefaultLinkExpression){
                    if($v=self::GetExpressQuery($express, $tinf)){
                        return $v;
                    }
                }
            }
            if($tinf->clType == "Enum"){
                return "'".igk_db_escape_string($value)."'";
            }
            return $value;
        }
        $of='NULL';
        if(($type == "i") && $tinf->clInsertFunction){
            $of=$tinf->clInsertFunction;
        }
        else if(($type != "i") && $tinf->clUpdateFunction){
            $of=$tinf->clUpdateFunction;
        }
        if(($value === null) || ($value == $tinf->clDefault) || (($value !== '0') && empty($value))){
            if($tinf->clNotNull){
                if($tinf->clDefault !== null){
                    if(is_integer($tinf->clDefault)){
                        return $tinf->clDefault;
                    }
                    else{
                        if(self::IsAllowedDefValue($def, $tinf->clType, $tinf->clDefault)){
                            return $tinf->clDefault;
                        }
                        return "'".igk_db_escape_string($tinf->clDefault)."'";
                    }
                }
                switch(strtolower($tinf->clType)){
                    case 'int':
                    case 'integer':
                    case 'float':
                    case 'double':
                    if(!$tinf->clNotNull){
                        return 'NULL';
                    }
                    return "0";
                    case "datetime":
                    case "date":
                    case "time":
                    return "NOW()";default: 
                    if(is_string($value)){
                        return "''";
                    }
                    return igk_str_format($of, $value);
                }
            }
            if(preg_match("/(date(time)?|timespan)/i", $tinf->clType)){
                if(strtolower($of) == 'now()'){
                    switch(strtolower($tinf->clType)){
                        case "datetime":
                        case "timespan":
                        return "'".igk_db_escape_string(igk_mysql_datetime_now())."'";
                        case "date":
                        return "'".igk_db_escape_string(date("Y-m-d"))."'";
                        case "time":
                        return "'".igk_db_escape_string(date("H:i:s"))."'";
                    }
                }
                if($value === 'NULL'){
                    $value=null;
                }
                if($tinf->clDefault && self::IsAllowedDefValue($def, $tinf->clType, $tinf->clDefault)){
                    return $tinf->clDefault;
                }
            }
            if($of != 'NULL'){
                $gt=explode("(", $of);
                $pos=strtoupper(array_shift($gt));
                if(!$tinf->clNotNull){
                    if(in_array($pos, self::AVAIL_FUNC)){
                        return igk_str_format($of, $value);
                    }
                }
            }
            if($value && ($value == $tinf->clDefault)){
                return "'".igk_db_escape_string($value)."'";
            }
            return 'NULL';
        }
        if(empty($value)){
            if(!$tinf->clNotNull || ($tinf->clAutoIncrement && strtolower($tinf->clType) == 'int'))
                return 'NULL';
        }
        if(is_object($value)){
            if(igk_reflection_class_implement($value, IHtmlGetValue::class)){
                return $value->getValue((object)["grammar"=>null, "type"=>"insert"]);
            }
        }
        if($tinf){
            $of=$type == "i" ? $tinf->clInsertFunction: $tinf->clUpdateFunction;
            if(!preg_match("/date(time)?/i", $tinf->clType) && !empty($of)){
                $gt=explode("(", $of);
                $pos=strtoupper(array_shift($gt));
                if($pos == "IGK_PASSWD_ENCRYPT"){
                    return "'".igk_db_escape_string(IGKSysUtil::Encrypt($value))."'";
                }
                return self::GetFCN($pos)."('".igk_db_escape_string($value)."')";
            }
        }
        return "'".igk_db_escape_string($value)."'";
    }
    ///<summary></summary>
    ///<param name="values"></param>
    ///<param name="tableInfo"></param>
    ///<param name="update"></param>
    private static function GetValues($values, $tableInfo, $update=0){
        $tvalues=igk_createobj();
        if(is_object($values) && method_exists($values, "to_array")){
            $values=$values->to_array();
        }
        if(is_array($values))
            $values=(object)$values;
        if($tableInfo){
            $filter=igk_environment()->mysql_query_filter;
            foreach($tableInfo as $k=>$v){
                if($v->clIsPrimary && $filter){
                    continue;
                }
                if(!property_exists($values, $k)){
                    if($update){
                        if($v->clLinkType || !$v->clUpdateFunction || !preg_match("/(date|datetime)/i", $v->clType)){
                            continue;
                        }
                    }
                    $tvalues->$k=null;
                }
                else{
                    $tvalues->$k=$values->{$k};
                }
            }
        }
        else{
            $tvalues=$values;
        }
        return $tvalues;
    }
    ///<summary></summary>
    protected function initConfig(){    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="tableinfo" default="null"></param>
    public function insert($tbname, $values, $tableinfo=null){
        $this->dieNotConnect();
        $this->initConfig();
        $query=SQLQueryUtils::GetInsertQuery($tbname, $values, $tableinfo);
        $t=$this->sendQuery($query);
        if($t){
            if(is_object($values)){
                if(igk_getv($values, IGK_FD_ID) == null)
                    $values->clId=$this->lastId();
            }
            return true;
        }
        else{
            $error="Query Insert Error : ".igk_mysql_db_error(). " : ".$query;
            igk_debug_wln($error);
            igk_db_error($error);
        }
        return false;
    }
    ///<summary>Represente IsAllowedDefValue function</summary>
    ///<param name="def"></param>
    ///<param name="type"></param>
    ///<param name="value"></param>
    protected static function IsAllowedDefValue($def, $type, $value){
        if($b=igk_getv($def, strtoupper($type))){
            if(isset($b[strtoupper($value)])){
                return true;
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    private static function IsNumber($t){
        return preg_match("/(int|float|decimal)/i", $t);
    }
    ///<summary>Represente Key function</summary>
    ///<param name="t"></param>
    ///<param name="adapter"></param>
    ///<param name="separator" default=","></param>
    private static function Key($t, $adapter, $separator=","){
        return implode($separator, array_map(function($t) use ($adapter){
            return "`".implode("`.`", array_map([$adapter, "escape_string"], explode(".", $t)))."`";
        }
        , array_map("trim", array_filter(explode(',', $t)))));
    }
    ///<summary>Represente ResolvType function</summary>
    ///<param name="t"></param>
    public static function ResolvType($t){
        return igk_getv(["int"=>"Int", "uint"=>"Int", "udouble"=>"Double", "bigint"=>"BIGINT", "ubigint"=>"BIGINT", "date"=>"Date", "enum"=>"Enum", "json"=>"JSON"], $t=strtolower($t), $t);
    }
    ///<summary>Represente SetAdapter function</summary>
    ///<param name="ad"></param>
    public static function SetAdapter($ad){
        self::$sm_adapter=$ad;
    }
    ///<summary>check if this type support defaut value</summary>
    public static function supportDefaultValue($type){
        return !in_array($type, ["int"]);
    }
}
