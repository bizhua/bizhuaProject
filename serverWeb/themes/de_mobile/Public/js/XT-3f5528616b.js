var XT=XT||{config:{yybUrl:"http://a.app.qq.com/o/simple.jsp?pkgname=com.shijia.baimei&android_scheme="},interface:{__chance:function(e,n){var t=window.location.hostname,a="",e=e||"api";switch(!0){case/test/g.test(t):a="https://"+e+"-test.chinaqljf.com";break;case/dev/g.test(t):a="https://"+e+"-dev.chinaqljf.com";break;case/alpha/g.test(t):a="https://"+e+"-alpha.chinaqljf.com";break;case/localhost/g.test(t):case/file/g.test(t):a="https://"+e+"-test.chinaqljf.com";break;default:a="http://"+e+".chinaqljf.com"}return a+n},api:function(e){return this.__chance("api",e)},fun:function(e){return this.__chance("fun",e)},pay:function(e){return this.__chance("pay",e)}},search:JSON.parse("{"+(location.search.match(/[\w\%]+=[^\&\=]+/g)||[]).map(function(e){return'"'+e+'"'}).join(",").replace(/=/g,'":"')+"}"),ua:{isApp:/bmlive/i.test(navigator.userAgent),isWeixin:/MicroMessenger/i.test(navigator.userAgent),isIos:/iphone|ipad/i.test(navigator.userAgent),isAndroid:/android/i.test(navigator.userAgent),isMobile:/iphone|ipad|android|mobile/i.test(navigator.userAgent),isUC:/UCWEB|UCBrowser/i.test(navigator.userAgent)},user:{default_avatar:"",uid:"",token:""},getSeqId:function(){return new Date(Date.now()+288e5).toISOString().replace(/\D/g,"").substr(0,14)+(""+1e7*(1+Math.random())).substr(1,6)},go:function(e,n){XT.ua.isApp?window.location.href=e||"bmlive://home?id=0":document.hidden||document.webkitHidden||(window.location.href=n||"http://www.dingsns.com/")},load:function(e){var n={css:e.css||"",js:e.js||""},t={css:e.cssVer||e.allVer||+new Date,js:e.jsVer||e.allVer||+new Date};for(var a in n)for(var i=0;i<n[a].length;i++)!function(e,n){if("js"==n){var t=document.createElement("script");t.setAttribute("type","text/javascript"),t.setAttribute("src",e)}else if("css"==n){var t=document.createElement("link");t.setAttribute("rel","stylesheet"),t.setAttribute("type","text/css"),t.setAttribute("href",e)}void 0!==t&&document.getElementsByTagName("head")[0].appendChild(t)}(n[a][i]+"?"+t[a],a)},throttle:function(e,n,t){var a,i,s,o=null,r=0;t||(t={});var c=function(){r=!1===t.leading?0:Date.now(),o=null,s=e.apply(a,i),o||(a=i=null)};return function(){var u=Date.now();r||!1!==t.leading||(r=u);var l=n-(u-r);return a=this,i=arguments,l<=0||l>n?(clearTimeout(o),o=null,r=u,s=e.apply(a,i),o||(a=i=null)):o||!1===t.trailing||(o=setTimeout(c,l)),s}},debounce:function(e,n,t){var a,i,s,o,r,c=function(){var u=Date.now()-o;u<n&&u>0?a=setTimeout(c,n-u):(a=null,t||(r=e.apply(s,i),a||(s=i=null)))};return function(){s=this,i=arguments,o=Date.now();var u=t&&!a;return a||(a=setTimeout(c,n)),u&&(r=e.apply(s,i),s=i=null),r}},jsonp:function(e){if(e.url){var n=e.url.split("/").slice(3).join("/");return jQuery.ajax({url:/http/g.test(e.url)?e.url:XT.interface.api(e.url),dataType:"jsonp",data:jQuery.extend({M:"H5"},e.data),jsonpCallback:e.jsonpCallback,success:function(t){"function"==typeof e.super_success?e.super_success(t):"ok"==t.code?(console.log(n,t),"function"==typeof e.success&&e.success(t.data)):(console.log("["+t.code+"]"+t.message,n,t),"function"==typeof e.validMsg&&e.validMsg(t.message))},fail:function(t){"function"==typeof e.fail?e.fail(t):console.log("网络错误",n)}})}},post:function(e){if(e.url){var n=e.url.split("/").slice(3).join("/"),t=jQuery.extend({M:"H5"},e.data);jQuery.ajax({url:/http/g.test(e.url)?e.url:XT.interface.api(e.url),type:"post",data:JSON.stringify(t),contentType:"application/json",success:function(t){"function"==typeof e.super_success?e.super_success(t):"ok"==t.code?(console.log(n,t),"function"==typeof e.success&&e.success(t.data)):(console.log("["+t.code+"]"+t.message,n,t),"function"==typeof e.validMsg&&e.validMsg(t.message))},fail:function(t){"function"==typeof e.fail?e.fail(t):console.log("网络错误",n)}})}},sign:function(e,n,t){n=$.extend({M:"H5"},n),XT.bridge.call("getApiSign",{method:e,parameters:n},function(e){var n=JSON.parse(e);t&&t(n)})}};XT.share=function(){var e={title:document.title,desc:document.title,imgUrl:"http://h5.dingsns.com/base/images/icon-app.png",url:location.origin+location.pathname+(location.search||"?H5"),link:location.origin+location.pathname+(location.search||"?H5"),type:"",dataUrl:"",success:function(){},cancel:function(){}},n=$('<div id="pop-weixin-share" class="g-wx-share-mask"></div>');$(function(){n.on("click",function(){$(this).removeClass("on")}).appendTo("body")});var t={wxConfig:{debug:!1,jsApiList:["onMenuShareAppMessage","onMenuShareTimeline","onMenuShareWeibo","onMenuShareQQ","chooseWXPay"]},wxIsReady:!1,wx:window.wx,set:function(n){e=$.extend(e,n),a(),console.log("[share]",e)},pop:function(e){switch(e&&this.set(e),!0){case XT.ua.isWeixin:n.addClass("on");break;default:console.log("触发分享")}}},a=function(){window.wx&&t.wxIsReady&&(window.wx.onMenuShareAppMessage(e),window.wx.onMenuShareTimeline(e),window.wx.onMenuShareWeibo(e),window.wx.onMenuShareQQ(e))};return window.wx&&($.ajax({url:XT.interface.api("/share/config/wx-web-auth?url="+encodeURIComponent(location.href.replace(/#.*$/,""))),dataType:"jsonp",success:function(e){"ok"==e.code&&(XT.share.wxConfig=$.extend(e.data,XT.share.wxConfig),XT.share.wxConfig.timestamp=e.data.timeStamp,window.wx.config(XT.share.wxConfig))}}),window.wx.ready(function(){t.wxIsReady=!0,a()})),t}(),XT.bridge=function(){var e={isReady:!1,queue:{call:[],on:[]},on:function(n,t){return n?("function"!=typeof t&&(t=function(){}),e.origin?e.origin.registerHandler(n,t):e.queue.on.push({event:n,callback:t}),this):this},call:function(n,t,a){return n?(e.origin?e.origin.callHandler(n,t,a):e.queue.call.push({event:n,data:t,callback:a}),this):this},debug:function(e){return XT.ua.isApp||(XT.user.uid=e.uid,XT.user.token=e.token),this},login:function(n){return e.call("login",{},n),this}},n=function(e){e=e?JSON.parse(e):{},XT.user=$.extend(XT.user,e)};return e.ready=function(t,a){if(!e.isReady){e.isReady=!0,e.origin=t;try{e.origin.init(function(e,n){})}catch(e){}setTimeout(function(){e.call("getUserInfo",{},function(e){n(e)}),XT.search.extCode?e.call("getUserInfo",{},function(e){var n=JSON.parse(e||{});XT.jsonp({url:"/share/share-getByExtCode?extCode="+XT.search.extCode+"&shareUserId="+n.id,jsonpCallback:"_nativeShare",success:function(e){XT.bridge.call("setShareInfo",{id:String(e.id),type:XT.search.extShareType||"page"},function(e){var n=JSON.parse(e);console.log("setShareInfo result:"+n)})}})}):XT.search.shareType&&XT.search.shareId&&e.call("setShareInfo",{type:XT.search.shareType,id:XT.search.shareId},function(e){JSON.parse(e)&&$(".u-page-share-btn").remove()})},0),e.queue.on.forEach(function(n){try{e.origin.registerHandler(n.event,n.callback)}catch(e){}}),e.queue.call.forEach(function(n){try{e.origin.callHandler(n.event,n.data,n.callback)}catch(e){}})}},function(e){if(window.WebViewJavascriptBridge)return e(WebViewJavascriptBridge);if(document.addEventListener("WebViewJavascriptBridgeReady",function(){e(WebViewJavascriptBridge)},!1),window.WVJBCallbacks)return window.WVJBCallbacks.push(e);window.WVJBCallbacks=[e];var n=document.createElement("iframe");n.style.display="none",n.src="wvjbscheme://__BRIDGE_LOADED__",document.documentElement.appendChild(n),setTimeout(function(){document.documentElement.removeChild(n)},0)}(e.ready),e}(),XT.setShare=function(){var e="";XT.ua.isApp||(e='<aside class="g-download-guide"><a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.shijia.baimei&android_scheme=bmlive%3A%2F%2Fbmlive%2Fhome%3Fid%3D0" class="u-download-btn">立即下载</a><figure class="m-app-info"><img src="../base/images/icon-app.png" alt="" class="logo"><figcaption class="info"><h1 class="name">百媚直播</h1><p class="intro">聚星闪耀 与众不瞳</p></figcaption></figure></aside>')},XT.getImgUrl=function(e,n,t){if(!n&&!t)return e;if(!/dingsns/g.test(e))return e;var a=e;a.substring(a.lastIndexOf(".")+1);return/cdn/g.test(a)?(n&&(a+="@"+n+"w"),t&&(a+=n?"_"+t+"h":"@"+t+"h")):(a+="?x-oss-process=image/resize",n&&(a+=",w_"+n),t&&(a+=",h_"+t)),a},$(function(){XT.search.userId&&(XT.user.id=XT.search.userId),window.template&&template.helper("replaceImg",function(e,n,t){return XT.getImgUrl(e,n,t)}),XT.search.extCode&&XT.setShare()});