<?php
// @author: C.A.D. BONDJE DOUE
// @filename: xml.php
// @date: 20220831 14:18:33
// @desc:
/**
* create xml node
* @param mixed $d
* @return mixed
*/
function igk_create_xmlnode($d)
{
    $c = new \IGK\System\Html\XML\XmlNode($d);
    return $c;
}
/**
* shorcut to create xml data
* @return mixed
*/
function igk_create_xml_cdata()
{
    return new \IGK\System\Html\XML\XmlCDATA();
}
/**
* create xlst node
* @return mixed
*/
function igk_create_xslt_node()
{
    $xsl = igk_create_xmlnode("xsl:stylesheet");
    $xsl["version"] = "1.0";
    $xsl["xmlns"] = "http://www.w3.org/1999/xhtml";
    $xsl["xmlns:xsl"] = "http://www.w3.org/1999/XSL/Transform";
    include_once(IGK_LIB_DIR . "/igk_xsl_definition.php");
    $xsl->setTempFlag("RootNS", "igk_xsl_creator_callback");
    return $xsl;
}