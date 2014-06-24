var ooki_abstractObjId = 'ookiAbstractTM';

var AbstractAdvDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		
		t.m_SelObj = null;
		t.m_Title = "";
		t.m_bShowTreeView = ed.ooki_abstract_bShowTreeView;
		t.m_selItem = null;
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		
		if ( (t.m_SelObj = t._getObj(n)) ) {
			t.m_Title = t.m_SelObj.title;
		}
		
		t.m_data = abstract_getLevelArray(ed.getDoc());
		if ( t.m_data && t.m_data.length > 0 ) {
			t.m_selItem = t.m_data[0][0];
			$('#seView').html(t._con_Data2Html(t.m_data));
		}
		$('#chkShowTreeView').attr("checked", t.m_bShowTreeView);
		
	},

	_getObj : function(n) {
		while ( n && n.className != ooki_abstractObjId )
			n = n.parentNode;
		return n;
	},
	
	_con_Data2Html : function(aData) {
		if (!aData || !aData.length) return;
		
		var t = this;
		var count, a, r, cn;
		count = aData.length;
		r = "<ul>";
		for (var x=0; x<count; x++) {
			a = aData[x];
			cn = (t.m_selItem == a[0] ? ' class="select"' : '');
			r += '<li>';
			r += '<span id="' + a[0] + '" style="cursor:hand;" onmouseover="style.backgroundColor=\'#dddddd\';" onmouseout="style.backgroundColor=\'\';" onclick="AbstractAdvDialog.OnSelect(this);" ondblclick="AbstractAdvDialog.OnEdit(this);"' + cn + '>' + a[1] + '</span>';
			if (a.length > 2) r += t._con_Data2Html(a[2]);
			r += '</li>';
		}
		r += "</ul>";
		return r;
	},

	_con_Html2Data : function(aData) {
		if (!aData || !aData.length) return;
		
		var t = this, doc = t.editor.getDoc();
		var count, a, r, o;
		count = aData.length;
		for (var x=0; x<count; x++) {
			a = aData[x];
			o = doc.getElementById(a[0]);
			if (o) 
				a[1] = o.innerText;
			if (a.length > 2) 
				t._con_Html2Data(a[2]);
		}
	},

	_isId : function(id) {
		return id.substr(0,9) == 'abstract.';
	},
	
	_Id2Level : function(id) {
		if (!id || !id.length) return -1;
		
		var l, x;
		l = x = 0;
		while((x=id.indexOf('.',x)) > -1) {
			l++; x++;
		}
		return l-1;
	},
	
	_getLevelArray : function(doc) {
		var arg = new Object();
		arg.oEC = doc.getElementsByTagName("SPAN");
		arg.xID = 0;
		return this._getLevelArray2(arg,0);
	},

	_getLevelArray2 : function(arg, level) {
		var t = this;
		var count, r, a, id, lv;
		count = arg.oEC.length;
		r = new Array();
		for (; arg.xID<count; arg.xID++) {
			id  = arg.oEC[arg.xID].id;
			if (!t._isId(id)) continue;
			
			lv = t._Id2Level(id);
			if ( lv == level) {
				a = new Array(id, arg.oEC[arg.xID].innerHTML);
				r.push(a);
			}
			else if (lv > level) {
				if (r.length) {
					a = t._getLevelArray2(arg, lv);
					r[r.length-1].push(a);
					
					arg.xID--;
				}
			}
			else {
				break;
			}
		}
		return r;
	},
	
	insert : function() {
		var t = this, ed = tinyMCEPopup.editor;
		var doc = ed.getDoc();
		
		t.m_bShowTreeView = $('#chkShowTreeView').attr("checked");
		t.m_data = t._getLevelArray(document);
		
		ed.ooki_abstract_bShowTreeView = t.m_bShowTreeView;
		abstract_Array2Level(doc, t.m_data, doc);
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	},
	
	OnSelect : function(o) {
		if (o.id == this.m_selItem) return;
		
		var oOld = document.getElementById(this.m_selItem);
		if (oOld) oOld.className = "";
		
		this.m_selItem = o.id;
		o.className = "select";
	},

	OnEdit : function(o) {
		var T = o.innerHTML;
		o.innerHTML = '<input id=inT value="' + T + '" size=40 onfocusout="AbstractAdvDialog.OnEditEnd(this)">';
		o.ondblclick = null;
		var oIn = o.all("inT");
		if (oIn) oIn.focus();
	},

	OnEditEnd : function(o) {
		var T = o.value;
		o = o.parentElement;
		o.innerHTML = T;
		o.ondblclick = function() {OnEdit(o);}
	},

	OnItemPre : function() {
		var oLi, oLiPre, oUl, oEC, xID, sLi;
		do {
			oLi = document.getElementById(this.m_selItem);
			if (!oLi) break;
			oLi = oLi.parentElement;
			if (!oLi) break;
			oLiPre = oLi.previousSibling;
			if (!oLiPre) break;
			
			sLi = oLi.outerHTML;
			oLi.outerHTML = "";
			oLiPre.insertAdjacentHTML("beforeBegin", sLi);
			
		} while(0);
	},

	OnItemNext : function() {
		var oLi, oLiNext, oUl, oEC, xID, sLi;
		do {
			oLi = document.getElementById(this.m_selItem);
			if (!oLi) break;
			oLi = oLi.parentElement;
			if (!oLi) break;
			oLiNext = oLi.nextSibling;
			if (!oLiNext) break;
			
			sLi = oLi.outerHTML;
			oLi.outerHTML = "";
			oLiNext.insertAdjacentHTML("afterEnd", sLi);
			
		} while(0);
	}
	
};

AbstractAdvDialog.preInit();
tinyMCEPopup.onInit.add(AbstractAdvDialog.init, AbstractAdvDialog);
