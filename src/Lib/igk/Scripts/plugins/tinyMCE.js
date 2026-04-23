  // tiny mce plugins 
  (function() {
      var m_loadtiny = 0;
      var b_reg = 0;
      var m_globals = [];
      var __u = igk.constants.undef;
      // var mut = new MutationObserver(function(e){
      // });
      // igk.ready(function(){
      // mut.observe(igk.dom.body().o, {childList:true});
      // });
      igk.system.createNS("igk.tinyMCE", {
          runOn: function(el, op) {
              igk.ready(function() {
                  if (typeof(tinyMCE) != __u) {
                      // because of identification. 
                      // remove all editor that's not present on document
                      for (var i in tinyMCE.editors) {
                          if (i == 'length') continue;
                          var m = tinyMCE.editors[i];
                          if (!$igk(m.getElement()).isOnDocument()) {
                              tinyMCE.remove(m);
                          }
                      }
                      setTimeout(function() {
                          var g = { selector: el.elements };
                          var m = null;
                          for (var i in op) {
                              if (i == "protect") {
                                  m = Array();
                                  for (var j = 0; j < op[i].length; j++) {
                                      m.push(new RegExp(op[i][j], "g"));
                                  }
                                  g[i] = m;
                                  continue;
                              }
                              g[i] = op[i];
                          }
                          tinyMCE.init(g);
                      }, 100);
                      return;
                  }
              });
          },
          init: function(elements, op) {
              // var prop={
              // // General options
              // mode : mode,
              // theme : "advanced",
              // // instanciate plugin list
              // plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
              // // Theme options
              // theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
              // theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
              // theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
              // theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
              // theme_advanced_toolbar_location : "top",
              // theme_advanced_toolbar_align : "left",
              // theme_advanced_statusbar_location : "bottom",
              // theme_advanced_resizing : true,
              // entity_encoding : "raw",
              // // 'forced_root_block' : 'div',
              // // Skin options
              // skin : "o2k7",
              // skin_variant : "silver",
              // // Example content CSS(should be your site CSS)
              // content_css : "css/example.css",
              // // Drop lists for link/image/media/template dialogs
              // // template_external_list_url : "js/template_list.js",
              // // external_link_list_url : "js/link_list.js",
              // // external_image_list_url : "js/image_list.js",
              // // media_external_list_url : "js/media_list.js",
              // // Replace values for the template plugin
              // template_replace_values : {
              // username : "Some User",
              // staffid : "991234"
              // },
              // style_formats:[
              // {title:"myFormat",inline:"div",classes:"thediv"}
              // ]
              function __loadAndInit() {
                  var tq = {};
                  if (op) {
                      for (var i in op) {
                          tq[i] = op[i];
                      };
                  }
                  var t = elements.elements;
                  if (typeof(t) == "string") {
                      tq.selector = t;
                      tinyMCE.init(tq);
                      return;
                  } else {
                      for (var i in t) {
                          var q = $igk(elements[i]).first();
                          tq.selector = elements;
                          tinyMCE.init(tq);
                      }
                  }
              }
              if (typeof(tinyMCE) != igk.constants.undef) {
                  __loadAndInit();
              } else {
                  igk.ready(function() {
                      __loadAndInit();
                  });
              }
              function __bindGlobal(t) {
                  // call on every ajx context finish loaded		
                  for (var i = 0; i < m_globals.length; i++) {
                      m_globals[i].apply();
                  }
                  m_globals = [];
              }
              if (!b_reg) {
                  b_reg = 1;
              } else {
                  m_globals.push(function() { __loadAndInit() });
              }
          },
          edit: function(selector, op) {
              function __loadAndInit() {
                  var q = { selector: selector, inline: true };
                  for (var i in op) {
                      q[i] = op[i];
                  }
                  tinyMCE.init(q);
              }
              if (typeof(tinyMCE) != igk.constants.undef) {
                  __loadAndInit();
              } else {
                  igk.ready(__loadAndInit);
              }
          },
          save: function(n, uri, callback) {
              // selector
              var g = tinyMCE.get(n);
              if (g) {
                  // g.setProgressState(1);
                  igk.ajx.post(uri, 'tiny=1&clContent=' + g.getContent(), function(xhr) {
                      if (this.isReady()) {
                          // g.setProgressState(0);
                          if (callback)
                              callback.apply(this);
                          igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                      }
                  });
              }
          }
      });
  })();