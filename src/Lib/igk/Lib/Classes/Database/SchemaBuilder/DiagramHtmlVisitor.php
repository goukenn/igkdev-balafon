<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DiagramHtmlVisitor.php
// @date: 20220531 13:35:47
// @desc: 

namespace IGK\Database\SchemaBuilder;



class DiagramHtmlVisitor extends DiagramVisitor{
   
    public function visitDiagramEntity($entity){
        $o = "";
        $n = igk_create_node("div");
        $n->h2()->Content = $entity->getName();
        if($p = $entity->getProperties()){
            $ul = $n->ul();
            foreach($p as $l){
                $ul->li()->Content = $l->clName;
            }
        }
        $o = $n->render();       
        return $o;
    }
}


