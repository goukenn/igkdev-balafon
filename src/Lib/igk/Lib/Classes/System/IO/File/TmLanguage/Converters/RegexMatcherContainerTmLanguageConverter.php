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
class RegexMatcherContainerTmLanguageConverter{
    private $m_data;
    private $m_references = [];
    protected function _removeType($a){
        $tab = [$a];
        $mark = [];
        while(count($tab)>0){
            $q = array_shift($tab);
            if (($tp = in_array($q, $mark))!==false){
                continue;
            }
            $mark [] = $q;
            if (is_array($q)){
                unset($q['type']);
            } else if (is_object($q)){
                unset($q->type);
            }
            if ($p = (array)igk_getv($q, 'patterns')){
                $tab = array_merge($tab, $p);
            }
        }
        return $a;
    }
    /**
     * convert to tm language 
     * @param RegexMatcherContainer $ctn 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     * @throws Error 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function Convert(RegexMatcherContainer $ctn){
        $this->m_data = Activator::CreateNewInstance(RegexMatcherContainerTmDefinition::class, (object)[
                'version'=>'1.0',
                'repository'=>[],
                'patterns'=>[],
                '$scope'=>''        
            ]);
        $this->m_references = [];
        if ($d = $ctn->getMatcher()){
            while(count($d)>0){
                $q = array_shift($d);
                if ($q instanceof RegexMatcherPattern){ 
                    if (false === array_search($q, $this->m_references, true)){
                        $this->m_references[] = $q; 
                    }
                    $r = $this->chainRepository($this->m_data->repository, $q);
                    $json = JSon::Encode($r, JSonEncodeOption::IgnoreEmpty());
                    $this->m_data->patterns[] = $this->_removeType(json_decode($json));
                }else{
                    igk_die('not a macher pattern');
                }
            }
        } 
        $obj = (object)(array)$this->m_data;
        $p = json_decode(JSon::Encode($obj, JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT));
        $obj = $this->_removeType($p);
        return $obj;
    }

    function chainRepository(& $repository, RegexMatcherPattern $q){
        $r = (array)$q;
        $patterns = igk_getv($r, 'patterns');
        $tc = [];
        $ref_include = 0;
        while(count($patterns)>0){
            $v_cp = array_shift($patterns);

            if (false === ($ind = array_search($v_cp, $this->m_references))){
                $this->m_references[] = $v_cp;
                $tc[] = $v_cp;
            }else{
                $idx = "repos_".$this->get_identifier($ind);
                if ($q === $v_cp){ 
                    $ref_include = '#'.$idx;
                }
                $n = (array)$v_cp;
                unset($n['patterns']);
                $trp = (array)$n;
                $trp['patterns'] = & $tc;
                 $repository[$idx] = $trp;
                $tc[] = [
                    'include'=>'#'.$idx
                ];

            }

        }
        $r['patterns'] = $tc;
        if ($ref_include){
            $r = ['include'=>$ref_include];
        }
        
        return $r;
    }
    private function get_identifier($ind){
        return '_n_'.$ind;
    }
}