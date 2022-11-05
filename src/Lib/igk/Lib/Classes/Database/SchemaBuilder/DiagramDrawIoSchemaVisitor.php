<?php

namespace IGK\Database\SchemaBuilder;
 
use igk\db\schemaBuilder\BoxDimension;
use \igk\drawio\mxGraphModel;

igk_require_module(\igk\drawio::class);


class DiagramDrawIoSchemaVisitor extends  DiagramVisitor{
    private $node;
    private $m_offsetX;
    private $m_offsetY;
    public function start()
    {
        $this->node = new mxGraphModel();
        $_t = [];
        $_t[] = $this->node->mxCell();
        $_t[] = $this->node->mxCell()->setParent($_t[0]);
        $_t[] = $this->node->mxCell()
        ->setAttribute("vertex", 1)
        ->setStyle(["rounded"=>0, "whiteSpace"=>"wrap", "html"=>1])
        ->setParent($_t[1]);
        $this->parent_node = $_t[1];
    }
    public function complete()
    {
        return $this->node->render();
    }
    public function visitDiagramEntity($entity){
        $o = "";
        $n = $this->node->mxCell();
        $v = $entity->getName();
        $Width = 200;
        $ItemHeight = 30;
        $y = 30;
        $boxDim = new BoxDimension();
        $boxDim->value = 200;
        $n->setContent($v)
        ->setParent( $this->parent_node)
        ->setAttribute("style", 
            "swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=30;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=1;marginBottom=0;"
            // "swimlane;rounded=0;whiteSpace=wrap;html=1;"
        )
        ->setAttribute("vertex", "1")
        ->mxGeometry()->setAttributes([
            "x"=>$this->m_offsetX,
            "y"=>0,
            "width"=>$Width,
            "height"=>$boxDim,
            "as"=>"geometry", 
        ]);
        if($p = $entity->getProperties()){
            // $ul = $n->ul();
            foreach($p as $l){
                $item = $this->node->mxCell(); 
                $fs = "";
                if ($l->clIsPrimary){
                    $fs = "fontStyle=5;";
                }

                $item->setContent($l->clName);
                $item->setParent($n);
                $item->setAttribute("vertex", "1")
                ->setAttribute("style", 
                "text;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;spacingLeft=4;spacingRight=4;overflow=hidden;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;rotatable=0;".$fs);

                $item->mxGeometry()->setAttributes([                    
                    "y"=>$y,
                    "width"=>$Width,
                    "height"=>$ItemHeight,
                    "as"=>"geometry", 
                ]);        
                $y+= $ItemHeight;        
            }
            $boxDim->value = max($boxDim->value, $y);
        }   
        $this->m_offsetX += $Width;   
        return $o;
    }
}
