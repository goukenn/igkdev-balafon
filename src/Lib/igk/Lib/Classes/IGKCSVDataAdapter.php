<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCSVDataAdapter.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Database\DataAdapterBase;
use IGK\Database\IDbQueryResult;
use IGK\Helper\IO;
use IGK\System\Database\IDbSendQueryListener;
use IGK\System\Database\SQLGrammar;
use IGK\System\IO\CSV\Helper\CSVHelper;
/**
* Represent IGKCSVDataAdapter class
*/
final class IGKCSVDataAdapter extends DataAdapterBase {

    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;

    /**
    * Name of dbname.
    * @var mixed
    */
    private $m_dbname;

    /**
    * Property: fhandle.
    * @var mixed
    */
    private $m_fhandle;

    /**
    * Constant: delimiter.
    * @var mixed
    */
    const DELIMITER = '"';

    /**
    * Constant: separator.
    * @var mixed
    */
    const SEPARATOR = ',';

    /**
    * Queries Column Charset.
    * @param string $charset
    * @return ?string
    */
    public function queryColumnCharset(string $charset): ?string { 
        return PHP_EOL;
    }

    /**
    * Sets Foreign Key Check.
    * @param mixed $flag
    */
    public function setForeignKeyCheck($flag) { }

    /**
    * Allows Type Length.
    * @param string $type
    * @param null|int $length
    * @return bool
    */
    public function allowTypeLength(string $type, ?int $length = null): bool { 
        return in_array($type, ['int','varchar']);
    }

    /**
    * Removes foreign.
    * @param string $name
    * @param string $column
    * @return ?string
    */
    public function remove_foreign(string $name, string $column): ?string { return null; }

    /**
    * Sets Send Db Query Listener.
    * @param null|IDbSendQueryListener $listener
    */
    public function setSendDbQueryListener(?IDbSendQueryListener $listener) { }

    /**
    * Returns Send Db Query Listener.
    * @return ?IDbSendQueryListener
    */
    public function getSendDbQueryListener(): ?IDbSendQueryListener { return null; }

    /**
    * Returns Date Time Format.
    * @return string
    */
    public function getDateTimeFormat(): string {
        return IGK_MYSQL_TIME_FORMAT;
    }

    /**
    * Exist column.
    * @param string $table
    * @param string $column
    * @param null|mixed $db
    * @return bool
    */
    public function exist_column(string $table, string $column, $db = null): bool {
        return false;
     }

    /**
    * Returns Version.
    * @return string
    */
    public function getVersion(): string { 
        return IGK_VERSION;
    }

    /**
    * Returns Type.
    * @return string
    */
    public function getType(): string {
        return 'CSV';
     }

    /**
    * Lists Tables.
    */
    public function listTables() {
        return ['name'=>['csv_file']];
     }
    /**
     * no query allowed
     * @param string $query 
     * @param bool $throwex 
     * @param mixed $options 
     * @param bool $autoclose 
     * @return null|bool|IDbQueryResult 
     */

    public function sendQuery($query, $throwex = true, $options = null, $autoclose = false) {
        return false;
     }

    /**
    * Constraint foreign key exists.
    * @param string $name
    * @return bool
    */
    public function constraintForeignKeyExists(string $name): bool { 
        return false;
    }

    /**
    * Table exists.
    * @param string $table
    * @param bool $throwex
    * @return bool
    */
    public function tableExists(string $table, bool $throwex = false): bool {
        return false;
     }

    /**
    * Returns Is Connect.
    * @return bool
    */
    public function getIsConnect(): bool {
        /** allway return true*/
        return true;
    }

    public function constraintExists(string $name): bool {
        return false;
    }

