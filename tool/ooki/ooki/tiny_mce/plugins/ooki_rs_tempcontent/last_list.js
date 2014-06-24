
var LastContentDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode(), dom = ed.dom;
		
		tempcontentTM_sKey = parent.tempcontentTM_sKey;
		tempcontentTM_type = parent.tempcontentTM_type;
		
		t.m_bSelObj = false;
		t.m_selText = ed.selection.getContent({format:'text'});
		
		this.g_tabSel = "general_tab";
		
		tinyMCEPopup.resizeToInnerSize();
		
		// var aData = lastcontentTM_DataList().split('\n');
		// var h = t._con_Data2Html( aData );
		// $('#seItems').html( h );
		rs_lastcontentTM_DataList(function(data){
			var h = t._con_Data2Html(data);
			$('#seItems').html(h);
			t.list_data = data;
		});
		
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
		for (var x in data) {
			rec = data[x];
			
			h += 	'<tr id="ritem" t="' + rec.t + '" onclick="LastContentDialog.insert(this);" onMouseOver="style.backgroundColor=\'#eeeeee\';" onMouseOut="style.backgroundColor=\'\';">' + 
						'<td id="ikey">' + rec.T + '</td>' + 
						'<td id="itime">' + B_con_rectime2html(rec.t) + '</td>' + 
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
		
		tTime = $(o).attr("t");
		for (var n in t.list_data) {
			rec = t.list_data[n];
			if (rec.t == tTime) {
				//ed.setContent(rec.C);
				// 最近寫的文章, 回復之前的版本; rec => [t,T,D,C]
				if (parent != window)
					parent.ooki_last_OnDataRecover(rec);
				else
					ooki_last_OnDataRecover(rec);
			}
		}
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

LastContentDialog.preInit();
tinyMCEPopup.onInit.add(LastContentDialog.init, LastContentDialog);
