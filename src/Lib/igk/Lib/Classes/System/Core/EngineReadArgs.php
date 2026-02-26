<?php
// @author: C.A.D. BONDJE DOUE
// @file: EngineReadArgs.php
// @date: 20251021 08:07:28
namespace IGK\System\Core;

use Error;
use Exception;
use IGK\System\Console\Logger;
use IGK\System\DataArgs;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;

/**
 * 
 * @package IGK\System\Core
 * @author C.A.D. BONDJE DOUE
 */
class EngineReadArgs
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $context;

    /**
    * .ctr
    * @param mixed $context
    */
    function __construct($context)
    {
        $this->context = $context;
    }
    /**
     * 
     * @return mixed 
     */

    protected function evalContext()
    {
        extract(func_get_arg(1) ?? []);
        return @eval(func_get_arg(0));
    }
    /**
     * eval expression 
     * @param string $e 
     * @return void 
     */

    public function evalExpression(string $src)
    {
        $r = $this->evalContext('return $context->' . $src . ';', $this->context);
        if ($r && !is_string($r) && !is_numeric($r)) {
            $r = json_encode($r);
        }
        return $r;
    }
    /**
     * read curl branket definition  
     * @param string $src 
     * @param int &$position 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     */

    public function readCurlBranketDefinition(string $src, int &$position)
    {
        $regex = new RegexMatcherContainer;
        $pos = &$position;
        // define
        $bcurl = $regex->begin('\{', '\}', 'curl')->last();
        $v_detect_arg = $this->_global_regex($regex);
        $v_detect_arg = $regex->begin('\$\{(?P<name>[a-zA-Z_][a-zA-Z_0-9]*)\\b', '\}', 'detect-curl-args')->last();
        $string = $regex->appendStringDetection('string', true)->last();
        $bcurl->patterns = [
            $v_detect_arg,
            $string,
            $bcurl
        ];

        $v_detect_arg->patterns = [
            $string
        ];
        $handlers = $this->_handlers();
        $out = $this->_treat($src, $pos, $handlers, $regex);
        return $out;
    }
    /**
     * treat for curl
     * @param mixed $src 
     * @param mixed $pos 
     * @param mixed $handlers 
     * @param mixed $regex 
     * @return mixed 
     * @throws Error 
     * @throws Exception 
     */

    protected function _treat($src, $pos, $handlers, $regex)
    {
        $replaces = [];
        $out = '';
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                igk_is_debug() && Logger::info('[EngineReadArgs] tokenid: ' . $e->tokenID);
                if (is_null($e->parentInfo) && ($e->tokenID == 'curl')) {
                    $out = $e->value;
                    if ($replaces) {
                        $out = $this->_replaceList($out, $replaces, $e->from);
                    }
                    break;
                }
                if ($handle = igk_getv($handlers, $e->tokenID)) {
                    $handle($e, $replaces, $pos, $src);
                }
            }
        }
        return $out;
    }

    /**
    * auto generate doc.
    */
    protected function _handlers()
    {
        $handlers = [
            'detect-args' => function ($e, &$replaces) {
                $te = $e->value;
                $src = substr(substr($te, 0, -2), 4);
                $r = $this->evalExpression($src);

                $replaces[] = (object)['from' => $e->from, 'to' => $e->to, 'value' => $r];
            },
            'detect-curl-args' => function ($e, &$replaces) {
                $te = $e->value;
                $src = substr(substr($te, 0, -1), 2);
                $r = $this->evalExpression($src);
                $replaces[] = (object)['from' => $e->from, 'to' => $e->to, 'value' => $r];
            },
        ];
        return $handlers;
    }

    /**
    * auto generate doc.
    * @param mixed $o
    * @param mixed $replaces
    * @param int $from
    */
    protected function _replaceList($o, $replaces, int $from)
    {
        $ts = $o;
        $v = '';
        usort($replaces, function ($a, $b) {
            return $a->from <=> $b->from;
        });
        $offset = 0;

        while (count($replaces)) {
            $q = array_shift($replaces);
            if ($q->from < $from) {
                array_unshift($replaces, $q);
                break;
            }
            $v .= substr($ts, $offset, $q->from - $from - $offset) . $q->value;
            $offset = $q->to - $from;
        }
        $v .= substr($ts, $offset);
        return $v;
    }

    /**
    * auto generate doc.
    * @param mixed $regex
    */
    protected function _global_regex($regex)
    {
        $v_detect_arg = $regex->begin('\[\[:@(?P<name>[a-zA-Z_][a-zA-Z_0-9]*)\\b', '\]\]', 'detect-args')->last();

        return $v_detect_arg;
    }
    /**
     * 
     * @param string $src 
     * @param mixed $context 
     * @return void 
     */

    public static function TreatGlobalArgs(string $src, $context)
    {
        $c = new static(['context' => new DataArgs($context ?? [])]);
        $regex = new RegexMatcherContainer;
        $v = $c->_global_regex($regex);
        $handlers = $c->_handlers();
        $pos = 0;
        $out = $src;
        $replaces = [];
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($handle = igk_getv($handlers, $e->tokenID)) {
                    $handle($e, $replaces, $pos, $src);
                }
            }
        }
        $out = $c->_replaceList($out, $replaces, 0); 
        return $out;
    }
}
