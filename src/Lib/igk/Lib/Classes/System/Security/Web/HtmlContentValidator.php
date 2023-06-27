<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlContentValidator.php
// @date: 20230303 21:32:35
namespace IGK\System\Security\Web;

use IGK\System\Html\HtmlRenderer;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class HtmlContentValidator extends MapContentValidatorBase{
    protected $notvalid_msg = "not a valid content.";
    /**
     * remove tags for security reason
     * @var string[]
     */
    var $skipTag = ['style','script'];

    protected  function validate(&$value, $key) :bool {   
        $skip_data = function(string $a) {           
            $dv = igk_create_notagnode(); 
            $dv->load($a); 
            $options = HtmlRenderer::CreateRenderOptions();
            $options->skipTags = $this->skipTag;
            return $dv->render($options); 
        };  
        // remove script tag
        if (is_string($value)){
            $value =  $skip_data($value); 
            return true;
        } 
        if (is_array($value)){           
            $value = array_map($skip_data, array_filter($value));
            return true;
        }
        return false;
    }

}