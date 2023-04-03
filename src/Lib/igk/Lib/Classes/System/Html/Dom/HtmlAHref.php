<?php
// @file: IGKHtmlAHref.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

use IGK\Helper\UriHelper;
use IGK\System\Html\IHtmlGetValue;
use IGKObject;
use IGKValidator;

/**
 * 
 * @package IGK\System\Html\Dom
 */
class HtmlAHref extends IGKObject implements IHtmlGetValue
{
    const OWNER = 2;
    const URI = 1;
    private $_;
    ///<summary></summary>
    ///<param name="a"></param>
    public function __construct($a)
    {
        $this->_ = array();
        $this->_[self::OWNER] = $a;
    }
    ///<summary></summary>
    ///<param name="option"></param>
    private function _checkLnk($option)
    {
        $bck = $this->getUri();
        $owner = $this->getOwner();
        if (!is_string($bck)) {
            if (is_object($bck) && ($bck instanceof IHtmlGetValue)) {
                $bck = $bck->getValue();
            } else {
                $bck = '' . $bck;
            }
        }
        $m = igk_xml_is_mailoptions($option);
        if (is_null($bck) || ($bck == "#")) {
            return $bck;
        }
        if (IGKValidator::IsUri($bck)) {
            if (igk_xml_is_cachingrequired($option)) {
                $s = igk_io_baseuri();
                if (strstr($bck, $s)) {
                    $uri = igk_str_rm_start($bck, $s);
                    if (empty($uri)){
                        $uri = "/";
                    }
                    return $uri;
                }
            }
            return $bck;
        }
        $s = UriHelper::UriSysReplace($bck);
        $bck = $s;
        $dx = igk_getv(explode("?", str_replace(igk_io_baseuri(), "", $bck)), 0);
        if (!empty($dx)) {
            $dx = igk_io_basedir() . $dx;
            if (is_dir($dx) && !preg_match("/\/$/", $dx)) {
                $m = igk_getv(explode("?", $bck), 0);
                $r = str_replace($m, igk_io_fullpath2fulluri($dx) . "/", $bck);
                return $r;
            }
        }
   
        if ($owner->domainLink) {
            if (preg_match("/^\/[^\/](.)+$/", $bck) && !igk_sys_is_subdomain()) {
                $u = igk_io_baseuri() . $bck;
                return $u;
            }
        }
        if ($owner->domainLink && igk_sys_is_subdomain()) {
            $u = "";
            if (IGKValidator::IsUri($bck)) {
                $u = $bck;
            } else {
                $uri = new HtmlUri();
                $u = igk_sys_srv_uri_scheme() . "://" . igk_sys_domain_name() . $uri->getValue($option);
            }
            $dn = igk_get_domain($u);
            if (!empty($dn) && (igk_sys_current_domain_name() != $dn)) {
                $u .= (strpos($u, '?') !== false) ? "&" : "?";
            }
            return $u;
        }
        if ($m) {
            $uri = new HtmlUri();
            $uri->setValue($bck);
            return $uri->getValue($option);
        } 
        // else {
        //     if (!IGKValidator::IsUri($bck) || preg_match("#\.\/#", $bck)) {
        //     }
        // }
        return $bck;
    }
    ///<summary></summary>
    public function getDesignMode()
    {
        return igk_is_design_mode();
    }
    ///<summary></summary>
    public function getOwner()
    {
        return igk_getv($this->_, self::OWNER);
    }
    ///<summary></summary>
    public function getUri()
    {
        return igk_getv($this->_, self::URI);
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options = null)
    {
        return $this->_checkLnk($options);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setUri($v)
    {
        $this->_[self::URI] = $v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setValue($v)
    {
        $this->setUri($v);
    }
}
