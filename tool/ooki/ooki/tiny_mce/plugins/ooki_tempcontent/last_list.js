var LastContentDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode(), dom = ed.dom;
		
		t.m_bSelObj = false;
		t.m_selText = ed.selection.getContent({format:'text'});
		
		this.g_tabSel = "general_tab";
		
		tinyMCEPopup.resizeToInnerSize();
		
		var aData = lastcontentTM_DataList().split('\n');
		var h = t._con_Data2Html( aData );
		$('#seItems').html( h );
		
	},

	_con_Data2Html : function(data) {
		var t = this, cnt, ot, h;
		
		cnt = data.length;
		if (!cnt) return;
		
		h = 	'<table id="tt" width="100%" cellpadding=0 cellspacing=0 border=0>' + 
					'  <tr id="ritem">' + 
					'    <th>' + tinyMCEPopup.getLang('ooki_tempcontent.lc_Title') + '</th>' + 
					'    <th>' + tinyMCEPopup.getLang('ooki_tempcontent.lc_Time') + '</th>' + 
					'  </tr>';
		for (x=0; x<cnt; x++) {
			cols = data[x].split("\t");
			if (cols.length < 2) continue;
			
			h += 	'<tr id="ritem" rid="' + cols[0] + '" onclick="LastContentDialog.insert(this);" onMouseOver="style.backgroundColor=\'#eeeeee\';" onMouseOut="style.backgroundColor=\'\';">' + 
						'<td id="ikey">' + cols[1] + '</td>' + 
						'<td id="itime">' + t._com_Time2Addr(cols[2]) + '</td>' + 
					'</tr>';
		}
		h += "</table>";
		
		return h;
	},
	
	_com_Time2Addr : function(st) {
		var oT = new Date();
		var oTC = new Date();
		oTC.setTime(parseInt(st)*1000);
		if (oTC.getFullYear() != oT.getFullYear() )
			return oTC.getFullYear()+"/" 
					+(oTC.getMonth()+1 < 10 ? "0" : "")+(oTC.getMonth()+1)+"/"
					+(oTC.getDate() < 10 ? "0" : "")+oTC.getDate();
		else if ( oTC.getMonth() != oT.getMonth() || oTC.getDate() != oT.getDate() )
			return (oTC.getMonth()+1) + tinyMCEPopup.getLang('ooki_tempcontent.lc_Month') 
					+(oTC.getDate() < 10 ? "0" : "")+oTC.getDate()+tinyMCEPopup.getLang('ooki_tempcontent.lc_Day');
		else 
			return (oTC.getHours() < 10 ? "0" : "") + oTC.getHours() + ":" 
					+(oTC.getMinutes() < 10 ? "0" : "")+oTC.getMinutes();
	},
	
	insert : function(o) {
		var t = this, ed = tinyMCEPopup.editor;
		var k;
		
		k = $(o).attr("rid");
		ed.setContent(lastcontentTM_DataGet(k));
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

LastContentDialog.preInit();
tinyMCEPopup.onInit.add(LastContentDialog.init, LastContentDialog);
