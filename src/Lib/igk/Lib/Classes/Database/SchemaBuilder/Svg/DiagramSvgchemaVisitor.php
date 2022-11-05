<?php

// @author: C.A.D. BONDJE DOUE
// @filename: d.php
// @date: 20220531 13:34:45
// @desc: 

namespace IGK\Database\SchemaBuilder\SVG;
 
use IGK\Database\SchemaBuilder\DiagramEntityColumnInfo;
use IGK\Database\SchemaBuilder\DiagramVisitor;
use IGK\Database\SchemaBuilder\Svg\Html\BoxDimension;
use IGK\System\Html\XML\XmlNode;

/**
 * diagram svg schema visitor
 * @package igk\db\schemaBuilder
 */
class DiagramSvgchemaVisitor extends DiagramVisitor{
    private $visitor_items = [];
    private $defs = null;
    var $width;
    var $height;
    const DEFAULT_WIDTH  = 500;
    const DEFAULT_HEIGHT = 500;
    
    public function start(){
        $this->defs = new XmlNode("defs");
        $this->visitor_items = [];
        return igk_str_format('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
        "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox='0 0 {0} {1}' width='{0}' height='{1}' >", 
            $this->width ?? self::DEFAULT_WIDTH, $this->height ?? self::DEFAULT_HEIGHT);
    }
    public function complete(){
        return $this->defs->render().
        implode("", array_map(function($n){
            return $n->render();
        }, $this->visitor_items))
        .        
        "</svg>";
    }
    public function visitDiagramEntity($entity){
        $o = "";       
        $n = new XmlNode("g");
        $key = $n["clName"] = $entity->getName();
        $this->visitor_items[$key] = $n;
        $n["clDescription"] = $entity->getDescription();
        $x = 10;
        $y = 10;
        $width = 150;
        $height = 10;
        $lineHeight = 30;
        $count = 1;
        $HeightDim = new BoxDimension(); 
        $HeightDim->value = $height ;

      
        $_clipid = "rect_".$count;
        $count++;
        $posxDim = new BoxDimension();
        $posxDim->value = $x;
        $this->defs->add("clipPath")->setAttributes(["id"=>$_clipid])
        ->add("rect")
        ->setAttributes(
            ["x"=>"10",//$posxDim,
            "y"=>"10",
            "width"=>$width,
            "height"=>$HeightDim,
            "rx"=>10,
            "ry"=>10,
            ]
        );
        $n["clip-path"] = sprintf("url(#%s)", $_clipid);
          
        
        if($p = $entity->getProperties()){
            $n->add("rect")
            ->setAttributes(
                ["x"=>"10", 
                "y"=>"10",
                "width"=>$width,
                "height"=>$HeightDim,
                "rx"=>10,
                "ry"=>10,
                "fill"=>"none",
                "stroke"=>"black",
                "stroke-width"=>"4"
                ]
            );

            foreach($p as $l){
                $ul = $n->add("rect");
                $ul->setAttributes([
                    "x"=>$posxDim,
                    "y"=>$y,
                    "width"=>$width,
                    "height"=>$lineHeight,                   
                    "fill"=>"none",
                    "stroke"=>"none",
                    "stroke-width"=>"4"
                ]);
                $txt = $n->add("text");
                $r = (array)$l;
                if (!DiagramEntityColumnInfo::SupportTypeLength($r["clType"])){
                    unset($r["clTypeLength"]);
                }
                $txt->Content = $r["clName"];
                $txt->setAttributes([
                    "x"=>"20",
                    "y"=>$y+20,  
                    "fill"=>"black",
                    "stroke"=>"none", 
                ]);
                $y+= $lineHeight;
                $HeightDim->value = $y-10; 
            }
        }
        //$o .= $this->defs->render();
        
        return $o;
    }
}

