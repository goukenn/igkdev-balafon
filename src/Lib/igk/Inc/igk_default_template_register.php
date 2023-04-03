<?php
// @file: igk_default_template_register.php
// @author: C.A.D. BONDJE DOUE
// @description: Html attribute template register
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\Encoding\ClassAttributeArrayValueEncoder;
use IGK\System\Html\HtmlAttributeExpression;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\Templates\BindingPipeExpressionInfo;

// +| definition of extra template depend on eval function 


if (in_array('eval', explode(',', ini_get('disable_functions')))){
    return;
}

function igk_template_if_attrib_expression($readerInfo, $attr, $v, $context, $setattrib){

    $g=(function() use ($readerInfo, $context, $setattrib, $attr){
        if ((func_num_args()!=1) ||  !is_string (func_get_arg(0))){
            igk_die("argument script not valid");
        } 
        if (!is_string($context) && igk_getv($context,'transformToEval')){    
            $readerInfo->setAttribute("igk:condition", func_get_arg(0));
            return null; 
        }   
        extract(igk_to_array($context)); 
        if(isset($ctrl)){
            extract(igk_extract_context($ctrl));
        }  
        
        $s="return ".func_get_arg(0).";";
        $_v= eval($s); 
        $readerInfo->setAttribute("igk:isvisible", $_v);
		$readerInfo->skipcontent = !$_v;
		$setattrib("igk:isvisible", $_v); 
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
}

function igk_template_update_attrib_expression($n, $attr, $v, $context, $setattrib){
	 $attrname = $attr;
	 while((strlen($attrname)>0) && ($attrname[0]=="*"))
		$attrname = substr($attrname, 1);
	 $g=(function($rv) use ($n, $context, $setattrib, $attrname){
        extract($tab = (array)$context);  
		$s = "return ".$rv.";";
		$v = @eval($s);
        $setattrib($attrname, $v);
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
}

function igk_template_update_attrib_piped_expression($n, $attr, $v, $context, $setattrib){
	 $attrname = $attr;
	 while((strlen($attrname)>0) && ($attrname[0]=="*"))
		$attrname = substr($attrname, 1);
	 (function($rv) use ($n, $context, $setattrib, $attrname){
		$v = igk_template_get_piped_value($rv, $context);
        $setattrib($attrname, $v);
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
}

function igk_template_get_piped_value($rv, $context){
	extract( igk_to_array($context));
    list($v, $pipe) = igk_str_pipe_args($rv, $c, 0);
    // language = 
    $tv = trim($v);
    $info = BindingPipeExpressionInfo::ReadInfo($tv);
    if ($info["type"]=="litteral"){
        // literal expression will be evaluate a
        // a lite
        $v = sprintf('"%s"', addslashes(igk_resources_gets($tv))); 
    }
    
    try{ 
	    $v = @eval("return $v;");  
        if ($e = error_get_last()){ 
            igk_dev_wln_e(__FUNCTION__."::Error:  ", $e, "source:".$rv, "output:".$v, $raw, $context);
        }
    }catch(ParseError $ex){
        igk_ilog("parse failed : ", $rv);
        if (igk_environment()->isDev()){
            igk_html_pre($v); 
            igk_trace();
        }
        throw $ex;
    }
    // igk_debug_wln_e("pipe....");
	$v = igk_str_pipe_value($v, $pipe);
	return $v;
}
function igk_template_bind_eval_transform($rv, $attrname, $setattrib){
    $rv = json_decode($rv) ?? $rv;
    // igk_wln_e("the rv", $rv);
    if (is_array($rv)){
        if (empty($rv)){
            return null;
        }
        $rv = 'implode(" ", array_filter(['.implode(", ", array_filter($rv)).']))';
    } else if (is_object($rv)){                    
        $rv = 'igk_css_get_class('.var_export($rv, true).')';
    } 
    $setattrib($attrname, new HtmlAttributeExpression('<?= '.$rv.' ?>'));
}
/**
 * bind single class attribute *class
 * @param mixed $n 
 * @param mixed $attr 
 * @param mixed $v 
 * @param mixed $context 
 * @param callable $setattrib 
 * @return void 
 * @throws IGKException 
 */
function igk_template_update_class_piped_expression($n, $attr, $v, $context, $setattrib){

    $attrname = $attr; 
    while((strlen($attrname)>0) && ($attrname[0]=="*"))
       $attrname = substr($attrname, 1);
 
    (function($rv) use ($n, $context, $setattrib, $attrname){

        if ($attrname=='class'){
            if (ClassAttributeArrayValueEncoder::DetectArrayList($rv)){
                $b = new ClassAttributeArrayValueEncoder;
                $b->strip_expression = true;
                if ($mp = $b->encode(htmlentities($rv))){
                    $rv = "igk_css_litteral({$mp})";
                }

            } 
        }

        if (!is_string($context) && igk_getv($context,'transformToEval')){             
            if ($rv){
                igk_template_bind_eval_transform($rv, $attrname, $setattrib);               
            }
            return null;
        }
       $v = igk_template_get_piped_value($rv, $context);
       if (is_array($v)){
            $data = [];
            foreach($v as $k=>$v){
                if (is_numeric($k)){
                    $data[] = $v;            
                }else if ($v){
                    $data[] = $k;
                }
            }
            $setattrib($attrname, implode(" ", $data));
       }
       else {
            $setattrib($attrname, $v);
       }
       return null;
   })(HtmlUtils::GetAttributeValue($v, $context, true));
 
}
 
// * --------------------------------------------------------------------------------------
// for loop : *for
// * --------------------------------------------------------------------------------------
igk_reg_template_bindingattributes("*for", function($reader, $attr, $v, $context, $setattrib){
    $g=(function($script) use ($context){      
        if (!is_string($context) && igk_getv($context,'transformToEval')){             
            $t = new \IGK\System\Html\HtmlBindingRawTransform("raw");
            $t->data = $context['raw'];
            $t->controller = $context['ctrl'];
            $t->root_context = $context['root_context'];
            return [ $t ]; 
        }        
        extract(igk_to_array($context));  
        return @eval((function(){           
            if (func_num_args()==1)
                return "return ".func_get_arg(0).";"; 
            
        })(HtmlUtils::GetAttributeValue($script, $context, true)));
    })($v);
    $reader->setInfos(["skipcontent"=>1, "attribute"=>$attr, "context-data"=>$g, "context"=>"expression", "operation"=>"loop", "for"=>$reader->getName()]);
    return null;
});

// * --------------------------------------------------------------------------------------
// for define : *class
// * --------------------------------------------------------------------------------------
igk_reg_template_bindingattributes("*classes", function($n, $attr, $v, $context, $setattrib){
    $g=(function($rv) use ($n, $context, $setattrib){
        extract(igk_to_array($context));
        if($d=igk_json_parse($rv)){
            $tab=[];
            foreach($d as $cl=>$cond){
                $s="return {$cond};";
                if(@eval($s)){
                    $tab[]="+".$cl;
                }
            }
            if(count($tab) > 0)
                $setattrib("class", implode(" ", $tab));
        }
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
	$n->setInfos(["attribute"=>$attr, "context-data"=>$g, "context"=>"bind-expression", "operation"=>"loop", "for"=>$n->getName()]);
    return null;
});
igk_reg_template_bindingattributes("*href", function($n, $attrname, $v, $context, $setattrib){
    $g=(function($rv) use ($n, $context, $setattrib, $attrname){
        if (!is_string($context) && igk_getv($context,'transformToEval')){             
            if ($rv){
                $attrname = substr($attrname, 1);
                igk_template_bind_eval_transform($rv, $attrname, $setattrib);               
            }
            return null;
        }
        extract(igk_to_array($context));
		$s="return {$rv};";
		$v = @eval($s);
        $setattrib("href", $v);
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
});
// * --------------------------------------------------------------------------------------
// for define : *visible
// * --------------------------------------------------------------------------------------
igk_reg_template_bindingattributes("*visible", 'igk_template_if_attrib_expression');
// * --------------------------------------------------------------------------------------
// for define : *if
// * --------------------------------------------------------------------------------------
igk_reg_template_bindingattributes("*if", 'igk_template_if_attrib_expression'); 

igk_reg_template_bindingattributes("**", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*value", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*title", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*src", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*action", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*class", 'igk_template_update_class_piped_expression');
igk_reg_template_bindingattributes("*style", 'igk_template_update_style_piped_expression');
igk_reg_template_bindingattributes("*placeholder", 'igk_template_update_attrib_piped_expression');

// + | template-attr
igk_reg_template_bindingattributes(IGK_ENGINE_ATTR_TEMPLATE_REF_ATTR, function($n, $attrname, $v, $context, $setattrib, $m=null){
    $setattrib($attrname, null);
    $v = stripslashes($v);
    $deco = json_decode($v);
    list($v_tnode, $v_expression) = (array)$deco;
    if (igk_getv($context, 'transformToEval')){        
        $setattrib($v_tnode, '<?= '.$v_expression.' ?>'); 
        return;
    }
    $root_context = $context['root_context'];
    $key = igk_getv($context, 'key');    
    $raw = igk_getv($root_context->raw, $key ?? 0);
    $ctrl = $root_context->ctrl;

    $b = (function(){   
        extract(igk_to_array(func_get_arg(1)));
        return @eval("return ".func_get_arg(0).";");
    })($v_expression, compact('raw', 'ctrl'));
    $setattrib($v_tnode, $b); 
    return $v;
});

igk_reg_template_bindingattributes("*template-attr", function(){
    igk_wln_e("template-attr ok");
});