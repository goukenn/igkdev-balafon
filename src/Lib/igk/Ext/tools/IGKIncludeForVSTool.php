<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKIncludeForVSTool.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\Controllers\ToolControllerBase;

class IGKIncludeForVSTool extends ToolControllerBase
{

	/**
	 * Return the URI of the tool image icon.
	 *
	 * @return string URI of the include-for-VS tool image.
	 */
	public function getImageUri(){
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_incforvs.png"));
		return $uri;
	}
	/**
	 * Recursively load files from a directory into an XML node.
	 *
	 * @param mixed  $node    Target XML node to populate.
	 * @param string $pattern Current relative path pattern used for link labels.
	 * @param string $dir     Absolute directory path to scan.
	 * @return void
	 */
	private function LoadFile($node , $pattern,  $dir)
	{
		$hdir = opendir($dir);

		if (is_resource($hdir))
		{
			while($s = readdir($hdir))
			{
				if (($s==".") || ($s== ".."))
					continue;
				$dname = $dir.DIRECTORY_SEPARATOR.$s;
				if (is_dir($dname))
				{
					$this->LoadFile($node, $pattern."\\".$s, $dname);
				}
				else if (igk_io_file_exists($dname))
				{
					$node->add("None", array("Include"=>realpath($dname)))->add("Link")->Content = $pattern."\\".$s;
				}
			}
			closedir($hdir);
		}
	}
	/**
	 * Build a Visual Studio include XML file and send it as a download.
	 *
	 * @return void
	 */
	public function doAction()
	{
		$out = "";
		$dir = igk_io_currentRelativePath("Lib");
		$f =  HtmlNode::CreateWebNode("ItemGroup");
		//get all files
		$this->LoadFile($f, "Lib", $dir);
		$out = $f->render();
		igk_download_content("includeforvs.xml", strlen($out) , $out);

	}
} 