<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKProcessDocument.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\Controllers\NonVisibleControllerBase;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlNode;

/**
* Igkprocess document.
*/
class IGKProcessDocument extends NonVisibleControllerBase
{

    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
		return "process_script";
	}

    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
		parent::initComplete();
	}

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct();
	}

    /**
    * Processes File.
    * @param null|mixed $file
    */

    public function processFile($file=null)
	{
		$file = $file == null? base64_decode(igk_getr("file")): $file;
		if (igk_io_file_exists($file))
		{
			//
			$str = IO::ReadAllText($file);

			$out = preg_replace("/^\s*\/\/\/@@@(?P<value>(.)+)$/i", '///<summary>${1}</summary>', $str);

			igk_io_save_file_as_utf8(dirname(__FILE__)."/out.php_t", $out, true);

			$doc  = $this->ProcessDoc($str);
			$doc->renderAJX();
		}
		igk_exit();
	}

    /**
    * Processes Doc.
    * @param mixed $text
    */

    public function processDoc($text){

		$v_tab = array();
		//$v_c = preg_match_all("/\s*\/\/\/\<summary\>(?P<value>(.)+)\<\/summary\>/im", $text, $v_tab);
		$v_c = preg_match_all("/^\s*\/\/\/(?P<value>(.)+)$/im", $text, $v_tab);

		$v_d =  new HtmlNode("div");
		$v_n =  new HtmlNode("div");
		if ($v_c > 0){
		for($i = 0; $i < $v_c; $i++)
		{
			$p = $v_d->add("p");
			$p->add("span")->Content = $i;
			$p->add("li")->Content = $v_tab["value"][$i];
			$v_n->Content .= $v_tab["value"][$i];
		}
		}
		else{
			$v_d->Content = "no matched document";
		}
		return $v_n;
	}

} 