<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriDetector.php
// @date: 20221122 19:53:46
namespace IGK\DocumentParser;

use IGK\System\IO\Path;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\DocumentParser
 */
class UriDetector
{
    private $m_regex;
    const CSS_URL = '/url\s*\((?<path>(((\.)?\.\/|(?P<protocol>[a-z0-9]+):\/\/))[^\)\#\?\,\+\ ]+)(?P<extra>([^\)]+))?\)/i';
    public function __construct()
    {
        $this->_initRegex();
    }
    protected function _initRegex()
    {
        $domain = '(?P<domain>\w+(\.[\w0-9]+)+)';
        $path = '(\/(?P<path>[^\#\?\'\"\),\s]+)?)?'; //'(?P<path>[^#\s\\\'\"]+)';
        $query = '(\?(?P<query>[^\#\?\'\"\,\s)]+)?)?';
        $hash = '(\?(?P<hash>[^\#\?\'\"\),\s]+)?)?';
        $this->m_regex = '#(?P<uri>((?P<scheme>http(s)?):)?\/\/)' . $domain . $path . $query . $hash . '?#im';
    }
    public function match($src)
    {
        if ($g = preg_match_all($this->m_regex, $src, $out)) {
            $tab = [];
            for ($i = 0; $i < $g; $i++) {
                $match = new UriDectectorMatch;
                foreach ($match as $k => $v) {
                    if ($k == 'uri') {
                        continue;
                    }
                    $r = igk_getv($out, $k);
                    $match->$k = $r ? igk_getv($r, $i) : null;
                }
                $match->uri = $out[0][$i];
                $tab[] = $match;
            }
            return $tab;
        }
        return false;
    }

    /**
     * get source string from data
     * @param mixed $src source string 
     * @param null|string $form from data
     * @return null|UriDectectorMatch[] 
     * @throws IGKException 
     */
    public function cssUrl($src, ?string $from = null)
    {
        $tab =  null;
        if ($g = preg_match_all(self::CSS_URL, $src, $out)) {
            for ($i = 0; $i < $g; $i++) {
                $match = new UriDectectorMatch;
                $r = igk_getv($out, 'path');
                $gr = $r ? trim(igk_getv($r, $i) ?? '', '"\'') :null;
                $match->path = $gr ? Path::FlattenPath($gr) : null; //$v;
               //  $match->match_path = $gr;

                if ($match->path && ($v_tg = parse_url($match->path))) {
                    $match->domain = igk_getv($v_tg, 'host');
                    $match->scheme = igk_getv($v_tg, 'scheme');
                }
                $match->uri = trim($out[0][$i], '"\'');
                $tab[] = $match;
                if ($from) {
                    // + | update from info
                    $match->fromUri = $from;
                }
            }
        }
        return $tab;
    }
}
