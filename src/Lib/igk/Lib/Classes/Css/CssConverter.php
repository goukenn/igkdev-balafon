<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssConverter.php
// @date: 20230124 15:25:16
namespace IGK\Css;
use IGK\Css\Traits\CssConverterScssVisitorTrait;
use IGK\Helper\StringUtility;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGKException;

/**
* auto generate doc.
* @package IGK\Css
*/
class CssConverter{
    use CssConverterScssVisitorTrait;
    /**
    * Property: length.
    * @var mixed
    */
    var $length;
    /**
    * Property: src.
    * @var mixed
    */
    var $src;
    /**
    * Property: imports.
    * @var mixed
    */
    private $imports = [];
    /**
    * Property: variables.
    * @var mixed
    */
    private $variables = [];
    /**
    * Property: functions.
    * @var mixed
    */
    private $functions = [];
    /**
    * Property: medias.
    * @var mixed
    */
    private $medias = [];
    /**
    * Property: keyframes.
    * @var mixed
    */
    private $keyframes = [];
    /**
     * source file
     * @var mixed
     */
    private $source_file;
    /**
    * Constant: mode root.
    * @var mixed
    */
    const MODE_ROOT = 0;
    /**
    * Constant: mode attrib.
    * @var mixed
    */
    const MODE_ATTRIB = 1;
    /**
    * Constant: mode value.
    * @var mixed
    */
    const MODE_VALUE = 2;
    /**
    * Constant: mode selector.
    * @var mixed
    */
    const MODE_SELECTOR = 3;
    /**
    * Constant: media key.
    * @var mixed
    */
    const MEDIA_KEY = '@media';
    /**
    * Constant: media variables key.
    * @var mixed
    */
    const MEDIA_VARIABLES_KEY = '@variables';
    /**
    * Constant: media function key.
    * @var mixed
    */
    const MEDIA_FUNCTION_KEY = '@function';
    /**
    * Constant: media keyframe key.
    * @var mixed
    */
    const MEDIA_KEYFRAME_KEY = '@keyframes';
    /**
    * auto generate doc.
    * @return mixed
    */
    private function initialize(){
        $this->imports = [];
        $this->variables = [];
    }
    /**
    * Parses Scss Content.
    * @param string $content
    */
    public function parseScssContent(string $content){
        $this->initialize();
        $this->src = $content;
        $this->length = strlen($content);
        return $this->parse();
    }
    /**
    * Parses Form SCSS.
    * @param string $file
    */
    public static function ParseFormSCSS(string $file)
    {
        $src = file_get_contents($file);
        $converter = new self;
        $converter->length = strlen($src);
        $converter->src = $src;
        $converter->source_file = $file;
        return $converter->parse();
    }
    /**
     * parse content
     * @return array 
     * @throws IGKException 
     */
    public function parse()
    {
        $offset = 0;
        $depth = 0; 
        $selector = '';
        $data = $this->_readData($offset, $depth, $selector);
        $merge = [];
        if (!empty($this->medias)){
            $merge[self::MEDIA_KEY] = $this->medias;
        }
        if (!empty($this->variables)){
            $merge[self::MEDIA_VARIABLES_KEY] = $this->variables;
        }
        if (!empty($this->keyframes)){
            $merge[self::MEDIA_KEYFRAME_KEY] = $this->keyframes;
        }
        return array_merge($data,  $merge);
    }
    /**
    * after : if char
    * @param mixed $src
    * @param mixed & $offset
    * @param mixed & $data
    * @param mixed $offset
    * @param mixed & $bch
    * @return void
    */
    private static function _TryReadSelector($src, & $offset,& $data, $ln, & $bch){
        $pos = $offset;
        $pos++;
        $r = '';
        $e = false;
        $response = false;
        while(!$e && ($pos<$ln) ){
            $ch = $src[$pos];
            switch($ch){                
                case ' ':                                        
                case '>':
                case '+':
                case '*':
                case "\n":
                    if (!empty($r)){
                        $response = true;
                        $e = true;
                        $data.= $bch.$r.' ';
                        $offset = $pos-1;
                        $bch = '';
                        return true;
                    }
                    return false;
                    break;
                    case ',': 
                        if (!empty($r)){    
                            $data.=  $bch.$r.',';
                            $offset = $pos;  
                            $bch = '';                 
                            $response = true;
                            return true;
                        }
                        break;
                case '{':
                    if (!empty($r)){    
                        $data.=  $bch.$r;
                        $offset = $pos-1;  
                        $bch = '';                 
                        $response = true;
                        return true;
                    }
                break;
                case ";":
                    return false;
            }
            $pos++;
            $r .= $ch;
        }
        return $response;
    }
    /**
    * auto generate doc.
    * @param mixed & $offset
    * @param mixed $depth
    * @param mixed $selector
    * @param mixed $stop
    * @return mixed
    */
    private function _readData(& $offset, $depth, $selector,
        $stop=false){
        $src = $this->src;
        $name = '';
        $attrib_name = '';
        $iv = '';
        $data = [];
        $mode = self::MODE_ROOT;
        $attribs = [];
        $selectors = [];
        $operators = ['&'];
        if (!empty($selector)){
            array_push($selectors, $selector);
        }
        while ($offset < $this->length) {
            $ch = $src[$offset];
            switch ($ch) {
                case '$':
                    if ($mode == self::MODE_ROOT) {
                        $offset++;
                        $n = self::_ReadVariable($src, $offset, $this->length);
                        $this->variables[$n['name']] = $n['value'];
                        $ch = '';
                    } else {
                        $offset++;
                        $n = $this->_ReadName($src, $offset, $this->length);
                        $iv .= igk_getv($this->variables, $n, "[prop:".$n."]");
                        if (!isset($this->variables[$n])){
                            $this->variables[$n] = '';
                        }
                        $ch = '';
                        $offset--;
                    }
                    break;
                case '{':
                    if ($mode == self::MODE_ROOT) {
                        $selector =
                            self::_GetSelector( trim($iv), $selector, $operators);
                        $iv = $ch = '';
                        $mode = self::MODE_ATTRIB;
                        $data[$selector] = [];
                        $attribs = &$data[$selector];
                    } else {
                        if ($mode == self::MODE_ATTRIB) {
                            $selectors[] = $selector;
                            $id = trim($iv);
                            $selector = self::_GetSelector($id, $selector, $operators);
                            $data[$selector] = [];
                            $attribs = &$data[$selector];
                            $iv = $ch = '';
                        } else {
                            igk_die(
                                "not implement : [case {]" . $mode .
                                    " data :\n" . substr($src, $offset - 30, 60)."\n".
                                    'iv:'.$iv
                            );
                        }
                    }
                    $depth++;
                    break;
                case '}':
                    if ($selector = array_pop($selectors)) {
                        if (isset($data[$selector])){
                            $attribs = &$data[$selector];
                        }
                    } else {
                        unset($attribs);
                        $mode = self::MODE_ROOT;                      
                    }
                    $iv = $ch = '';
                    $depth--;
                    if ($depth < 0){
                        if ($stop){
                            return $data;
                        }
                    }
                    break;
                case ':':
                    if ($mode == self::MODE_ATTRIB) {
                        $attrib_name = trim($iv);
                        $iv = '';
                        if ((strlen($attrib_name)>0) && 
                            (
                                in_array($attrib_name[0], ['&', '.', '#']) || 
                                self::_TryReadSelector($src, $offset, $attrib_name, $this->length, $ch)
                        )) {
                            $offset++;
                            $attrib_name .= $ch . $this->_ReadSelector(
                                $src,
                                $offset,
                                $this->length,
                                $selector
                            );
                            $iv .= $attrib_name;
                            $attrib_name = '';
                            $offset--;
                        } else {
                            $mode = self::MODE_VALUE;
                        }
                        $ch = '';
                    } else if ($mode != self::MODE_ROOT){
                        igk_die("not implement [case :] " . $mode. " : ".substr($src, $offset-10,20));
                    }
                    break;
                case ';':
                    if ($mode == self::MODE_VALUE) {
                        $attrib_value = trim($iv);
                        $attribs[$attrib_name] = $attrib_value;
                        $attrib_name =
                            $attrib_value =
                            $ch =
                            $iv = '';
                        $mode = self::MODE_ATTRIB;
                    } else {
                        igk_die("not implement ..... ; " . $mode . "  s=".  substr($src, $offset - 10,20));
                    }
                    break;
                case '"':
                case "'":
                    $iv .= igk_str_read_brank($src, $offset, $ch, $ch, null, true, true);
                    $ch = '';
                    break;
                case '(':
                    $iv .= igk_str_read_brank($src, $offset, ')', '(', null, true, true);
                    $ch = '';
                    break;
                case '/':
                    if (strpos($src, '/', $offset + 1) === ($offset + 1)) {
                        $l = strpos($src, "\n", $offset + 1);
                        $offset = $l === false ? $this->length : $l;
                        $ch = '';
                    } else if (strpos($src, '*', $offset + 1) === ($offset + 1)) {
                        $l = strpos($src, "*/", $offset + 1);
                        $offset = $l === false ? $this->length : $l + 2;
                        $ch = '';
                    }
                    break;
                case '@':
                    $offset++;
                    $name = $this->_readName($src, $offset, $this->length);
                    $_info = (object)[
                        'name' => $name, 'offset' => &$offset,
                        'depth' => $depth,
                        'ch' => null,
                        'length' => $this->length,
                        'selector' => $selector
                    ];
                    $this->_visit($name, $_info);
                    $ch = '';
                    break;
            }
            $offset++;
            $iv .= $ch;
        }
        return $data;
    }
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $selector
    * @param mixed $operators
    * @return string
    */
    private static function _GetSelector($id, $selector, $operators):string{
        $sep = ' ';
        $g = explode(',', $id);
        $cp = [];
        while(count($g)>0){
            $id = array_shift($g);   
            if (empty($id)){
                continue;
            }
            if ($id && in_array($id[0], $operators)) {
                $sep = igk_getv(['&' => ''], $id[0], ' ');
                $id = substr($id, 1);
            }
            $cp[] = trim(implode($sep, array_filter([$selector, $id])));
        }
        return implode(",", $cp);
    }
    /**
    * read selector and stop at the '{' or end files
    * @param mixed $src
    * @param mixed & $offset
    * @param mixed $offset
    * @param mixed $selector
    * @return string
    */
    private static function _ReadSelector($src, &$offset, $length, $selector)
    {
        $sl = '';
        $lch = '';
        while ($length > $offset) {
            $ch = $src[$offset];
            if ($ch == '{') {
                break;
            }
            switch ($ch){
                case "\n":
                case "\t":
                case "\r":
                case ' ':
                    if (($lch != ' ') && !empty($lch)){
                        $sl.=' ';                        
                    }
                    $ch = '';
                    break;
                case '&':
                    $sl .= $selector;
                    $ch = "";
                    break;
            } 
            $offset++;
            $sl .= $ch;
            $lch = $ch;
        }
        return trim($sl);
    }
    /**
    * auto generate doc.
    * @param mixed $src
    * @param mixed & $offset
    * @param mixed $length
    * @return mixed
    */
    private static function _ReadName($src, &$offset, $length)
    {
        $n = '';
        $token = StringUtility::IDENTIFIER_TOKEN.'-';
        while ($offset < $length) {
            $ch = $src[$offset];
            if (strpos($token, $ch) === false) {
                break;
            }
            $n .= $ch;
            $offset++;
        }
        return $n;
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $options
    * @return mixed
    */
    protected function _visit($name, $options)
    {
        $name = ltrim(str_replace('-', '_', $name), '_ ');
        if (method_exists($this, $fc = strtolower('_visit_' . $name))) {
            $this->$fc($options);
        }else {
            Logger::danger("missing visitor for ".$name);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $gt
    * @return mixed
    */
    private function _copyData($gt){
        $this->variables = array_merge($this->variables, igk_getv($gt, self::MEDIA_VARIABLES_KEY, []));
        $this->keyframes = array_merge($this->keyframes, igk_getv($gt, self::MEDIA_KEYFRAME_KEY, []));
        $this->medias = array_merge($this->medias, igk_getv($gt, self::MEDIA_KEY, []));
        $this->functions = array_merge($this->functions, igk_getv($gt, self::MEDIA_FUNCTION_KEY, []));
    }
    /**
    * Visit import.
    * @param mixed $options
    */
    protected function _visit_import($options)
    {
        $iv = '';
        $src = $options->src ?? $this->src;
        $end = false;
        while (!$end && $this->_read($options)) {
            $ch = $options->ch;
            switch ($ch) {
                case ';':
                    $path =  trim($iv);
                    $this->imports[] = $path;
                    if ($this->source_file){
                        $g = Path::FlattenPath(Path::Combine(dirname($this->source_file), $path));
                        $name = basename($g);
                        $dir = dirname($g);
                        foreach(['', '_'] as $f){
                            if (igk_io_file_exists($tf = $dir."/".$f.$name.".scss")){
                                $gt = self::ParseFormSCSS($tf);
                                // + | copy data
                                $this->_copyData($gt);
                                break;
                            }
                        }
                    }
                    $end = true;
                    $ch = $iv = '';
                    break;
                case '"':
                case "'":
                    $g = trim(igk_str_read_brank($src, $options->offset, $ch, $ch, null, false, true), $ch . ' ');
                    $iv .= $g;
                    $ch = '';
                    $options->offset++;
                    break;
            }
            $iv .= $ch;
        }
    }
    /**
    * Visit media.
    * @param mixed $options
    */
    protected function _visit_media($options)
    {
        $src = $this->src;
        $offset = & $options->offset; 
        $selector = $options->selector;
        $condition = self::_ReadSelector($src, $offset, $this->length, null);
        $offset++;
        if ($data = $this->_readData($offset, 0, $selector, true)){
            if (!isset($this->medias[$condition])){
                $this->medias[$condition] = []; 
            }
            $this->medias[$condition] = array_merge(
                $this->medias[$condition], $data
            );
        } 
    }
    /**
     * read char
     * @param mixed $options 
     * @return bool 
     */
    private function _read($options)
    {
        $src = $options->src ?? $this->src;
        if ($options->offset < $options->length) {
            $options->ch = $src[$options->offset];
            $options->offset++;
            return true;
        }
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $src
    * @param mixed & $offset
    * @param mixed $length
    * @return mixed
    */
    private function _ReadVariable($src, &$offset, $length)
    {
        $name = '';
        $value =  '';
        $read = 0;
        $end = false;
        $iv = '';
        while (!$end && ($offset < $length)) {
            $ch = $src[$offset];
            switch ($ch) {
                case ':':
                    $read = 1;
                    $name = trim($iv);
                    $ch = $iv = '';
                    break;
                case "'":
                case '"':
                    $iv = igk_str_read_brank($src, $offset, $ch, $ch);
                    $ch = '';
                    break;
                case ";":
                    $ch = '';
                    $end = true;
                    break;
            }
            $iv.=$ch;
            $offset++;
        }
        $value = trim($iv);
        return compact('name', 'value');
    }
}