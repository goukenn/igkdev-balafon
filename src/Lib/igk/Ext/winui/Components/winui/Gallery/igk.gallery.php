<?php

function igk_html_node_Gallery(){
$n = igk_create_node("div");
$n["class"]="igk-winui-gallery";
$n->setCallback("addPicture", "igk_gallery_add");
return $n;
}

function igk_gallery_add($gallery, $src, $alt=null){
	$i = $gallery->div()->setClass("bx");
	$h = $i->addXmlNode("img");
	$h["src"]=$src;
	$h["alt"]=$alt;
	return $i;
} 