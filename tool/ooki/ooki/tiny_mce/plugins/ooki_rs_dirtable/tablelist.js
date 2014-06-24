var ooki_rs_dirtableObjId = 'ookiRSDirTableTM';

var RSDirTableDialog = {
	preInit : function() {
		var url;
		
		tinyMCEPopup.requireLangPack();
		
		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this;
		t.editor = tinyMCEPopup.editor;
		t.m_url = tinyMCEPopup.getWindowArg('plugin_url');
		t.m_data = tinyMCEPopup.getWindowArg('data').split('\r\n');
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );

		
		t._resetList();
		
	},

	_getObj : function(n) {
		while ( n && n.className != ooki_abstractObjId )
			n = n.parentNode;
		return n;
	},
	
	_resetList : function() {
		var t = this, ed = t.editor, cnt;
		if ( typeof(t.m_data) != 'object' || !(cnt=t.m_data.length) ) 
			return;
		
		var data = t.m_data;
		var ot = new Date();
		var h = 	'<table id="tt" width="100%" cellpadding=0 cellspacing=0 border=0>' + 
					'  <tr id="ritem">' + 
					'    <th>' + ed.getLang('ooki_rs_dirtable.FTitle') + '</th>' + 
					'  </tr>';
		for (var x=0; x<cnt; x++) 
		{
			if ( !data[x] ) continue;
			cols = data[x].split("\t");
			if (cols.length < 2) continue;
			
			t = parseInt(cols[1]);
			ot.setTime(t*1000);
			
			h += 	'<tr id="ritem" rid=' + x + ' dU="' + cols[0] + '" dT="' + cols[1] +'" onclick="RSDirTableDialog.insert(this);" onMouseOver="style.backgroundColor=\'#eeeeee\';" onMouseOut="style.backgroundColor=\'\';">' + 
					'  <td>' + cols[1] + '</td>' + 
					'</tr>';
		}
		h += '</table>';
		
		seItems.innerHTML = h;
	},
	
	insert : function(o) {
		var t = this, ed = t.editor;
		var data = "title:'" + o.dT + "',src:'" + o.dU + "'";
		var h = '<img class="' + ooki_rs_dirtableObjId + '" title="' + o.dT + '" data="' + data + '" src="' + tinyMCE.baseURL +'/themes/advanced/img/spacer.gif" />';
		ed.execCommand('mceInsertContent', false, h);
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
	
};

RSDirTableDialog.preInit();
tinyMCEPopup.onInit.add(RSDirTableDialog.init, RSDirTableDialog);
