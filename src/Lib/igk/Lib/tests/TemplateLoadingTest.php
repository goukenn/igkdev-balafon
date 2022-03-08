<?php
namespace IGK\Tests;

use IGK\System\Html\HtmlContext;
use IGK\Tests\BaseTestCase;

class TemplateLoadingTest extends BaseTestCase{
    public function test_visibile_attribute(){
        $src = '<a *visible="false">item ok</a>';
        $n = igk_create_node("div");
        $n->load($src);
        $this->assertEquals(
            "<div></div>",
            $n->render(),
            "not visible not handle"
        );

        //
        $src = '<a *visible="true">item ok</a>';
        $n = igk_create_node("div");
        $n->load($src);
        $this->assertEquals(
            "<div><a>item ok</a></div>",
            $n->render(),
            "visible not handle"
        ); 
        $src = '<a>{{ $raw | json }}</a>';
        $n = igk_create_node("div");
        $n->load($src,(object)[
            "Context"=>HtmlContext::XML,
            "raw"=>[
                "data"=>"ok", 
            ],
            "ctrl"=>\IGK\Tests\Controllers\TestController::ctrl()
        ]);
        
        $this->assertEquals(
            "<div><a>{{ \$raw | json }}</a></div>",
            $n->render(),
            "load do not escape inner context failed"
        );
        $n->clearChilds();

        $n->load($src,(object)[
            "Context"=>HtmlContext::Html,
            "raw"=>[
                "data"=>"ok", 
            ],
            "ctrl"=>\IGK\Tests\Controllers\TestController::ctrl()
        ]);
        
        $this->assertEquals(
            '<div><a>{"raw":{"data":"ok"},"ctrl":{}}</a></div>',
            $n->render(),
            "load inner raw load"
        );

        // igk_wln(__FILE__.":".__LINE__,  "bindig properties");
        // $src = '{{ $raw }} <a *visible="$raw->visible">raw ok = {{ $raw }}</a>';
        $src = '<a *visible="$raw->visible">raw ok</a>';
        $n = igk_create_node("div");
        $n->load($src,(object)[
            "Context"=>HtmlContext::Html,
            "raw"=>(object)[
                "data"=>"ok",
                "visible"=>true
            ],
            "ctrl"=>\IGK\Tests\Controllers\TestController::ctrl()
        ]);
        
        $this->assertEquals(
            "<div><a>raw ok</a></div>",
            $n->render(),
            "last properties"
        );
    }

    public function test_title_properties(){
        $src = '<a *title="$raw->title" >info</a>';
        $n = igk_create_node("div");
        $n->load($src,(object)[
            "Context"=>HtmlContext::Html,
            "raw"=>(object)[
                "title"=>"presentation"
            ],
            "ctrl"=>\IGK\Tests\Controllers\TestController::ctrl()
        ]);
        $this->assertEquals(
            "<div><a title=\"presentation\">info</a></div>",
            $n->render(),
            "title evaluation"
        );
    }
}