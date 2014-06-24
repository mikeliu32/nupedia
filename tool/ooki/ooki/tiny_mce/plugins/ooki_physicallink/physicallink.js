var PhysicalLinkDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom; n = ed.selection.getNode();
		
		t.m_url = tinyMCEPopup.getWindowArg('plugin_url');
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		$('#seResult').html('<img src="' + ooki_icon_wait() + '"> ' + tinyMCEPopup.getLang('ooki_cmn.searched'));
		
		
		t.m_SelObj = dom.getParent(n, "A");
		t.m_selText = ed.selection.getContent({format:'text'});
			
		$.ajax({
			type: "GET", 
			url: t.m_url + "/workpage/askgoogleTM.php?term=" + encodeURIComponent(t.m_selText) + "&cset=UTF-8",
			//data: request,
			success: function (r) {
				var str, term_arr, out, u;
				
				str = r.replace(/[\n\r ]/g, "");
				if(str == "") 
					return;
					
				term_arr = str.split("TO_SEPARATE_TAG_TM");
				out = "";
				for(var i = 0; i < term_arr[0]; i++) {
					u = B_URL_InsertPath(term_arr[i*2+1], "http://www.google.com.tw/");
					out += 	'<table id="item" cellSpacing="0" cellPadding="2" width="100%">' + 
								'<tr>' + 
									'<td vAlign="top" width="1%" rowSpan="4">' + 
										'<input type="radio" id="rdo" name="rdo" style="border:0px;" value="' + u + '"></td>' + 
									'<td width="1%">' + i + '.</td>' + 
									'<td id="title"><a href="'+u+'" target="_blank">'+term_arr[i*2+2]+'</a></td>' + 
								'</tr>' + 
								'<tr>' + 
									//'<td id="url" colSpan="3">' + u + '</td>' + 
									'<td id="url" colSpan="3"></td>' + 
								'</tr>' + 
							'</table>';
				}
				$('#seResult').html(out);
			}			
		});
		
	},

	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		var oSel, oI, u, n, h;
		
		oSel = $(document.body).find('input').filter(":checked");
		if ( oSel.length > 0 ) {
			u = oSel.val();
			if ( t.m_SelObj ) {
				ed.focus();
				ed.execCommand("mceBeginUndoLevel");
				ed.dom.setAttrib(n, 'title', t.m_selText);
				ed.dom.setAttrib(n, 'href', u);
				ed.execCommand("mceEndUndoLevel");
			}
			else {
				h = '<a href="' + u + '">' + t.m_selText + '</a>';
				ed.execCommand('mceInsertContent', false, h);
			}
		}
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

PhysicalLinkDialog.preInit();
tinyMCEPopup.onInit.add(PhysicalLinkDialog.init, PhysicalLinkDialog);
