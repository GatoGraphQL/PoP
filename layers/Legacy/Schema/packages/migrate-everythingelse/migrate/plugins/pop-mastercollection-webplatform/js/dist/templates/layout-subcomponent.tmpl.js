!function(){var e=Handlebars.template;(Handlebars.templates=Handlebars.templates||{})["layout-subcomponent"]=e({1:function(e,n,l,t,a,u,r){var s;return null!=(s=(l.withget||n&&n.withget||l.helperMissing).call(null!=n?n:e.nullContext||{},n,null!=r[1]?r[1]["subcomponent-field"]:r[1],{name:"withget",hash:{},fn:e.program(2,a,0,u,r),inverse:e.noop,data:a}))?s:""},2:function(e,n,l,t,a,u,r){var s;return null!=(s=l.each.call(null!=n?n:e.nullContext||{},null!=(s=null!=r[2]?r[2]["module-names"]:r[2])?s.layouts:s,{name:"each",hash:{},fn:e.program(3,a,0,u,r),inverse:e.noop,data:a}))?s:""},3:function(e,n,l,t,a,u,r){var s;return null!=(s=(l.withModule||n&&n.withModule||l.helperMissing).call(null!=n?n:e.nullContext||{},r[3],n,{name:"withModule",hash:{},fn:e.program(4,a,0,u,r),inverse:e.noop,data:a}))?s:""},4:function(e,n,l,t,a,u,r){var s;return null!=(s=l.if.call(null!=n?n:e.nullContext||{},null!=r[4]?r[4].individual:r[4],{name:"if",hash:{},fn:e.program(5,a,0,u,r),inverse:e.program(9,a,0,u,r),data:a}))?s:""},5:function(e,n,l,t,a,u,r){var s,o=null!=n?n:e.nullContext||{};return null!=(s=l.each.call(o,(l.maybe_make_array||n&&n.maybe_make_array||l.helperMissing).call(o,r[2],{name:"maybe_make_array",hash:{},data:a}),{name:"each",hash:{},fn:e.program(6,a,0,u,r),inverse:e.noop,data:a}))?s:""},6:function(e,n,l,t,a,u,r){var s,o=e.lambda,i=e.escapeExpression,h=null!=n?n:e.nullContext||{};return"\t\t\t\t\t\t<"+i(o(null!=r[5]?r[5]["html-tag"]:r[5],n))+' class="'+i(o(null!=r[5]?r[5].class:r[5],n))+'" style="'+i(o(null!=r[5]?r[5].style:r[5],n))+'"  '+(null!=(s=l.each.call(h,null!=r[5]?r[5]["previousmodules-ids"]:r[5],{name:"each",hash:{},fn:e.program(7,a,0,u,r),inverse:e.noop,data:a}))?s:"")+">\n\t\t\t\t\t\t\t"+i((l.enterModule||n&&n.enterModule||l.helperMissing).call(h,r[5],{name:"enterModule",hash:{context:r[1],objectID:n,subcomponent:null!=r[5]?r[5]["subcomponent-field"]:r[5]},data:a}))+"\n\t\t\t\t\t\t</"+i(o(null!=r[5]?r[5]["html-tag"]:r[5],n))+">\n"},7:function(e,n,l,t,a,u,r){var s,o=null!=n?n:e.nullContext||{},i=l.helperMissing,h=e.escapeExpression;return" "+h((s=null!=(s=l.key||a&&a.key)?s:i,"function"==typeof s?s.call(o,{name:"key",hash:{},data:a}):s))+'="#'+h((l.lastGeneratedId||n&&n.lastGeneratedId||i).call(o,{name:"lastGeneratedId",hash:{module:n,context:r[6]},data:a}))+'"'},9:function(e,n,l,t,a,u,r){var s,o=e.lambda,i=e.escapeExpression,h=null!=n?n:e.nullContext||{};return"\t\t\t\t\t<"+i(o(null!=r[4]?r[4]["html-tag"]:r[4],n))+' class="'+i(o(null!=r[4]?r[4].class:r[4],n))+'" style="'+i(o(null!=r[4]?r[4].style:r[4],n))+'" '+(null!=(s=l.each.call(h,null!=r[4]?r[4]["previousmodules-ids"]:r[4],{name:"each",hash:{},fn:e.program(10,a,0,u,r),inverse:e.noop,data:a}))?s:"")+">\n\t\t\t\t\t\t"+i((l.enterModule||n&&n.enterModule||l.helperMissing).call(h,r[4],{name:"enterModule",hash:{dbObjectIDs:r[2],subcomponent:null!=r[4]?r[4]["subcomponent-field"]:r[4]},data:a}))+"\n\t\t\t\t\t</"+i(o(null!=r[4]?r[4]["html-tag"]:r[4],n))+">\n"},10:function(e,n,l,t,a,u,r){var s,o=null!=n?n:e.nullContext||{},i=l.helperMissing,h=e.escapeExpression;return" "+h((s=null!=(s=l.key||a&&a.key)?s:i,"function"==typeof s?s.call(o,{name:"key",hash:{},data:a}):s))+'="#'+h((l.lastGeneratedId||n&&n.lastGeneratedId||i).call(o,{name:"lastGeneratedId",hash:{module:n,context:r[5]},data:a}))+'"'},compiler:[7,">= 4.0.0"],main:function(e,n,l,t,a,u,r){var s;return null!=(s=l.with.call(null!=n?n:e.nullContext||{},null!=n?n.dbObject:n,{name:"with",hash:{},fn:e.program(1,a,0,u,r),inverse:e.noop,data:a}))?s:""},useData:!0,useDepths:!0})}();