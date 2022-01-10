<?php

namespace IGK\System\Templates;
 
use IGK\System\Html\HtmlUtils;
use ReflectionFunction;

/**
 * reprenet a rendering template engine
 * @package IGK\System\Templates
 */
class TemplateEngine
{
    var $start;
    private function getFuncArgInfo($rffunc, array $creationargs = null)
    {
        $info = new TemplateArgInfo();
        $info->setCreationArg($creationargs);
        // igk_wln_e($rffunc->getNumberOfParameters() );
        if ($rffunc->getNumberOfParameters() > 0) {
            foreach ($rffunc->getParameters() as $c) {
                // igk_wln_e("push ....".$c->getName(), " ? ".$c->isOptional());
                if ($c->isOptional()) {
                    break;
                }
                $info->push($c);
            }
        }
        return $info;
    }
    public function Render($node, $options = null)
    {
        $infos = [];
        $tab = array($node);
        $rdinfo = (object)[
            "parent" => null,
            "rdinfo" => null,
            "tagname" => null
        ];
        
        $s = "";
        if ($options === null) {
            $options = igk_createobj();
        }
        if (!isset($options->templateData)) {
            $options->templateData = igk_createobj();
        }
        if (!property_exists($options, "noUseTemplateData")){
            $options->noUseTemplateData = 0;
        }
        $indent = $options->Indent;
        $lf = "";
        $indent_str = "";
        $depth = 0;
        while ($node = array_pop($tab)) {
            if ($node->getFlag("NO_TEMPLATE")) {
                // $rdinfo = $rdinfo->$rdinfo;
                continue;
            }
            if (method_exists($node, "templateData")) {
                $s .= $node->templateData($options);
                continue;
            }
            $attr = "";
            $tagname = HtmlUtils::GetGeneratedTagname($node);
            
      
            if (!empty($rdinfo->content)){
                $s .= $rdinfo->content;
            }

            if ($fc = $node->getFlag(IGK_NODETYPENAME_FLAG)) {
                if (!($info = igk_getv($infos, $fc))) {
                    $d = new ReflectionFunction($fc);
                    $info = $this->getFuncArgInfo($d, $node->getNodeCreationArgs());
                    $infos[$fc] = $info;
                }
                // info to args
                if (!empty($args = $info->getArgs())) {
                    $attr .= "igk:args=\"" . $args . "\"";
                }
                if (strpos($fc, "igk_html_node_") === 0) {
                    $tagname = "igk:" . strtolower(substr($fc, strlen("igk_html_node_")));
                }
            } else {
                $attr = $node->getAttributeString($options);
                if (isset($options->ns)) {
                    array_push($ns, $options->ns);
                }
            }
            // echo "<!-- ".$tagname .":".$rdinfo->tagname."-->\n";
            // if ($tagname=="igk:article"){

            //     igk_wln_e($p === $rdinfo->parent, $rdinfo->tagname);
            // }

            $s .= $lf . $indent_str . "<" . $tagname;

            if (!empty($attr)) {
                $s .= " " . $attr;
            }
            if (!$this->start) {
                $s .= " xmlns:igk=\"https://schemas.igkdev.com/template\"";
                $this->start = true;
            }

            $inner = IGK_STR_EMPTY;
            if (!$node->getFlag("NO_CHILD")) {
                $c_childs = $node->GetRenderingChildren($options);
                $c_tchild = igk_count($c_childs);
                if (!$node->getFlag("NO_CONTENT"))
                    $inner .= HtmlUtils::GetContentValue($node, $options);

                if ($c_tchild > 0) {

                    $s .= ">";
                    $rdinfo = (object) [
                        "parent" => $node,
                        "rdinfo" => $rdinfo,
                        "content" => $inner,
                        "tagname" => $tagname,
                        "count" => $c_tchild
                    ];
                    $tab = array_merge($tab, array_reverse($c_childs));
                    if ($indent) {
                        $lf = "\n";
                        $depth++;
                        $indent_str = str_repeat("\t", $depth);
                    }
                    continue;
                }
            }

            if (trim($inner) == "") {
                $s .= "/>";
            } else {
                $s .= ">";
                $s .= $inner;
                $s .= "</" . $tagname . ">";
            }
            $rdinfo->count--;
        
            if ($rdinfo->count <= 0) {
                //close rd  - info - tils parent
                do{
                if ($indent) {
                    $lf = "\n";
                    $depth--;
                    $indent_str = str_repeat("\t", $depth);
                }
                $s .= $lf . $indent_str . "</" . $rdinfo->tagname . ">";// aaa {$rdinfo->count}>";       
                $rdinfo = $rdinfo->rdinfo;
                $rdinfo->count--;
                //igk_wln_e($rdinfo->count);
                }
                while( $rdinfo && ($rdinfo->count <=0) );  
            }
        }
        while ($rdinfo && $rdinfo->tagname) {
            if ($indent) {
                $lf = "\n";
                $depth--;
                $indent_str = str_repeat("\t", $depth);
            }
            $s .= $lf . $indent_str . "</" . $rdinfo->tagname . ">";
            $rdinfo = $rdinfo->rdinfo;
        }
        $this->start = false;

        if (!$options->noUseTemplateData) {
            $data = "";
            foreach ($options->templateData as $k => $v) {
                $data .= "\$" . $k . " = unserialize('" . serialize($v) . "');\n";
            }
            if (!empty($data)) {

                $data .= "<?php \n" . $data . " ?>\n";
            }
            $s = $data . $s;
        }
        return $s;
    }
}
