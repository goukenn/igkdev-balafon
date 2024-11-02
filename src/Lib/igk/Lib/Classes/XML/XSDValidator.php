<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XSDValidator.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\XML;

use DOMDocument;

///<summary></summary>
class XSDValidator
{
    /**
     * validate with source 
     * @param string $source xml source
     * @param string $xsd xsd source
     * @param mixed|null $error allow error
     * @return bool|void return true if succeed
     */
    public static function ValidateSource(string $source, string $xsd, array & $error = null)
    {
        // $source = preg_replace('/\<\?.+\?\>/m', '', $source);
        $dom = new DOMDocument;
        $dom->loadXml($source);
        if ($error !== null) {
            libxml_use_internal_errors(true);
        }
        
        if ($success = !$dom->schemaValidateSource($xsd)) {
            $errors = libxml_get_errors();      
            foreach ($errors as $error) {
                switch ($error->level) {
                    case LIBXML_ERR_WARNING:
                        $type = "WARNING";
                        break;
                    case LIBXML_ERR_ERROR:
                        $type = "ERROR";
                        break;
                    case LIBXML_ERR_FATAL:
                        $type = "FATAL";
                        break;
                }
                $error = ["type" => $type, "message" => $error->message];
            }
        }
        return !$success;
        
    }
    /**
     * @return ?bool
     */
    public static function ValidateSourceUri(string $source, string $uri): ?bool{
       
        
        if ($g = @file_get_contents($uri)){
            if (!XSDValidator::ValidateSource($source, $g)){
                igk_ilog(" not a good validator ");
                return false;
            }
            return true;
        }
        error_clear_last();       
        return null;
    }
}
