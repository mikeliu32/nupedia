
var YouTubeDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		
		//
		var data = tinyMCEPopup.getWindowArg("plugin_data");
		if (data) $("#txtC").val( decodeURIComponent(data) );
	},

	_parseData : function() {
		var arow, acol, out;
		
		out = {};
		arow = data_web.split("\n");
		for (row=0; row<arow.length; row++) {
			if ( arow[row] == "" ) continue;
			acol = arow[row].split("\t");
			if ( acol.length < 3 ) continue;
			
			if ( !out[acol[0]] ) out[acol[0]] = {};
			out[acol[0]][acol[1]] = acol[2];
		}
		return out;
	},
	
	_getAttr : function(s, n) {
		var ed = tinyMCEPopup.editor;
		n = new RegExp(n + '=\"([^\"]+)\"', 'ig').exec(s);
		return n ? ed.dom.decode(n[1]) : '';
	},
	
	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		var h;
		var bObject = false;
		
		h = $.trim( $(document.body).find('#txtC').val() ); // class="ookiYouTube"
		h = h.replace(/[\r\n]+>/ig, "");
		// Object
		h = h.replace(/<object([^>]*)>/i, function(a, b) {
			bObject = true;
			var w, h;
			if ( (w = t._getAttr(b, 'width')) < 100 ) w = 100;
			if ( (h = t._getAttr(b, 'height')) < 100 ) h = 100;
			return '<object class="ookiYouTube" width="' + w + '" height="' + h + '">';
		});
		// iframe
		if (!bObject) {
			h = h.replace(/<iframe\s+[^>]*>.*?<\/iframe>/ig, function(a) {
				if (!/class=\"ookiYouTube\"/i.test(a))
					return a.substr(0,7)+' class="ookiYouTube"'+a.substr(7);
				else
					return a;
			});
		}
		
		ed.execCommand('mceInsertContent', false, h);
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

YouTubeDialog.preInit();
tinyMCEPopup.onInit.add(YouTubeDialog.init, YouTubeDialog);
