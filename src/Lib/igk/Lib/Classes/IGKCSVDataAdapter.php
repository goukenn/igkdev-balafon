<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCSVDataAdapter.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>Represente class: IGKCSVDataAdapter</summary>

use IGK\Database\DataAdapterBase;
use IGK\Database\IDbQueryResult;
use IGK\Helper\IO;
use IGK\System\Database\IDbSendQueryListener;
use IGK\System\Database\SQLGrammar;
use IGK\System\IO\CSV\Helper\CSVHelper;

/**
* Represente IGKCSVDataAdapter class
*/
final class IGKCSVDataAdapter extends DataAdapterBase {
    private $m_ctrl;
    private $m_dbname;
    private $m_fhandle;

    const DELIMITER = '"';
    const SEPARATOR = ',';

    public function setForeignKeyCheck($flag) { }

    public function allowTypeLength(string $type, ?int $length = null): bool { 
        return in_array($type, ['int','varchar']);
    }
    
    public function remove_foreign(string $name, string $column): ?string { return null; }

    public function setSendDbQueryListener(?IDbSendQueryListener $listener) { }

    public function getSendDbQueryListener(): ?IDbSendQueryListener { return null; }

    public function getDateTimeFormat(): string {
        return IGK_MYSQL_TIME_FORMAT;
    }

    public function exist_column(string $table, string $column, $db = null): bool {
        return false;
     }

    public function getVersion(): string { 
        return IGK_VERSION;
    }

    public function getType(): string {
        return 'CSV';
     }

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

    public function constraintForeignKeyExists(string $name): bool { 
        return false;
    }

    public function tableExists(string $table, bool $throwex = false): bool {
        return false;
     }

    public function getIsConnect(): bool {
        /** allway return true*/
        return true;
    }

    public function constraintExists(string $name): bool {
        return false;
    }

    public function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $column,string $dbname): string {
        return "";
    }

    public function getCreateTableFormat(?array $options = null): ?string {
        return null;
    }

    public function filterColumn($columninfo, $value): bool { 
        return false;
    }

    public function getDbName(): ?string { 
        return "file://csv";
    }

    public function escape_table_name(string $v): string {
        return  $v;
    }

    public function escape_table_column(string $v): string { 
        return $v;
    }
    public function isTypeSupported(string $type): bool {
        return true;
     }

    public function escape(?string $column=null): string {
        return $column;
     }

    public function supportDefaultValue(string $type): bool {
        return false;
     }

    public function isAutoIncrementType(string $type): bool { 
        return false;
    }

    public function getDataValue($value, $tinf) { }

    public function getParam(string $key, $rowInfo = null, $tableInfo = null): ?string {
        return null;
     }

    public function getDataTableDefinition(string $tablename) { 
        return null;
    }

    public function last_error() {
        return null;
     }
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
    ///<summary>escape string </summary>
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
    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    /**
    * 
    * @param mixed $ctrl the default value is null
    */
    public function __construct($ctrl=null){
        $this->m_ctrl=$ctrl;
    }
    ///<summary></summary>
    ///<param name="data"></param>
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
    ///<summary></summary>
    ///<param name="l"></param>
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
    ///<summary></summary>
    /**
    * 
    */
    public function close(){
        if($this->m_fhandle){
            fclose($this->m_f);
        }
    }
    ///<summary></summary>
    ///<param name="datafile" default="file"></param>
    /**
    * 
    * @param mixed $datafile the default value is "file"
    */
    public function connect($datafile="file"){
        $this->m_dbname=$datafile;
    }
    public function getFileName(){
        return $this->m_dbname;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function selectCount(string $table,?array $where = null, ?array $options = null){
        igk_dev_wln_e("CSV Adapter: Not Implement, ".__METHOD__, igk_ob_get_func('igk_show_trace'));
    }
    ///<summary></summary>
    ///<param name="result" default="null"></param>
    /**
    * 
    * @param mixed $result the default value is null
    */
    public function CreateEmptyResult($result=null){
        return null;
    }
    ///<summary></summary>
    ///<param name="value"></param>
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
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="callback"></param>
    /**
    * 
    * @param mixed $tablename
    * @param mixed $callback
    */
    public function initSystablePushInitItem($tablename, $callback){}
    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
    * 
    * @param mixed $tablename
    */
    public function initSystableRequired($tablename){
        return false;
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    /**
    * 
    * @param mixed $filename
    */
    public static function LoadData($filename, $options=null){
        $txt=IO::ReadAllText($filename);
        return self::LoadString($txt, true, $options);
    }
    ///<summary></summary>
    ///<param name="txt"></param>
    ///<param name="rmBom" default="true"></param>
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
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
    * 
    * @param mixed $tbname
    */
    public function selectAll($tbname){
        $this->selectAllFile($tbname);
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
    * 
    * @param mixed $tbname
    */
    public function selectAllFile($tbname){
        $f=igk_io_applicationdatadir()."/".$tbname.".csv";
        if(file_exists($f)){
            $r=IGKCSVQueryResult::CreateEmptyResult();
            $r->AppendEntries(self::LoadData($f), $this->m_ctrl->getDataTableInfo());
            return $r;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    ///<param name="entries"></param>
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