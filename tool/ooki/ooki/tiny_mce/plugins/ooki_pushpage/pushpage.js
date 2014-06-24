var ooki_questObjId = 'ooki_PushPageTM';
var sys_img_width = 120;

var PushPageDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		
		tinyMCEPopup.resizeToInnerSize();
		
		t.img_info = [];
		t.img_read_number = 0;
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#form1').submit( function(ev){t.form_OnSubmit(); ev.preventDefault()} );
		$('#insert').click( function(ev){t.insert(); ev.preventDefault()} );
		$('#cancel').click( function(ev){t.OnBnClose(); ev.preventDefault()} );
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
	
	B_CheckError_SendResult : function(r){
		if ( (typeof(r) == "array" || typeof(r) == "object" ) && r['error'] ) return true;
		if (r.length > 128) r = r.substr(0,128);
		re = new RegExp("error:|<b>Warning</b>:|<b>Fatal error</b>:|<b>Notice</b>:", "i");
		return re.test(r);
	},
	
	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		var oRes, h;
		
		oRes = $('#seResult');
		oRes.find('#title').html( '<a href="'+t.data.url+'">'+t.data.title+'</a>' );
		oRes.find('#img').html( '<a href="'+t.data.url+'">'+oRes.find('#img').html()+'</a>' ).attr("valign","middle");
		oRes.find('#seBnts').remove();
		h = oRes.html();
		ed.execCommand('mceInsertContent', false, h);
		
		t.OnBnClose();
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	},
	
	form_OnSubmit : function() {
		var t = this, url, request_data;
		url = $('#txtUrl').val();
		if ( url.length == 0 ) return;
		if ( !B_IsUrl(url) ) {
			url = "http://" + url;
			$('#txtUrl').val(url);
		}
		
		$('#seResult').html( '<span class="wait"> 資料讀取中．．． </span>' );
		
		request_data = "url=" + encodeURIComponent(url);
		$.ajax({
			type: "POST", 
			url: "pushpage.php",
			data: request_data, 
			async : false,
			dataType: "json",
			success: function(data) {
				if ( t.B_CheckError_SendResult(data) )
					alert( data );
				else {
					t.img_info = [];
					t.img_read_number = 0;
					t.data = data;
					t.sys_cur = 0;
					$('body').append(t._con_imgs2html(data.imgs));
					
					window.setTimeout( function(){t._con_html2imginfo();}, 1000 );
				}
			}
		});
		//seResult
		
		return false;
	},
	
	Bn_OnClick : function(o) {
		var t = this, rel = $(o).attr('rel'), u, oImg;
		switch (rel) {
			case "bnPre":
				t.sys_cur--;
				if ( t.sys_cur < 0 ) t.sys_cur = t.img_info.length -1;
				u = t.img_info[t.sys_cur].url;
				$('#seResult #img img').attr('src', u).attr('title', u).width( (t.img_info[t.sys_cur].width > 100 ? 100 : t.img_info[t.sys_cur].width) + "px" );
				$('#seResult #imginfo').html( t.sys_cur+1 + "/" + t.img_info.length );
				break;
				
			case "bnNext":
				t.sys_cur++;
				if ( t.sys_cur >= t.img_info.length ) t.sys_cur = 0;
				u = t.img_info[t.sys_cur].url;
				$('#seResult #img img').attr('src', u).attr('title', u).width( (t.img_info[t.sys_cur].width > 100 ? 100 : t.img_info[t.sys_cur].width) + "px" );
				$('#seResult #imginfo').html( t.sys_cur+1 + "/" + t.img_info.length );
				break;
		}
	},
	
	_con_data2html : function() {
		var t = this, h_img, h_bnts, HostIP;
		h_img=""; h_bnts="";
		if ( t.img_info.length > 0 ) {
			u = t.img_info[0].url;
			w = t.img_info[0].width > sys_img_width ? sys_img_width : t.img_info[0].width;
			h_img = '<td id="img" width="'+(sys_img_width+10)+'px;" align="center" valign="middle">' + 
						'<img width="'+w+'px" title="'+u+'" src="'+u+'">' + 
					'</td>';
			h_bnts ='<div id="seBnts">' + 
						'<img align="absmiddle" class="bn2 bnPre" rel="bnPre" id="bnPre" src="/css/layout_images/arrow_p_f1.gif" />' + 
						'<img align="absmiddle" class="bn2 bnNext" rel="bnNext" id="bnNext" src="/css/layout_images/arrow_n_f1.gif" />' + 
						'<span id="imginfo">1/'+t.img_info.length+'</span>' + 
					'</div>';
		}
		HostIP = B_URL_MakeIPPort(t.data.url,false,false);
		return 	'<table id="' + ooki_questObjId + '" width="100%" cellpadding=0 cellspacing=0 border=0><tr>' + 
					h_img + 
					'<td valign="top" style="padding-left:5px;">' + 
						'<span id="title" style="font-weight: bold;">'+t.data.title+'</span><br>' + 
						'<span id="url" style="color:#808080;">'+HostIP+'</span><br>' + 
						'<span id="desc">'+t.data.desc+'</span><br>' + 
						h_bnts + 
					'</td>' + 
				'</tr></table>';
	},
	
	_con_html2imginfo : function() {
		var t = this, bOK;
		t.img_read_number++;
		bOK = true;
		$('#esImgs img').each( function(){
			var o = $(this), w, h, oImg;
			w = o.width();
			h = o.height();
			if ( !w || !h ) {
				bOK = false;
				return;
			}
			if ( (w > 20 && h > 20) && ((w > h && w < h*4) || (w < h && w*4 < h)) ) {
				oImg = {}
				oImg.url = $(this).attr('src');
				oImg.width = w;
				oImg.height = h;
				t.img_info.push(oImg);
			}
			o.remove();
		});
		if ( !bOK && t.img_read_number <= 3 )
			window.setTimeout( function(){t._con_html2imginfo();}, 1000 );
		else {
			$('#esImgs').remove();
			html = t._con_data2html();
			$('#seResult').html( html );
			$('#seResult .bn2').click( function(ev){t.Bn_OnClick(this); ev.preventDefault()} );
		}
			
	},
	
	_con_imgs2html : function(aImg) {
		var res, cnt;
		res = '<div id="esImgs">';
		for (var x=0; x<aImg.length; x++)
			res += '<img src="' + aImg[x] + '">';
		res += "</div>";
		return res;
	}
};

PushPageDialog.preInit();
tinyMCEPopup.onInit.add(PushPageDialog.init, PushPageDialog);
