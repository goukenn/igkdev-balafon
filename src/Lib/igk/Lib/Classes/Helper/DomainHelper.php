<?php
// @author: C.A.D. BONDJE DOUE
// @file: DomainHelper.php
// @date: 20230110 14:10:59
namespace IGK\Helper;
/**
* 
* @package IGK\Helper
*/
/**
* auto generate doc.
* @package IGK\Helper
*/
class DomainHelper{
    /**
    * auto generate doc.
    * @param string $hayhstack
    * @return bool
    */
    public static function IsInSameDomain(string $domain, string $hayhstack ):bool{
        return igk_get_domain_name($domain) == igk_get_domain_name($hayhstack);
    }
}