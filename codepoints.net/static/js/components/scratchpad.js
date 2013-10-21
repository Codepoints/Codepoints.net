define(["jquery","components/gettext","polyfills/fromcodepoint","components/unicodetools","zeroclipboard"],function(e,t,n,r,i){function c(){o.length>=f&&(o=o.slice(f-o.length+1)),o.push(parseInt(e(".payload").data("cp"),10)),u.update()}function h(){o=[],u.update()}function p(){l.setText("Copy me!")}function d(){var e=o.map(function(e){return"U+"+r.formatCodepoint(e)});window.location.href="/"+e.join(",")}i.setDefaults({moviePath:"/static/ZeroClipboard.swf"});var s=t.gettext,o=[],u=e('<div class="scratchpad__container"></div>'),a=e('<div class="scratchpad__controls"></div>').appendTo(u),f=128,l=new i;return l.on("complete",function(e,t){alert("Copied text to clipboard: "+t.text)}),{init:function(){if("localStorage"in window){var t=window.localStorage;o=JSON.parse(t.getItem("scratchpad")),e.isArray(o)||(o=[]);var i=e('<button type="button" class="scratchpad__empty">'+s("empty")+"</button>").on("click",h).appendTo(a),f=e('<button type="button" class="scratchpad__copy">'+s("copy")+"</button>").on("click",p).appendTo(a),l=e('<button type="button" class="scratchpad__show">'+s("show")+"</button>").on("click",d).appendTo(a);u.update=function(){this.find(".data, .quiet").remove();if(o.length){var t=this.prepend('<ul class="data"></ul>').find(".data");e.each(o,function(e,i){t.append('<li><a class="cp" href="/U+'+r.formatCodepoint(i)+'">'+r.formatCodepoint(i)+'<span class="img">'+n(i)+"</span></a></li>")}),a.show()}else this.prepend('<p class="quiet">'+s("You have no codepoints here yet. Add one by clicking “Add to scratchpad” on the details page.")+"</p>"),a.hide();return this}.bind(u);var v=e('<li class="scratchpad"><a href="#">Scratchpad</a></li>').on("click",function(){return u.update().dialog({title:s("Scratchpad")}),!1}).hide();e(".hd .primary").append(v),v.show("normal");var m=e(".codepoint--tools");if(m.length){var g=e('<p><button type="button" class="button button--hi"><i class="icon-edit"></i> '+s("Add to scratchpad")+"</button></p>").on("click",c);m.append(g)}e(window).on("unload",function(){t.setItem("scratchpad",JSON.stringify(o))})}}}});