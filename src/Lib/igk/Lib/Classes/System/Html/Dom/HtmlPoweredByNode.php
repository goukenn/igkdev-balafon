<?php
// @file: IGKHtmlHookNode.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use function igk_resources_gets as __;
use IGKApp;
use IGKHtmlDoc;

/**
* Html powered by node.
* @package IGK\System\Html\Dom
*/
class HtmlPoweredByNode extends HtmlNode{

    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";
    /**
     * Returns the singleton instance of this node.
     * @return static
     */

    public static function getItem(){
        static $_instance;
        if ($_instance==null){
            $_instance = new self();
        }
        return $_instance;
    }
    /**
     * Determines whether the powered-by message is visible based on app config.
     * @return bool
     */

    public function getIsVisible()
    {
        return !IGKApp::GetConfig("no_powered_message") && !empty($this->getContent());
    }
    /**
     * Constructor.
     */
    private function __construct()
    {
        parent::__construct();
        $this["class"] = "igk-powered no-selection no-contextmenu google-Roboto";
        $this["igk-no-contextmenu"]="1";
    }
    /**
     * Builds and returns the powered-by HTML content string.
     * @return string|null
     */

    public function getContent()
    {
        $uri = IGKApp::GetConfig('powered_uri');
        $msg = IGKApp::GetConfig('powered_message');
        if ($uri && $msg){
            $data = "<a href=\"{$uri}\" title=\"powered target\">".$msg."</a>";
            return __("Powered by {0}", $data);
        }
    }
    /**
     * Determines whether this node should be included in the rendered output.
     * @param mixed $options Render options, may contain a Document instance.
     * @return bool
     */

    protected function _acceptRender($options = null):bool
    {
        if (!$this->getIsVisible()){
            return false;
        }
        $doc = null;
        $options && ($doc = igk_getv($options, "Document"));
        if (($doc instanceof IGKHtmlDoc) && $doc->getNoPowered()){
            return false;
        }
        return true;
    }
}
