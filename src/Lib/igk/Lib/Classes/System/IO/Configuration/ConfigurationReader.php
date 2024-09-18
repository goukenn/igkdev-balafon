<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationReader.php
// @date: 20220830 09:47:38
// @desc: 

namespace IGK\System\IO\Configuration;

use Closure;
use IGK\System\IO\EnumDefinitionReader;
use stdClass;

/**
 * String configuration reader. 
 * @package IGK\System\Configuration
 */
class ConfigurationReader
{
    /**
     * delimit key-value pair
     * @var string
     */
    var $delimiter = ",";

    /**
     * separator between key and value
     * @var string
     */
    var $separator = "=";

    /**
     * read errors
     * @var array
     */
    protected $m_errors = [];

    /**
     * litteral string to read
     * @var string
     */
    protected $m_text;
    /**
     * offset position
     * @var int
     */
    protected $m_offset;

    /**
     * read mode
     * @var mixed
     */
    protected $m_readmode;
    /**
     * length to read
     * @var mixed
     */
    protected $m_ln;
    
    protected $m_result;

    /**
     * escae start litter counter 
     * @var mixed
     */
    var $escape_start; // ConfigurationReader

    /**
     * escape start
     * @var mixed
     */
    var $escape_end;

    const MODE_NAME = 1;
    const MODE_VALUE = 2;

    /**
     * non marked string listener
     * @var ?callable
     */
    var $NonMarkedStringPropertiesListener;


    /**
     * treat expression 
     * @param string $text 
     * @param mixed $expression 
     * @return string 
     */
    public function treatExpression(string $text, & $expression){
        $expression = [];
        $l = $text;
        $offset = 0;
        $expression = [];
        $exp_count = 0;
        $s_ch = $this->escape_start;
        $e_ch = $this->escape_end;
        if ($s_ch && $e_ch)
        while(($pos = strpos($l, $s_ch, $offset))!==false){
            $spos = $pos;
            $n = igk_str_read_brank($l, $pos, $e_ch, $s_ch);
            $exp_count++;
            $key = '%__exp_'.$exp_count.'__%';
            $l = substr($l, 0,  $spos).$key.substr($l, $pos+1);
            $pos = $spos; 
            $expression[$key] = $n;
        }

        return $l;
    }
    public function __construct()
    {
    }
    
