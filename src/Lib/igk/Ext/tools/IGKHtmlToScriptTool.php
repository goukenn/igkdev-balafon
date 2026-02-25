<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHtmlToScriptTool.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\Controllers\ToolControllerBase;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlUtils;
use IGK\XML\XMLNodeType;

class IGKHtmlToScriptTool extends ToolControllerBase
{
	/**
	 * Complete the initialisation of the tool.
	 *
	 * @param mixed $context Optional initialisation context.
	 * @return void
	 */
	protected function initComplete($context=null)
	{
		parent::initComplete();
	}
	/**
	 * Return the URI of the tool image icon.
	 *
	 * @return string URI of the HTML-to-script tool image.
	 */
	public function getImageUri(){
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_c2script.jpeg"));
		return $uri;
	}
	/**
	 * Convert submitted HTML code to a PHP script and send it as a download.
	 *
	 * @return void
	 */
	public function convert()
	{
		$c = igk_getr("clHtmlCode");
		$dv =  HtmlNode::CreateWebNode("div");
		$dv->Load($c);

		$out ="<?php\n";
		$out .= self::ConvertToScript($dv);
		$out .="?>";
		igk_download_content("script.php", strlen($out) , $out);
		igk_exit();
	}
	/**
	 * Build PHP assignment statements for all attributes of a node.
	 *
	 * @param mixed $k HTML node whose attributes are converted.
	 * @return string PHP code string containing attribute assignments.
	 */
	private static function GetAttribute($k)
	{
		$out = "";
		$t = $k->Attributes;
		if ($t)
		{
			foreach($t as $m=>$s)
			{
				$out .= "\$d[\"".$m."\"] = \"".trim(HtmlUtils::GetValue($s))."\";\n";
			}
		}
		return $out;
	}
	/**
	 * Build a PHP assignment statement for a node's text content.
	 *
	 * @param mixed $k HTML node whose text content is converted.
	 * @return string|null PHP code string or null when there is no content.
	 */
	private static function GetTextContent($k)
	{
			if ($k->Content)
				{
		return  "\$d->Content = \"".trim(HtmlUtils::GetValue($k->Content))."\";\n";
				}
			return null;

	}
	/**
	 * Recursively convert child nodes to PHP script statements.
	 *
	 * @param mixed $dv    Parent HTML node whose children are processed.
	 * @param mixed $owner Optional owner node; null when at the root level.
	 * @return string PHP code string representing the child nodes.
	 */
	private static function GetChild($dv,  $owner=null)
	{
		$out = "";
		$v_child = $dv->Childs;
			if ($v_child)
			{
				foreach($v_child as $k)
				{

					switch($k->NodeType)
					{
						case XMLNodeType::TEXT:
							if ($owner)
							{
								$out .= self::GetTextContent($k);
							}
						break;
						default:
						if ($owner){
							$out .= "\$d = \$d->add(\"".$k->TagName."\");\n";
						}
						else{
							$out .= "\$d =  HtmlNode::CreateWebNode(\"".$k->TagName."\");\n";
						}
						// if ($k->Content)
						// {
							// $out .= "\$d->Content = ".HtmlUtils::GetAttributeValue($k->Content).";\n";
						// }
						break;
					}
					//render attributes
					$t = $k->Attributes;
					if ($t)
					{
						foreach($t as $m=>$s)
						{
							$out .= "\$d[\"".$m."\"] = \"".trim(HtmlUtils::GetValue($s))."\";\n";
						}
					}
					$out .= self::ConvertToScript($k, true);
				}
			}
			if ($owner)
			{
				$out .= "\$d = \$d->ParentNode;\n";
			}
			else{
				$out .= "unset(\$d);\n";
			}
			return $out;
	}

	/**
	 * Convert an HTML node tree to a PHP script string.
	 *
	 * @param mixed $dv    HTML node to convert.
	 * @param mixed $owner Optional owner context; null at the root level.
	 * @return string PHP code string representing the node tree.
	 */
	private static function ConvertToScript($dv, $owner=null)
	{
		$out = "";
		if ($owner === null)
		{
			$out .= self::GetChild($dv);
		}
		else
		{

		if (($dv !== null) && is_object($dv))
		{
			//detect if has child property

			if ($dv->NodeType == XMLNodeType::TEXT)
			{
				//$out .= self::GetTextContent($dv);
			}
			else
			{
				$out .= self::GetChild($dv, $owner);
			}
		}
		else{
			igk_wln("dv = ".$dv);
		}

		}
		return $out;
	}
	/**
	 * Render the HTML-to-script conversion form.
	 *
	 * @return void
	 */
	public function doAction()
	{
		$frame = igk_html_frame($this, "tool.htmltoscript");

		//$frame = igk_add_new_frame($ctrl, $id, $closeuri, $target);
		$frame->Title = R::ngets("title.frameConvertHTMLToScript");
		$d = $frame->getBoxContent();
		$d->clearChilds();

		$frame->Form = $d->addForm();
		$frame->Form["action"] = $this->getUri("convert");
		$frame->Form->Div = $frame->Form->div();
		$frame->Form->Div->addTextArea("clHtmlCode", "code .... ");
		$frame->Form->Div->addInput("confirm", "hidden",1);
		$frame->Form->addHSep();
		$frame->Form->addInput("btn.submit", "submit", R::ngets("btn.convert"));

	}
} 