var Z = Z || {};
Z.version = '1.0';
Z.trim = function(str){
	return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}
Z.id = function(_){return document.getElementById(_);};
Z.Template = {
    parse: function (json, tpl, splitStr) {
        var blocks = [];
        for (var i in json) {
        	var el = json[i];
            blocks.push(Z.trim(tpl).replace(/{(\w+)}/g, function (a, b) {
                return el[b] !== undefined ? el[b] : a;
            }));
        }
        return blocks.join(splitStr || '');
    },
    stuff: function (json, target, tpl, splitStr) {
        tpl = document.getElementById(tpl || target + '_tpl').value;
        target = document.getElementById(target);
        var m = tpl.match(/<#([\s\S]+)#>/);
        var blockTpl = m[1];
        target.innerHTML = tpl.replace(m[0], this.parse(json, blockTpl, splitStr));
    }
};
Z.domready = function (callback) {
    /* Internet Explorer */
    /*@cc_on
    @if (@_win32 || @_win64)
        document.write('<script id="ieScriptLoad" defer src="//:"><\/script>');
        document.getElementById('ieScriptLoad').onreadystatechange = function() {
            if (this.readyState == 'complete') {
                callback();
            }
        };
        return;
    @end @*/
    /* Mozilla, Chrome, Opera */
    if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', callback, false);
    }
    /* Safari, iCab, Konqueror */
    else if (/KHTML|WebKit|iCab/i.test(navigator.userAgent)) {
        var DOMLoadTimer = setInterval(function () {
            if (/loaded|complete/i.test(document.readyState)) {
                callback();
                clearInterval(DOMLoadTimer);
            }
        }, 10);
    }
    /* Other web browsers */
    else {
    	window.onload = callback;
    }
};
Z.loadScript = function (url, callback) { 
  var script = document.createElement('script');
  script.charset = 'utf-8';
  script.type = 'text/javascript';
  if (callback) 
    script.onload = script.onreadystatechange = function() { 
    if (script.readyState && script.readyState != 'loaded' && script.readyState != 'complete') 
    return; 
    script.onreadystatechange = script.onload = null; 
    callback(); 
  }; 
  script.src = url; 
  document.getElementsByTagName('head')[0].appendChild(script); 
}
Z.getQuery = function (key) {
}

/**
* get parameters from url
* @method getQuery
* @param {String} key
* @param {String} url
* @return {String | Array}
*/
 
Z.getQuery = function( key , url )
{
    url = url || window.location.href;
    var rts = [],rt;
    queryReg = new RegExp( '(^|\\?|&)' + key + '=([^&]*)(?=&|#|$)' , 'g' );
    while ( ( rt = queryReg.exec( url ) ) != null )
    {
        rts.push( decodeURIComponent( rt[ 2 ] ) );
    }
    if ( rts.length == 0 ) return null;
    if ( rts.length == 1 ) return rts[ 0 ];
    return rts;
}