<?php
// @file: igk_default_template_register.php
// @author: C.A.D. BONDJE DOUE
// @description: Html attribute template register
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\HtmlUtils;

// +| definition of extra template depend on eval function 


if (in_array('eval', explode(',', ini_get('disable_functions')))){
    return;
}
 

function igk_template_update_attrib_expression($n, $attr, $v, $context, $setattrib){
	 $attrname = $attr;
	 while(strlen($attrname)>0 && ($attrname[0]=="*"))
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
	 while(strlen($attrname)>0 && ($attrname[0]=="*"))
		$attrname = substr($attrname, 1);
	 $g=(function($rv) use ($n, $context, $setattrib, $attrname){
		$v = igk_template_get_piped_value($rv, $context);
        $setattrib($attrname, $v);
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
}

function igk_template_get_piped_value($rv, $context){
	extract( igk_to_array($context));
    list($v, $pipe) = igk_str_pipe_args($rv, $c, 0);   
    try{
	    $v = eval( "return $v;");    
        if ($e = error_get_last()){
            igk_dev_wln_e(__FUNCTION__."::Error:", $e, "source:".$rv, "output:".$v, $raw, $context);
        }
    }catch(ParseError $ex){
        igk_wln("parse failed : ", $rv);
    }
	$v = igk_str_pipe_value($v, $pipe);
	return $v;
}


igk_reg_template_bindingattributes("*for", function($reader, $attr, $v, $context, $setattrib){
    $g=(function($script) use ($context){
        extract(igk_to_array($context));  
        return @eval((function(){
            // if (igk_is_debug()){
            //     igk_trace();
            //     igk_wln_e(__FILE__.":".__LINE__, func_get_arg(0));
            // }
            if (func_num_args()==1)
            return "return ".func_get_arg(0).";"; 
        })(HtmlUtils::GetAttributeValue($script, $context, true)));
    })($v);
    $reader->setInfos(["skipcontent"=>1, "attribute"=>$attr, "context-data"=>$g, "context"=>"expression", "operation"=>"loop", "for"=>$reader->getName()]);
    return null;
});
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
igk_reg_template_bindingattributes("*href", function($n, $attr, $v, $context, $setattrib){
    $g=(function($rv) use ($n, $context, $setattrib){
        extract(igk_to_array($context));
		$s="return {$rv};";
		$v = @eval($s);
        $setattrib("href", $v);
        return null;
    })(HtmlUtils::GetAttributeValue($v, $context, true));
    return null;
});

igk_reg_template_bindingattributes("*visible", function($readerInfo, $attr, $v, $context, $setattrib){

    $g=(function() use ($readerInfo, $context, $setattrib, $attr){
        if ((func_num_args()!=1) ||  !is_string (func_get_arg(0))){
            igk_die("argument script not valid");
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
});


igk_reg_template_bindingattributes("**", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*value", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*title", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*src", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*action", 'igk_template_update_attrib_piped_expression');