    /**
    * Creates Table Column Info Query.
    * @param SQLGrammar $grammar
    * @param string $table
    * @param string $column
    * @param string $dbname
    * @return string
    */
    public function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $column,string $dbname): string {
        return "";
    }

    /**
    * Returns Create Table Format.
    * @param null|array $options
    * @return ?string
    */
    public function getCreateTableFormat(?array $options = null): ?string {
        return null;
    }

    /**
    * Filters Column.
    * @param mixed $columninfo
    * @param mixed $value
    * @return bool
    */
    public function filterColumn($columninfo, $value): bool { 
        return false;
    }

    /**
    * Returns Db Name.
    * @return ?string
    */
    public function getDbName(): ?string { 
        return "file://csv";
    }

    /**
    * Escape table name.
    * @param string $v
    * @return string
    */
    public function escape_table_name(string $v): string {
        return  $v;
    }

    /**
    * Escape table column.
    * @param string $v
    * @return string
    */
    public function escape_table_column(string $v): string { 
        return $v;
    }

    /**
    * Returns true if Type Supported.
    * @param string $type
    * @return bool
    */
    public function isTypeSupported(string $type): bool {
        return true;
     }

    /**
    * Escape.
    * @param null|string $column
    * @return string
    */
    public function escape(?string $column=null): string {
        return $column;
     }

    /**
    * Support default value.
    * @param string $type
    * @return bool
    */
    public function supportDefaultValue(string $type): bool {
        return false;
     }

    /**
    * Returns true if Auto Increment Type.
    * @param string $type
    * @return bool
    */
    public function isAutoIncrementType(string $type): bool { 
        return false;
    }

    /**
    * Returns Data Value.
    * @param mixed $value
    * @param mixed $tinf
    */
    public function getDataValue($value, $tinf) { }

    /**
    * Returns Param.
    * @param string $key
    * @param null|mixed $rowInfo
    * @param null|mixed $tableInfo
    * @return ?string
    */
    public function getParam(string $key, $rowInfo = null, $tableInfo = null): ?string {
        return null;
     }

    /**
    * Returns Data Table Definition.
    * @param string $tablename
    */
    public function getDataTableDefinition(string $tablename) { 
        return null;
    }

    /**
    * Last error.
    */
    public function last_error() {
        return null;
     }

    /**
    * To date time str.
    * @param mixed $format
    * @param mixed $value
    */
    public static function ToDateTimeStr($format, $value){
        $_format = "Y-m-d";
        if (preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $value)){
            $_format = "d/m/Y";
        }
        // Logger::print("Format : ". 
        // if(!($mktime = strtotime($value))){       
        if ($g = date_parse_from_format($_format, $value)){
           // igk_wln_e($g);
           if ($g["error_count"]==0){
            $mktime = mktime($g["hour"], $g["minute"], $g["second"], $g["month"], 
            $g["day"], $g["year"]);
           }else  
            return null;
       }
       return date($format, $mktime);
    }
    /**
     * escape string
     * @param mixed $v 
     * @return string 
     */

    public function escape_string(?string $v = null):string{
        // same as XMLDataAdapter 
        $v = stripslashes($v);
        return addslashes($v); 
	}
    /**
    * 
    * @param mixed $ctrl the default value is null
    */

    public function __construct($ctrl=null){
        $this->m_ctrl=$ctrl;
    }
    /**
    * 
    * @param mixed $data
    */

    static function __removeguillemet($data){
        $data = trim($data);
        if(0===strpos($data, '"')){
            $data=substr($data, 1);
        }
        if(strpos($data, '"', -1)!==false)
            $data=substr($data, 0, strlen($data)-1);
        return $data;
    }
    /**
    * 
    * @param mixed $l
    */
    private static function _CSVReadLine($l, $sep=",", ?int $flags=null){ 
        $ch = '';
        $ln = strlen($l);
        $pos = 0;
        $tab = [];
        $v = '';
        $f = false;
        $wait = false;
        $v_is_read_serialize = $flags && (($flags & CSVHelper::CSV_READ_SERIAL)>0);
        while($pos<$ln){
            $ch = $l[$pos];
            if ($wait){
                if ($ch == $sep){
                    $wait = false;
                }
                $pos++;
                continue;
            }
            if ($ch=="\""){
                $mpos = $pos;
                $v = igk_str_remove_quote(trim(igk_str_read_brank($l, $pos, $ch,$ch,null,true)));
                $v = igk_str_transform_linefeed($v); 
                if ($v=='{'){
                    // possible json data
                    $rpos = $pos-1;
                    $json = igk_str_read_brank($l, $rpos, '}','{',null,true);
                    $json = stripslashes($json);
                    if (json_decode($json)){
                        $pos = $rpos;
                        $v = $json;
                    }
                } else if ($v_is_read_serialize && preg_match("/^[^:]+:[^:]+:\{/", $v, $b)){
                    // possible serialized data
                    // a:3:{i:1;a:1:{s:5:"title"}}}
                    $v_s = $b[0];
                    $v_ln = strlen($v_s);
                    $rpd = substr($l, $mpos+$v_ln);   
                    $npos = 0;
                    $m = rtrim($b[0],'{').igk_str_read_brank($rpd, $npos, '}','{', null, true);
                    if (@unserialize($m)){
                        $pos = $mpos+$npos+$v_ln;                               
                        $v = $m;
                    }  else {
                        igk_ilog([
                            'failed to unserialize',
                            $m
                        ]);
                        igk_die('faile to unserialize data ');
                    }          
                }
                else{
                    $v = stripslashes($v);
                }
                $tab[] = $v;
                $v = '';
                $wait = true;
            }else{
                if ($ch == $sep){
                    if (!empty($v)){
                        $tab[] = trim($v);
                        $v = '';
                    }else if ($f){
                        $tab[] = '';
                    }
                    $f = true;
                }else{
                    $v.= $ch;
                    $f = false;
                }
            }
            $pos++;
        }
        if(!empty($v)){
            $tab[] = $v;
        }
        return $tab;
    }
    /**
    * 
    */

    public function close(){
        if($this->m_fhandle){
            fclose($this->m_f);
        }
    }
    /**
    * 
    * @param mixed $datafile the default value is "file"
    */

    public function connect($datafile="file"){
        $this->m_dbname=$datafile;
    }

    /**
    * Returns File Name.
    */
    public function getFileName(){
        return $this->m_dbname;
    }
    /**
    * 
    */

    public function selectCount(string $table,?array $where = null, ?array $options = null){
        igk_dev_wln_e("CSV Adapter: Not Implement, ".__METHOD__, igk_ob_get_func('igk_show_trace'));
    }
    /**
    * 
    * @param mixed $result the default value is null
    */

    public function createEmptyResult($result=null){
        return null;
    }
    /**
    * 
    * @param mixed $value
    */

    public static function GetValue(?string $value){
        if($value && ((strpos($value, igk_csv_sep()) !== false) || preg_match("/( |\t|\n)/i", $value))){
            $value = igk_str_replace_assoc_array(["\n"=>'\n',"\t"=>'\t',"\r"=>'\r'],$value);
            return "\"".$value."\"";
        }
        return $value;
    }
    /**
    * 
    * @param mixed $tablename
    * @param mixed $callback
    */

    public function initSystablePushInitItem($tablename, $callback){}
    /**
    * 
    * @param mixed $tablename
    */

    public function initSystableRequired($tablename){
        return false;
    }
    /**
    * 
    * @param mixed $filename
    */

    public static function LoadData($filename, $options=null){
        $txt=IO::ReadAllText($filename);
        return self::LoadString($txt, true, $options);
    }
    /**
    * 
    * @param mixed $txt
    * @param mixed $rmBom the default value is true
    * @param array|\IGK\System\IO\CSV\CSVDataAdapterLoadStringOptions $options assoc array of delimiter:ch|separator|flag : csv flag|filter callback to filter
    */

    public static function LoadString($txt, $rmBom=true, $options=null){
        if(empty($txt))
            return array();
        if($rmBom){
            $txt=igk_io_remove_bom($txt);
        }
        $entries = [];
        $sep = ($options ? igk_getv($options, "separator"): null) ?? self::SEPARATOR; 
        $delimeter = ($options ? igk_getv($options, "delimiter"): null) ?? self::DELIMITER;
        $flags = ($options ? igk_getv($options, "flags"): null) ?? 0;
        $filter = igk_getv($options, "filter", function(){
            return function(){
                return true;
            };
        });
        igk_csv_readline($txt, '"', $last, function($line)use($sep, & $entries, $filter, $delimeter, $flags){
            $tab = self::_CSVReadLine($line, $sep, $flags);
            if ($filter($tab)){
                $entries[] =$tab;
            }
            return true;
        }, $flags);
        return $entries;
        // $lines=explode(IGK_LF, $txt);
        // $entries=array();
        // foreach($lines as $l){
        //     if(empty($l)){
        //         continue;
        //     }
        //     if (!$filter($tab=self::_CSVReadLine($l, $sep))){
        //         break;
        //     }
        //     $entries[]=$tab;
        // }
        // return $entries;
    }
    /**
    * 
    * @param mixed $tbname
    */

    public function selectAll($tbname){
        $this->selectAllFile($tbname);
    }
    /**
    * 
    * @param mixed $tbname
    */

    public function selectAllFile($tbname){
        $f=igk_io_applicationdatadir()."/".$tbname.".csv";
        if(igk_io_file_exists($f)){
            $r=IGKCSVQueryResult::CreateEmptyResult();
            $r->AppendEntries(self::LoadData($f), $this->m_ctrl->getDataTableInfo());
            return $r;
        }
        return null;
    }
    /**
    * 
    * @param mixed $filename
    * @param mixed $entries
    */

    public static function StoreData($filename, $entries){
        $out=IGK_STR_EMPTY;
        $v_ln=false;
        foreach($entries as $v){
            if($v_ln){
                $out .= IGK_LF;
            }
            else
                $v_ln=true;
            $v_sep=false;
            foreach($v as $d){
                if($v_sep){
                    $out .= igk_csv_sep();
                }
                else
                    $v_sep=true;
                $out .= self::GetValue($d);
            }
        }
        igk_io_save_file_as_utf8($filename, $out, true);
    }
    ///convert tab to line entry
    /**
    */

    public static function toCSVLineEntry($tab, $key=null){
        $out=IGK_STR_EMPTY;
        $v_sep=false;
        if(is_object($tab)){
            foreach($tab as  $c){
                if($v_sep)
                    $out .= igk_csv_sep();
                else
                    $v_sep=true;
                $out .= self::GetValue($c);
            }
            return $out;
        }
        if(!is_array($tab)){
            return null;
        }
        if($key != null){
            $v_sep=false;
            foreach($tab as $c){
                if($v_sep)
                    $out .= igk_csv_sep();
                else
                    $v_sep=true;
                $out .= $c->$key;
            }
        }
        else{
            foreach($tab as $c){
                if($v_sep)
                    $out .= igk_csv_sep();
                else
                    $v_sep=true;
                $out .= self::GetValue($c);
            }
        }
        return $out;
    }
}