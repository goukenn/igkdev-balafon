<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Uri.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\System;

use IGKException;

/**
 * manage uri
 * @package IGK\System
 */
class Uri
{
    const TEMP_ENV_KEY = "sys://temp_uri";
    private $m_protocol;
    private $m_domain;
    private $m_path;
    private $m_query;
    private $m_segment;
    private $m_options;
    /**
     * build a query arg query
     * @param mixed $uri 
     * @param null|array $append_args 
     * @param null|array $exclude_query 
     * @return string 
     * @throws IGKException 
     */
    public static function BuildUri($uri, ?array $append_args = [], ?array $exclude_query = null, $append = true)
    {
        $q = parse_url($uri);
        if ($c = igk_getv($q, "query", null)) {
            parse_str($c, $ctab);
            $c = $ctab;
        } else {
            $c = [];
        }

        if ($append_args) {
            $c = array_merge($c, $append_args);
        }
        if ($exclude_query) {
            foreach ($exclude_query as $k) {
                unset($c[$k]);
            }
        }
        $cpath = igk_getv($q, "path");
        $query = http_build_query($c);
        if (!empty($query)) {
            $cpath .= "?" . $query;
            if ($append) {
                $cpath .= "&";
            }
        } else {
            if ($append) {
                $cpath .= "?";
            }
        }
        return $cpath;
    }

    public static function get(string $name, $default = null)
    {
        return igk_environment()->getArray(self::TEMP_ENV_KEY, $name, $default);
    }
    public static function register(string $name, string $uri)
    {
        igk_environment()->setArray(self::TEMP_ENV_KEY, $name, $uri);
    }
    /**
     * create from uri
     * @param string $uri 
     * @return void 
     */
    public function __construct(string $uri)
    {
        self::_Parse($this, $uri);
    }
    private static function _Parse($n, string $uri)
    {
        $g = parse_url($uri);
        $n->m_domain = igk_getv($g, "host");
        $n->m_path = igk_getv($g, "path");
        $n->m_protocol = igk_getv($g, "scheme");
    }
    /**
     * get site uri. combine protocol and domain name
     * @return string 
     */
    public function getSiteUri()
    {
        return implode("", array_filter([
            ($this->m_protocol && $this->m_domain) ? $this->m_protocol . "://" : ($this->m_domain ?  "//" : null),
            $this->m_domain
        ]));
    }
    /**
     * get path
     * @return ?string 
     */
    public function getPath()
    {
        return $this->m_path;
    }
    /**
     * get the domain
     * @return ?string
     */
    public function getDomain(): ?string{
        return $this->m_domain;
    }
    /**
     * get full uri
     * @return string 
     */
    public function getFullUri(): string
    {
        return implode("", array_filter([
            ($this->m_protocol && $this->m_domain) ? $this->m_protocol . "://" : ($this->m_domain ?  "//" : null),
            $this->m_domain,
            $this->m_path,
            implode("", [
                $this->m_query ? "?" . $this->m_query : null,
                $this->m_options ? ";" . $this->m_options : null,
                $this->m_fragment ? "#" . $this->m_fragment : null
            ])
        ]));
    }
}