    /** 
     * read a value and return a object associated with it   
     * @param string $value 
     * @param null|int $length 
     * @param null|Closure $callback 
     * @return false|stdClass 
     */
    public function read(string $value, ?int $length = null, ?Closure $callback=null)
    {
        if(preg_match($not_regex = "/('|\")/i", $this->separator) || 
            preg_match($not_regex, $this->delimiter)){
                $this->m_errors[] = "not a valid separator or delimiter";
            return false;
        }

        $obj = new ConfigurationObject();
        $this->m_text = $value;
        $this->m_offset = 0;
        $this->m_readmode = self::MODE_NAME;
        $this->m_ln = $length ?? strlen($this->m_text);
        $list = [];
        $name = null;
        $value = null;

        $fc_bind = function(& $list, $name, $value){
            $gf = $this->NonMarkedStringPropertiesListener;
            if (!is_null($name) && !empty($name)){
                $obj = new ConfigurationObject;
                $obj->key = self::RmStringMark($name);
                $obj->value = $gf && $gf($obj->key)? $value: self::RmStringMark($value);  
                $list[] = $obj;
            }
        };
        $sep = $this->separator;
        $v_escape_counter = 0;
        while ($this->_canRead()) {
            $ch = $this->m_text[$this->m_offset];  
            switch ($ch) {
                case $this->delimiter:
                    if ($v_escape_counter==0){
                        $fc_bind($list, $name, $value);
                        $this->m_readmode = self::MODE_NAME;
                    } else {
                        $value.=$ch;
                    }
                    break;
                case $sep:
                    if (is_null($name)){       
                        $this->m_offset++;                 
                        $name = $sep.$this->_readName(); 
                    }
                    $this->m_readmode = self::MODE_VALUE;
                    break;
                default:
                    switch ($this->m_readmode) {
                        case  self::MODE_NAME:
                            $name = $this->_readName(); 
                            break;
                        case self::MODE_VALUE:
                            $value = $this->_readValue();
                            $fc_bind($list, $name, $value); 
                            $name = null;
                            $value = null;
                            if ($callback){
                                $callback($obj);
                            }
                        default: 
                            break;
                    }
            }
            $this->m_offset++;
        }
        if (empty($this->m_errors)){
            $fc_bind($list, $name, $value);
            $info = new stdClass;
            array_map(function($a)use($info){ 
                $v = $a->value;
                if (is_numeric($v)){
                    $v = floatval($v);
                }
                $info->{$a->key} = $v;           
            },$list);
            $this->m_result = $list;
            return $info;
        }
        return false;
    }
    /**
     * get the result of last reading string
     * @return mixed 
     */
    public function getResult(){
        return $this->m_result;
    }
    public static function RmStringMark($str){
        if (!is_null($str) && !empty($g = trim($str))){
            if (preg_match("/^('|\")(.)*\\1$/", $g, $tab)){                
                $str = trim($g, $tab[1]);
            }
        }
        return $str;
    }
    protected function _canRead(): bool
    {
        if (count($this->m_errors)>0){
            return false;
        }
        if ($this->m_offset < $this->m_ln) {
            return true;
        }
        return false;
    }
    protected function _readName(): ?string
    {
        return trim($this->_readData($this->separator) ?? '');
    }
    protected function _readValue(): ?string
    {
        return trim($this->_readData($this->delimiter) ?? '');
    }
    protected function _readData(string $end){
        /**
         * @var ?string $d
         */
        $d = null;
        $escape_delimiter = $this->delimiter== $end;
        $v_ecounter = 0;
        while($this->_canRead()){
            $ch = $this->m_text[$this->m_offset]; 
            if ($escape_delimiter){
                if ($ch=='\\'){
                    if (($this->escape_start || $this->escape_end) && ( $this->m_ln-1 > $this->m_offset)){
    
                        $v_next_ch = $this->m_text[$this->m_offset+1];
                        if (($this->escape_end == $v_next_ch) || ($this->escape_start==$v_next_ch)){
    
                            if ($this->escape_start==$v_next_ch){
                                $v_ecounter++;
                                
                            }else if ($this->escape_end == $v_next_ch){
                                $v_ecounter--;
                            }
                            $d.= $v_next_ch;
                            $this->m_offset+=2;
                            continue;
                        }
                    }
                }
            }

            switch($ch){
                case '"':
                case "'":
                    // litteral consideration
                    $d.= igk_str_read_brank($this->m_text, $this->m_offset, $ch, $ch,null,1, 1);                   
                break;
                default:
                    if (is_null($d)){
                        $d = "";
                    }
                    // $d .= $ch;
                    if ($this->_readLitteralEnd($ch, $end)){
                        if ($v_ecounter==0){ 
                            $this->m_offset--;
                            return !is_null($d) ? trim($d) : null; 
                        }
                    }
                    $d .= $ch;
                    break; 
            }
            $this->m_offset++;
        } 
        return $d;
    }
    protected function _readLitteralEnd(string $ch, string $end):bool{
        return $ch == $end;
    }
   
    public function getErrors(){
        return $this->m_errors;
    }

    /**
     * create a css value reader
     * @return ConfigurationReader 
     */
    public static function CreateCssValueReader(){
        $reader = new self;
        $reader->separator = ':';
        $reader->delimiter = ';';
        return $reader;
    }
    /**
     * create a connexion string value reader
     * @return ConfigurationReader 
     */
    public static function CreateConnexionStringValueReader(){
        $reader = new self; 
        return $reader;
    }
    /**
     * create environment value reader
     * @return ConfigurationReader 
     */
    public static function CreateEnvironmentValueReader(){
        $reader = new self; 
        $reader->separator = '=';
        $reader->delimiter = "\n";
        return $reader;
    }

    /**
     * direct parsing
     */
    public static function Parse(string $value){
        $reader = new self;
        return $reader->read($value);
    }

    public static function ParseEnumLitteralValue(string $value){
        $r = new EnumDefinitionReader;
        return $r->read($value);
    }
}
