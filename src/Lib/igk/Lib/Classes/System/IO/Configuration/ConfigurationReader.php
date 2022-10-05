<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationReader.php
// @date: 20220830 09:47:38
// @desc: 

namespace IGK\System\IO\Configuration;

use Closure;
use PhpParser\Node\Stmt\Break_;
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
    private $m_errors = [];

    /**
     * litteral string to read
     * @var string
     */
    private $m_text;
    /**
     * offset position
     * @var int
     */
    private $m_offset;

    /**
     * read mode
     * @var mixed
     */
    private $m_readmode;
    /**
     * length to read
     * @var mixed
     */
    private $m_ln;
    
    private $m_result;

    const MODE_NAME = 1;
    const MODE_VALUE = 2;

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
            $this->m_errors[] = "not a valid separtor or delimiter";
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
            if (!is_null($name) && !empty($name)){
                $obj = new ConfigurationObject;
                $obj->key = self::RmStringMark($name);
                $obj->value = self::RmStringMark($value);
                $list[] = $obj;
            }
        };

        while ($this->_canRead()) {
            $ch = $this->m_text[$this->m_offset];            
            switch ($ch) {
                case $this->delimiter:
                    $fc_bind($list, $name, $value);
                    $this->m_readmode = self::MODE_NAME;
                    break;
                case $this->separator:
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
                            # code...
                            break;
                    }
            }
            $this->m_offset++;
        }
        if (empty($this->m_errors)){
            $fc_bind($list, $name, $value);
            $info = new stdClass;
            array_map(function($a)use($info){ 
                $info->{$a->key} = $a->value;           
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
    private function _canRead(): bool
    {
        if (count($this->m_errors)>0){
            return false;
        }
        if ($this->m_offset < $this->m_ln) {
            return true;
        }
        return false;
    }
    private function _readName(): ?string
    {
        return $this->_readData($this->separator);
    }
    private function _readValue(): ?string
    {
        return $this->_readData($this->delimiter);;
    }
    private function _readData(string $end){
        /**
         * @var ?string $d
         */
        $d = null;
        while($this->_canRead()){
            $ch = $this->m_text[$this->m_offset];
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
                    $d .= $ch;
                    break;
                case $end: 
                    $this->m_offset--;
                    return !is_null($d) ? trim($d) : null; 
            }
            $this->m_offset++;
        } 
        return $d;
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
}
