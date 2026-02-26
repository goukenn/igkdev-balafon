<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPScriptMixedDetector.php
// @date: 20250704 13:57:49
namespace IGK\System\IO\File;
use Error;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
/**
 * 
 * @package IGK\System\IO\File
 * @author C.A.D. BONDJE DOUE
 */
class PHPScriptMixedDetector
{

    /**
    * Property: regex.
    * @var mixed
    */
    private $m_regex;

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->initialize();
    }

    /**
    * Initializes.
    */
    protected function initialize()
    {
        $regex = new RegexMatcherContainer;
        $regex->autoStore = false;
        $string = $regex->appendStringDetection()->last();
        $here_doc = [];
        $comments = [];
        $comments[] = $regex->appendSingleLineComment()->last();
        $comments[] = $regex->appendMultilineComment()->last();
        RegexMatcherUtility::AppendPhpHereDoc($regex, $here_doc);
        $regex->autoStore = true;
        // detect until last <?php
        // $regex->match('((?<=\\? >))?[\\s\\S]+(?=<\\?)', 'mixed');
        $l = $regex->begin('(?=<\\?(=|php\\b))', '(?<=\\?>)', 'php-block')->last();
        $l->patterns = [
            ['match' => '<\\?(=|php\\b)', 'tokenID' => 'block-start'],
            $string,
            ['patterns' => $comments],
            ['patterns' => $here_doc]
        ];
        $regex->match('(?:[\\s\\S]*?)(?=<\\?)', 'mixed');
        $regex->match('(?<=\\?>)(([\\s\\S]*?)(?=\\<\\?)|([\\s\\S]*))', 'mixed');
        $this->m_regex = $regex;
    }
    /**
     * 
     * @param string $file 
     * @return object 
     * @throws Error 
     */

    public function detectFromFile(string $file)
    {
        return $this->detectFromSource(file_get_contents($file));
    }
    /**
     * 
     * @param string $source 
     * @return object|{mixed:string, source:array} 
     * @throws Error 
     */

    public function detectFromSource(string $source)
    {
        $regex = $this->m_regex;
        $regex->resetTreatment();
        $sr1 = $source;
        $pos = 0;
        $mixed = false;
        $start = false;
        $source = [];
        while ($g = $regex->detect($sr1, $pos)) {
            if ($e = $regex->end($g, $sr1, $pos)) {
                if ($e->tokenID == 'mixed') {
                    if (!$mixed) {
                        if (!$start && empty($s = trim($e->value))) {
                            continue;
                        }
                        $mixed = true;
                    }
                    $source[] = $e->value;
                }
                switch ($e->tokenID) {
                    case 'block-start':
                        $start = true;
                        break;
                    case 'php-block':
                        $source[] = $e->value;
                        break;
                }
                if (igk_is_debug()) {
                    Logger::info('tokenID:' . $e->tokenID);
                    Logger::print($e->value);
                }
            }
        }
        return (object)compact('mixed','source');
    }
}