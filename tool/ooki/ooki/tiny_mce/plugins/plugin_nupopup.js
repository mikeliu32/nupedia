//

function NUPopup_Engine() {
	var o;
	if ( typeof(window.dialogArguments) == "undefine" ) o = window.dialogArguments;
	if ( !o && typeof(window.opener.dialogArguments) != "undefine" ) o = window.opener.dialogArguments;
	this.loadLang(o);
	window.attachEvent("onload", this.replaceLabel);
};

NUPopup_Engine.prototype = {
	findWin : function(w) {
		var c;
	
		// Check parents
		c = w;
		while (c && (c = c.parent) != null) {
			if (typeof(c.tinyMCE) != "undefined")
				return c;
		}

		// Check openers
		c = w;
		while (c && (c = c.opener) != null) {
			if (typeof(c.tinyMCE) != "undefined")
				return c;
		}

		// Try top
		if (typeof(top.tinyMCE) != "undefined")
			return top;

		return null;
	},
	
	init : function() {
		var win = window.opener ? window.opener : window.dialogArguments, c;
		var inst, re, title, divElm;

		if (!win)
			win = this.findWin(window);

		if (!win) {
			alert("tinyMCE object reference not found from popup.");
			return;
		}

		window.$ = win.$;
		this.windowOpener = win;
		this.editor = win.tinyMCE.selectedInstance;
		this.addEvent = win.tinyMCE.addEvent;
	
	},
	   
	addToLang : function(prefix, ar) {
		for (var key in ar) {
			if (typeof(ar[key]) == 'function')
				continue;
			tinyMCELang[(key.indexOf('lang_') == -1 ? 'lang_' : '') + (prefix != '' ? (prefix + "_") : '') + key] = ar[key];
		}
	},
	
	loadLang : function(ar) {
		var u = ar.BaseUrl + '/langs/' + ar.language + '.js';
		document.write('<script language="javascript" type="text/javascript" src="' + u + '"></script>');
	},
	
	replaceLabel : function(ar) {
		var ss = document.body.innerHTML;
		for (var key in tinyMCELang) {
			if (typeof(tinyMCELang[key]) == 'function')
				continue;
			ss = ss.replace(new RegExp('{\\$' + key + '}', 'g'), tinyMCELang[key]);
		}
		document.body.innerHTML = ss;
	}
};

var tinyMCELang = {};
var tinyMCE = new NUPopup_Engine();
tinyMCE.init();

