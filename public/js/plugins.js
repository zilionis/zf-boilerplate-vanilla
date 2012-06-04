
// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
    arguments.callee = arguments.callee.caller;
    var newarr = [].slice.call(arguments);
    (typeof console.log === 'object' ? log.apply.call(console.log, console, newarr) : console.log.apply(console, newarr));
  }
};

// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());


// place any jQuery/helper plugins in here, instead of separate, slower script files.

/*
 * jQuery replaceText - v1.1 - 11/21/2009
 * http://benalman.com/projects/jquery-replacetext-plugin/
 * 
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($){$.fn.replaceText=function(b,a,c){return this.each(function(){var f=this.firstChild,g,e,d=[];if(f){do{if(f.nodeType===3){g=f.nodeValue;e=g.replace(b,a);if(e!==g){if(!c&&/</.test(e)){$(f).before(e);d.push(f)}else{f.nodeValue=e}}}}while(f=f.nextSibling)}d.length&&$(d).remove()})}})(jQuery);


/**
 * jQuery Mustache Plugin v0.2.2
 * 
 * @author Jonny Reeves (http://jonnyreeves.co.uk/)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the 
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to  
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the 
 * Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS  
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
(function(a,b){function f(){if(d===null){d=b.Mustache;if(typeof d==="undefined"){a.error("Failed to locate Mustache instance, are you sure it has been loaded?")}}return d}function g(a){return typeof c[a]!=="undefined"}function h(b,d){if(!e.allowOverwrite&&g(b)){a.error("TemplateName: "+b+" is already mapped.");return}c[b]=d}function i(){var b;if(arguments.length===0){b=a("body script").filter('[type="'+e.domTemplateType+'"]').map(function(){return this.id})}else{b=a.makeArray(arguments)}a.each(b,function(){var b=a("#"+this).html();if(b===void 0){a.error("No such elementId: #"+this)}else{h(this,b.trim())}})}function j(a){var b=c[a];delete c[a];return b}function k(){c={};f().clearCache()}function l(b,d){if(!g(b)){if(e.warnOnMissingTemplates){a.error("No template registered for: "+b)}return""}return f().to_html(c[b],d,c)}function m(b,c){return a.Deferred(function(d){a.get(b).done(function(b){a(b).filter("script").each(function(b,c){h(c.id,a(c).html().trim())});if(a.isFunction(c)){c()}d.resolve()}).fail(d.reject)}).promise()}function n(){return a.map(c,function(a,b){return b})}"use strict";var c={},d=null,e={warnOnMissingTemplates:false,allowOverwrite:true,domTemplateType:"text/html"};a.Mustache={options:e,load:m,add:h,addFromDom:i,remove:j,clear:k,render:l,templates:n,instance:d};a.fn.mustache=function(b,c,d){var e=a.extend({method:"append"},d);var f=function(c,d){a(c)[e.method](l(b,d))};return this.each(function(){var b=this;if(a.isArray(c)){a.each(c,function(){f(b,this)})}else{f(b,c)}})}})(jQuery,window)