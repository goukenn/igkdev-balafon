<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTmLanguageConverter.php
// @date: 20250704 13:51:16
namespace IGK\System\IO\File\TmLanguage\Converters;
use Exception;
use Error;
use IGK\Helper\Activator;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherPattern;
use IGKException;
use ReflectionException;

/**
 * 
 * @package IGK\System\IO\File\TmLanguage\Converters
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\System\IO\File\TmLanguage\Converters
*/
class RegexMatcherContainerTmLanguageConverter
{
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * Property: references.
    * @var mixed
    */
    private $m_references = [];
    /**
    * Remove type.
    * @param mixed $a
    */
    protected function _removeType($a)
    {
        $tab = [$a];
        $mark = [];
        while (count($tab) > 0) {
            $q = array_shift($tab);
            if (($tp = in_array($q, $mark)) !== false) {
                continue;
            }
            $mark[] = $q;
            if (is_array($q)) {
                unset($q['type']);
            } else if (is_object($q)) {
                unset($q->type);
            }
            if ($p = (array)igk_getv($q, 'patterns')) {
                $tab = array_merge($tab, $p);
            }
        }
        return $a;
    }
    /**
    * convert to tm language
    * @param RegexMatcherContainer $ctn
    * @param string $scopeName
    * @throws IGKException
    * @throws Exception
    * @throws Error
    * @throws CssParserException
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @return array
    */
    public function convert(RegexMatcherContainer $ctn, string $scopeName):array
    {
        $this->m_data = Activator::CreateNewInstance(RegexMatcherContainerTmDefinition::class, (object)[
            'version' => '1.0',
            'repository' => [],
            'patterns' => [],
            '$scope' => '',
            'scopeName' => $scopeName
        ]);
        $this->m_references = [];
        if ($d = $ctn->getMatcher()) {
            $refdata = [];
            $datas = [];
            $logic = [];
            $repository = [];
            while (count($d) > 0) {
                $q = array_shift($d);
                if (is_array($q)){
                    continue;
                }
                $id = spl_object_id($q);
                if (!isset($refdata[$id])){
                    $refdata[$id] = (object)['data'=>$q, 'id'=>'pattern-'.$id, 'ref'=>0];
                    if ($patterns =  igk_getv($q, 'patterns')) {
                        array_unshift($d, ...$patterns);
                    }
                }else{
                    $refdata[$id]->ref++;
                }
            }
            $d = $ctn->getMatcher();
            $patterns = & $this->m_data->patterns;
            $repository = & $this->m_data->repository; 
            while (count($d) > 0){
                $q = array_shift($d);
                if (is_array($q)){
                    $patterns[] = $q;
                    continue;
                }
                $id = spl_object_id($q);
                $o = $this->_unsetPrivateMembers((array)$q);
                if ($dc = igk_getv($q, 'patterns')){
                    $np = $this->_getIncludeData($refdata, $dc);
                    $o['patterns'] = $np;
                } 
                $repository[$refdata[$id]->id] = $o;
                $patterns[] = ['include'=>'#'.$refdata[$id]->id];
            }          
        }
        foreach($refdata as $tp){
            if (!isset($repository[$tp->id])){
                $o = $this->_unsetPrivateMembers((array)$tp->data);
                if ($dc = igk_getv($o, 'patterns')){
                    $np = $this->_getIncludeData($refdata, $dc);
                    $o['patterns'] = $np;
                }
                $repository[$tp->id] = $o;
            }
        } 
        $data = $this->m_data->jsonSerialize();        
        return $data;
    }
    /**
    * auto generate doc.
    * @param mixed $refdata
    * @param array $tab
    * @return array
    */
    private function _getIncludeData($refdata, array $tab){
        $tr = [];
        foreach($tab as $c){
            if (is_array($c)){
                $tr[] = $c;
                continue;
            }
            $id = spl_object_id($c);
            $tr[] = ['include'=>'#'.$refdata[$id]->id];
        }
        return $tr;
    }
    /**
    * auto generate doc.
    * @param array $tab
    * @return mixed
    */
    private function _unsetPrivateMembers(array $tab)
    {
        $g = array_keys($tab);
        foreach ($g as $tc) {
            if (false !== strpos($tc, "\0")) {
                unset($tab[$tc]);
            }
        }
        return $tab;
    }
    /**
     * chain repository 
     * @param mixed &$repository 
     * @param RegexMatcherPattern $q 
     * @return mixed|array|void
     * @throws Exception 
     */
    protected function _chainRepository(&$repository, RegexMatcherPattern $q)
    {
        $r = $this->_unsetPrivateMembers((array)$q);
        $patterns = igk_getv($r, 'patterns');
        if (empty($patterns)) {
            return;
        }
        $tc = [];
        $ref_include = 0;
        while (count($patterns) > 0) {
            $v_cp = $this->_unsetPrivateMembers((array)array_shift($patterns));
            if (false === ($ind = array_search($v_cp, $this->m_references))) {
                $this->m_references[] = $v_cp;
                $tc[] = $v_cp;
            } else {
                $idx = "repos_" . $this->get_identifier($ind);
                if ($q === $v_cp) {
                    $ref_include = '#' . $idx;
                }
                $n = (array)$v_cp;
                unset($n['patterns']);
                $trp = (array)$n;
                if (false !== ($l = array_search($v_cp, $this->m_data->patterns, true))) {
                    $this->m_data->patterns[$l] = [
                        'include' => '#' . $idx
                    ];
                }
                $repository[$idx] = $trp;
                $tc[] = [
                    'include' => '#' . $idx
                ];
            }
            if ($cpattern = igk_getv($v_cp, 'patterns')) {
                $v_tcp = [];
                foreach ($cpattern as $tpattern) {
                    $v_tcp = $this->_unsetPrivateMembers((array)$tpattern);
                    $ind = array_search($v_cp, $this->m_references);
                    igk_wln(__FILE__ . ":" . __LINE__, $ind);
                }
                $v_cp['patterns'] = $v_tcp;
            }
        }
        $r['patterns'] = $tc;
        if ($ref_include) {
            $r = ['include' => $ref_include];
        }
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $ind
    * @return mixed
    */
    private function get_identifier($ind)
    {
        return '_n_' . $ind;
    }
}