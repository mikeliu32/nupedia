/* 參數列表
tinyMCE.ooki.meta["title"]
tinyMCE.ooki.meta["author"]
tinyMCE.ooki.meta["keywords"]
tinyMCE.ooki.meta["abstract"]
tinyMCE.ooki.meta["description"]
tinyMCE.ooki.meta.onchange 有更新
*/

var QuestDialog = {
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
		
		$('#insert').click( function(){t.update();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		
		
		// init data
		$("#txtTitle").val(tinyMCE.ooki.meta["title"]);
		$("#txtAuthor").val(tinyMCE.ooki.meta["author"]);
		$("#txtKeywords").val(tinyMCE.ooki.meta["keywords"]);
		$("#txtAbstract").val(tinyMCE.ooki.meta["abstract"]);
		$("#txtDescription").val(tinyMCE.ooki.meta["description"]);
		
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
	
	update : function() {
		var t = this;
		
		$(".data_item").each( function() {
			var o=$(this), n, v;
			n = o.attr("rel");
			v = o.val();
			tinyMCE.ooki.meta[n] = v;
		});
	
		if ( typeof(tinyMCE.ooki.meta.onchange) == "function" )
			tinyMCE.ooki.meta.onchange();
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

QuestDialog.preInit();
tinyMCEPopup.onInit.add(QuestDialog.init, QuestDialog);
