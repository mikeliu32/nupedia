var ooki_usetcssObjId = 'ookiUserCSSTM';
var API_Tools = "/ooki_tools.php";
var myLayout;


$(document).ready( function(){
	myLayout = $('body').layout({
		name: "syncSet"
	,	defaults: {
			size:					"auto"
		,	paneClass:				"pane" 		// default = 'ui-layout-pane'
		,	resizerClass:			"resizer"	// default = 'ui-layout-resizer'
		,	togglerClass:			"toggler"	// default = 'ui-layout-toggler'
		,	buttonClass:			"button"	// default = 'ui-layout-button'
		,	contentSelector:		".content"	// inner div to auto-size so only it scrolls, not the entire pane!
		,	contentIgnoreSelector:	"span"		// 'paneSelector' for content to 'ignore' when measuring room for content
		,	togglerLength_open:		35			// WIDTH of toggler on north/south edges - HEIGHT on east/west edges
		,	togglerLength_closed:	35			// "100%" OR -1 = full height
		,	hideTogglerOnSlide:		true		// hide the toggler when pane is 'slid open'
		,	togglerTip_open:		"Close This Pane"
		,	togglerTip_closed:		"Open This Pane"
		,	resizerTip:				"Resize This Pane"
		//	effect defaults - overridden on some panes
		,	fxName:					"slide"		// none, slide, drop, scale
		,	fxSpeed_open:			750
		,	fxSpeed_close:			1500
		,	fxSettings_open:		{ easing: "easeInQuint" }
		,	fxSettings_close:		{ easing: "easeOutQuint" }
	}
	,	west: {
			size:					"auto"
		,	minSize:				100
		,	spacing_closed:			21			// wider space when closed
		,	togglerLength_closed:	21			// make toggler 'square' - 21x21
		,	togglerAlign_closed:	"top"		// align to top of resizer
		,	togglerLength_open:		0 			// NONE - using custom togglers INSIDE east-pane
		,	togglerTip_open:		"Close East Pane"
		,	togglerTip_closed:		"Open East Pane"
		,	resizerTip_open:		"Resize East Pane"
		,	slideTrigger_open:		"mouseover"
		,	initClosed:				false
		//	override default effect, speed, and settings
		,	fxName:					"drop"
		,	fxSpeed:				"normal"
		,	fxSettings:				{ easing: "" } // nullify default easing
		}
	});
	
	
	
});

var UserCSSDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		var OUS = ed.OokiUserCSS;
		
		tinyMCEPopup.resizeToInnerSize();
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		t.ed = ed;
		t.m_url = tinyMCEPopup.getWindowArg('plugin_url');
		t.m_urlBase = tinyMCEPopup.getWindowArg('base_url');
		t.m_uset_css_info = tinyMCEPopup.getWindowArg('uset_css_info'); if ( !t.m_uset_css_info ) t.m_uset_css_info = {};
		t.m_sel = tinyMCEPopup.getWindowArg('select');
		t.m_def = t.m_uset_css_info.def_name;

		$("#css_arg #name input")		.keyup( function(){t.arg_update();} );
		$("#css_arg #content textarea")	.keyup( function(){t.arg_update();} );
		$("#css_arg #chkAuto input")	.click( function(){t.arg_update();} );
		
		$("#bnCancel").click( function(){t.OnBnClose();} );
		$("#bnApply").click( function(){t.OnBnApply();} );
		$("#bnDelete").click( function(){t.OnBnDelete();} );
		$("#bnAdd").click( function(){t.OnBnAdd();} );
		
		t.list_reset();
		// default css
		if ( t.m_sel && t.m_sel != "" ) $("#l_list .list_item[rel='" + t.m_sel + "']").click();
		if ( t.m_def && t.m_def != "" ) $("#l_list .list_item[rel='" + t.m_def + "']").addClass("default");
		
	},

	list_reset : function() {
		var t = this, h;
		h = '<ul class="list_items">';
		for (n in t.m_uset_css_info.css_info)
			h += '<li class="list_item" rel="' + n + '">' + n + '</li>';
		h += '</ul>';
		$("#l_list").html(h);
		$("#l_list .list_item").click( function(e){ t.list_Item_OnClick(e, $(this)); } );
	},

	list_Item_OnClick : function(e, o) {
		var t = this;
		$("#l_list .list_item").removeClass("select");
		o.addClass("select");
		t.arg_reset(o.attr("rel"));
		
	},
	
	arg_reset : function(n) {
		var t = this, info;
		info = t.m_uset_css_info.css_info[n];
		if ( info ) {
			$("#css_arg #name input")		.val(info.name)		.attr("old", info.name);
			$("#css_arg #content textarea")	.val(info.content)	.attr("old", info.content);
			$("#css_arg #chkAuto input")	.attr("checked", info.name == t.m_def).attr("old", info.name == t.m_def);
		}
		else {
			$("#css_arg #name input")		.val("").attr("old", "");
			$("#css_arg #content textarea")	.val("").attr("old", "");
			$("#css_arg #chkAuto input")	.attr("checked", false).attr("old", false);
		}
		
		// apply button
		$(".apply_bar #bnApply").attr("disabled", true);
		$(".apply_bar #bnDelete").attr("disabled", false);
		$(".apply_bar #bnAdd").attr("disabled", false);
	},
	
	arg_update : function() {
		var t = this, b=false, oN, oC, oA;
		oN = $("#css_arg #name input");
		oC = $("#css_arg #content textarea");
		oA = $("#css_arg #chkAuto input");
		if ( oN.attr("old") != oN.val() ) b = true;
		if ( !b && oC.attr("old") != oC.val() ) b = true;
		if ( !b && (oA.attr("old") != (oA.attr("checked") ? "true" : "false")) ) b = true;
		
		$(".apply_bar #bnApply").attr("disabled", (b ? false : true) );
		$(".apply_bar #bnDelete").attr("disabled", true);
		$(".apply_bar #bnAdd").attr("disabled", false);
	},
	
	editors_update : function() {
		var t = this;
		t.ed.OokiUserCSS.editors_Update();
	},
	
	OnBnAdd : function() {
		var t = this, nOld, n, C, def, sArg;
		if ( !$(".apply_bar #bnApply").attr("disabled") ) {
			if (!confirm(tinyMCEPopup.getLang("ooki_usercss_dlg.m_is_abort")))	// "是否放棄目前的編輯？"
				return;
		}

		$("#css_arg #name input")		.val("").attr("old", "").focus();
		$("#css_arg #content textarea")	.val("").attr("old", "");
		$("#css_arg #chkAuto input")	.attr("checked", false).attr("old", false);
	},
	
	OnBnApply : function() {
		var t=this, ed=t.ed, nOld, n, C, def, sArg;
		nOld = $("#css_arg #name input").attr("old");
		n = $.trim($("#css_arg #name input").val());
		C = $.trim($("#css_arg #content textarea").val());
		if ( t.m_def == n ) {
			def = $("#css_arg #chkAuto input").attr("checked") ? n : "";
		} else {
			def = $("#css_arg #chkAuto input").attr("checked") ? n : t.m_def;
		}
		if ( !nOld || nOld == "" ) nOld = n;
		if ( n == "" ) {
			alert(tinyMCEPopup.getLang("ooki_usercss_dlg.m_input_name"));	//"請輸入名稱！"
			$("#css_arg #name input").focus();
			return;
		}
		if ( C == "" ) {
			alert(tinyMCEPopup.getLang("ooki_usercss_dlg.m_input_css"));	// "請輸入 CSS Code！"
			$("#css_arg #content textarea").focus();
			return;
		}
			
		sArg = "mode=user_css"
			+	"&act=upd"
			+	"&old=" + encodeURIComponent(nOld)
			+	"&n=" + encodeURIComponent(n)
			+	"&C=" + encodeURIComponent(C)
			+	"&def=" + encodeURIComponent(def)
			;
			
		jQuery.ajax({
			type: "POST"
		,	url: t.m_urlBase + API_Tools
		,	data: sArg
		,	success: function(data, textStatus) {
				if ( B_CheckError_SendResult(data) ) {
					alert(data);
				}
				else {
					// delete old css
					if ( nOld != n ) {
						delete t.m_uset_css_info.css_info[nOld];
					}
					// new or update
					if ( !t.m_uset_css_info ) t.m_uset_css_info = {};
					if ( !t.m_uset_css_info.css_info ) t.m_uset_css_info.css_info = {};
					if ( !t.m_uset_css_info.css_info[n] ) t.m_uset_css_info.css_info[n] = {};
					t.m_uset_css_info.css_info[n].name = n;
					t.m_uset_css_info.css_info[n].content = C;
					
					t.m_def = def;
					t.list_reset();
					
					$("#l_list .list_item[rel='" + n + "']").click();
					t.editors_update();
					
					alert('OK'); 
				}
			}
		,	error: function(XMLHttpRequest, textStatus, errorThrown) {
				if ( textStatus ) alert( textStatus );
				else if ( errorThrown ) alert( errorThrown );
			}
		});
	},

	OnBnDelete : function() {
		var t = this, o, n;
		o = $("#l_list .select");
		if ( !o.length ) return;
		n = o.attr("rel");
		
		if (confirm(tinyMCEPopup.getLang("ooki_usercss_dlg.m_is_del").replace("%s",n))) {	// "是否刪除 \"" + n + "\"？"
			sArg = "mode=user_css"
				+	"&act=del"
				+	"&n=" + encodeURIComponent(n)
				;
			
			jQuery.ajax({
				type: "POST"
			,	url: t.m_urlBase + API_Tools
			,	data: sArg
			,	success: function(data, textStatus) {
					if ( B_CheckError_SendResult(data) ) {
						alert(data);
					}
					else {
						var oNext, nNext;
						// delete old css
						delete t.m_uset_css_info.css_info[n];
						
						oNext = o.next();
						if ( !oNext.length ) oNext = o.prev();
						nNext = oNext.length ? oNext.attr("rel") : "";
				
						t.list_reset();
						if ( nNext != "" )
							$("#l_list .list_item[rel='" + nNext + "']").click();
						t.editors_update();
						
						alert('Delete OK'); 
					}
				}
			,	error: function(XMLHttpRequest, textStatus, errorThrown) {
					if ( textStatus ) alert( textStatus );
					else if ( errorThrown ) alert( errorThrown );
				}
			});
		}
	},
		
	OnBnClose : function() {
		tinyMCEPopup.close();
	}

};

UserCSSDialog.preInit();
tinyMCEPopup.onInit.add(UserCSSDialog.init, UserCSSDialog);
