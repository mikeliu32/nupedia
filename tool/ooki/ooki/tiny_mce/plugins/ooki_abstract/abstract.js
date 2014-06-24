var ooki_abstractObjId = 'ookiAbstractTM';

var AbstractDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		
		t.m_selData = tinyMCEPopup.getWindowArg('selData');
		t.m_SelObj = null;
		t.m_Title = "";
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		
		if ( (t.m_SelObj = t._getObj(n)) ) {
			t.m_Title = t.m_SelObj.title;
		}
		
		$('#txtTitle').val(t.m_Title);
		
	},

	_getObj : function(n) {
		while ( n && n.className != ooki_abstractObjId )
			n = n.parentNode;
		return n;
	},
	
	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		var T, h, oid, selHtml;
		
		T = $('#txtTitle').val();
		if (t.m_SelObj) {
			t.m_SelObj.title = T;
		}
		else {
			selHtml = t.m_selData;
			oid = abstract_getOnlyId(ed.getDoc(), n);
			h = '<div ' +
				' id="' + oid + '"' + 
				' title="' + T + '"' + 
				' class="' + ooki_abstractObjId + '"' + 
				' >' + selHtml + '</div>';
			
			ed.execCommand('mceInsertContent', false, h);
		}
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

AbstractDialog.preInit();
tinyMCEPopup.onInit.add(AbstractDialog.init, AbstractDialog);
