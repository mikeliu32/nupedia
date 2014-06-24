var ToTableDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		
		t.m_Res = "";
		t.m_temHtml = ed.selection.getContent();
		
		tinyMCEPopup.resizeToInnerSize();
		
		$('#bnOK').click( function(){t.insert();} );
		$('#bnResetRes').click( function(){t.resetRes();} );
		
		t.m_Res = t._parseTable(t.m_temHtml);
		$('#totableTmpResTM').html(t.m_Res);
	},

	insert : function() {
		var t = this, ed = tinyMCEPopup.editor;
		
		ed.execCommand('mceInsertContent', false, t.m_Res);
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	},
	
	resetRes : function () {
		var t = this, ed = tinyMCEPopup.editor;
		var doc=ed.getDoc(), row, colum, tmpText, lines;
		
		row = $('#totableRowSepTM').val();
		colum = $('#totableColumSepTM').val();
		row = t._tranCode(row);
		colum = t._tranCode(colum);

		tmpText = t.m_temHtml;
		tmpText = tmpText.replace(/<\/p>\s*<p>/gi, "<br>");
		tmpText = tmpText.replace(/<p>|<\/p>/gi, "");
		lines = tmpText.split(row);
		t.m_Res = t._getTable(lines, colum);
		$('#totableTmpResTM').html(t.m_Res);
	},
	
	_tranCode : function(text){
		var tranTable_s = new Array("\\n");
		var tranTable_t = new Array("<br(?: /)?>");
		for(var i = 0;i < tranTable_s.length;i++)
		{
			while(text.indexOf(tranTable_s[i]) != -1)
			{
				text = text.replace(tranTable_s[i], tranTable_t[i]);
			}
		}
		var rule = new RegExp(text, "gi");
		return rule;
	},
	
	_parseTable : function(c) {
		var t = this;
		var tmpText, delims, lines, delimiter, i, d;
		delimiter = ",";
		tmpText = c;
		tmpText = tmpText.replace(/<\/p>\s*<p>/gi, "<br>");
		tmpText = tmpText.replace(/<p>|<\/p>/gi, "");
		delims = new Array (",", ";", ":", "=", " ");
		lines = tmpText.split(/<br(?: \/)?>/gi);

		for(d = 0; d < delims.length; d++) {
			var fields = lines[0].split(delims[d]);
			if( fields.length == 1 ) continue;
			for(i = 1; i < lines.length; i++) {
				lines[i] = lines[i].replace(/^\s+|\s+$/, "");
				if( lines[i] == "" ) continue;
				var tmp_fields = lines[i].split(delims[d]);
				if( fields.length != tmp_fields.length ) {
					break;				
				}
			}
			if( i == lines.length ) break;
		}
		if( d < delims.length ) delimiter = delims[d];
		return t._getTable( lines, delimiter );
	},
	
	_getTable : function( lines, delimiter) {
		var tmp_table, fields;
		
		tmp_table = "<table border='1' class='totableTM'>";
		fields = lines[0].split(delimiter);

		tmp_table += "<tr>";
		for(var i = 0; i < fields.length; i++) {
			tmp_table += "<td>" + fields[i] + "</td>";
		}
		tmp_table += "</tr>";

		for(var i = 1; i < lines.length; i++) {
			tmp_table +=  "<tr>";
			var fields = lines[i].split(delimiter);
			for(var j = 0; j < fields.length; j++) {
				tmp_table += "<td>" + fields[j] + "</td>";
			}
			tmp_table += "</tr>";
		}
		tmp_table += "</table>";

		return tmp_table;
	}
	
};

ToTableDialog.preInit();
tinyMCEPopup.onInit.add(ToTableDialog.init, ToTableDialog);
