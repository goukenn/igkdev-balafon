<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssAnalyzer.php
// @date: 20250627 06:20:07
namespace IGK\Css\Analyzer;
use IGK\System\Text\RegexMatcherContainer;

/**
* auto generate doc.
* @package IGK\Css\Analyzer
* @author C.A.D. BONDJE DOUE
*/
class CssAnalyzer
{
    /**
    * Property: regex.
    * @var mixed
    */
    private $m_regex;
    /**
    * Listener: split listener.
    * @var mixed
    */
    private $m_splitListener;
    /**
    * Property: selectors.
    * @var mixed
    */
    var $selectors = [];
    /**
    * Property: classes.
    * @var mixed
    */
    var $classes = [];
    /**
    * Property: identifiers.
    * @var mixed
    */
    var $identifiers = [];
    /**
    * Property: medias.
    * @var mixed
    */
    var $medias = [];
    /**
    * Returns Split Listener.
    */
    public function getSplitListener()
    {
        return $this->m_splitListener;
    }
    /**
    * Sets Split Listerner.
    * @param null|ICssSplitListener $splitter
    */
    public function setSplitListerner(?ICssSplitListener $splitter)
    {
        $this->m_splitListener = $splitter;
    }
    /**
    * Initializes.
    */
    protected function initialize()
    {
        $rg = new RegexMatcherContainer;
        $brank = $rg->begin("\{", "\}", 'branck')->last();
        $str = $rg->appendStringDetection()->last();
        $comment = $rg->begin('\/\*', '\*\/', 'multi-comment')->last();
        $selector = $rg->begin('(?i)[\.a-z0-9 #\[\]]+', "(?=\{)", "selector")->last();
        $media = $rg->begin('@media\\b', '(?<=\})')->last();
        $rg->autoStore = false;
        $brank_def = $rg->begin("\{", "\}", 'branck')->last();
        $brank_def->patterns = [
            $str,
            $comment,
            $selector,
            $brank,
        ];
        $media_condition = $rg->begin('\\s+\(', '(?=\{)', 'media-condition')->last();
        $media_condition->patterns = [
            $media_condition
        ];
        $rg->autoStore = true;
        $media->patterns = [
            $media_condition,
            $brank_def,
        ];
        $rg->begin('@\\w+', '(?=;|\{)', 'skip');
        $this->m_regex = $rg;
    }
    /**
    * Analyse.
    * @param string $file
    */
    public function analyse(string $file)
    {
        $this->initialize();
        $src = file_get_contents($file);
        $pos = 0;
        $regex = $this->m_regex;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($e->tokenID == 'selector') {
                    if (!empty($v = trim($e->value))) {
                        $tv = $this->m_splitListener ? $this->m_splitListener->split($v) : [$v];
                        if (count($tv)>1){
                            $tv[] = $v;
                        }
                        $v = preg_replace("/\\s+/", " ", $v);
                        $this->selectors[$v] = 1;
                        while (count($tv) > 0) {
                            $v = trim(array_shift($tv));
                            if (preg_match("/\.\\w+\\b/", $v)) {
                                $this->classes[$v] = 1;
                            }
                            if (preg_match("/#\\w+\\b/", $v)) {
                                $this->identifiers[$v] = 1;
                            }
                        }
                    }
                }
                if ($e->tokenID == 'media-condition') {
                    if (!empty($v = trim($e->value))) {
                        $v = trim(preg_replace("/\\s+/", " ", $v));
                        $this->medias[$v] = 1;
                    }
                }
            }
        }
        ksort($this->medias);
        ksort($this->classes);
        ksort($this->identifiers);
        ksort($this->selectors);
    }
}