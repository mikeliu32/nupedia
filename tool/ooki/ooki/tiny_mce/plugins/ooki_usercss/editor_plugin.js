var ooki_usercssObjId = 'ooki_usercssTM_';
var SYS_UserCSS_FP_DATA = '/db/ooki_user_css.js';
var ooki_user_css_info = {};

(function() {
	tinymce.create('tinymce.plugins.OokiUserCSS', {
		getInfo : function() {
			return {
				longname : 'User CSS plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			ed.OokiUserCSS = t;
			t.editor = ed;
			t.m_url = url;
			t.m_sel = "";
			
			// Gets executed before DOM to HTML string serialization 
			ed.onSetContent.add(function(ed, o) {
				// 自動套用 css
				if ( ooki_user_css_info.def_name && ooki_user_css_info.css_info[ooki_user_css_info.def_name] ) {
					var doc=ed.getDoc(), N, C, bSet=false;
					N = ooki_user_css_info.def_name;
					C = ooki_user_css_info.css_info[ooki_user_css_info.def_name].content;
					
					// 檢查是否設定過 css
					$(doc.body).find("style").each( function() {
						var n = $(this).attr("id");
						if ( n.indexOf(ooki_usercssObjId) > -1 ) {
							bSet = true;
							
							n = n.substr(ooki_usercssObjId.length);
							t.m_btn.select( n );
							t.m_sel = n;
						}
					});
					// 設定
					if ( !bSet ) {
						$(doc.body).find("style").remove();
						h = '<style id="' + ooki_usercssObjId + N + '">' + C + '</style>';
						ed.setContent( h + ed.getContent() );
						
						t.m_btn.select( N );
						t.m_sel = N;
					}
				}
			});
			
		},
		
		createControl : function(n, cf) {
			switch (n) {
				case "usercss":
					var btn, t = this, ed = t.editor;
					btn = ed.controlManager.createListBox('usercss', {
						title : 'ooki_usercss.desc', 
						width : 20,
						onselect : function(v) {
							if ( v == "edit" ) {
								ed.windowManager.open({
									file : t.m_url + '/UserCSS.htm',
									width : 500,
									height : 400,
									scrollbars : false,
									inline : 1
								}, {
									base_url : tinymce.baseURL,
									plugin_url : t.m_url,
									uset_css_info : ooki_user_css_info,
									select : t.m_sel
								});
								
								setTimeout( function()	{
									t.m_btn.select( t.m_sel );
								}, 10);
								return;
							}
							
							// select
							if (1) {
								var doc=ed.getDoc(), h="";
								$(doc.body).find("style").remove();
								if (ooki_user_css_info.css_info[v]) {
									h = '<style id="' + ooki_usercssObjId + v + '">' + ooki_user_css_info.css_info[v].content + '</style>';
									ed.setContent( h + ed.getContent() );
								}
								
								t.m_sel = v;
							}
						}
					});
					
					// Load Data Info
					jQuery.ajax({
						type: "GET"
					,	url: tinymce.baseURL + SYS_UserCSS_FP_DATA
					,	dataType: "script"
					,	success: function(data, textStatus) {
							if (ooki_user_css_info && ooki_user_css_info.css_info) {
								// create list button
								var infos = ooki_user_css_info.css_info;
								for (n in infos) {
									btn.add(n, n); 
								}
							}
							btn.add(ed.translate('ooki_usercss.edit'), "edit");
						}
					,	error: function(XMLHttpRequest, textStatus, errorThrown) {
							// if ( textStatus ) alert( textStatus );
							// else if ( errorThrown ) alert( errorThrown );
							btn.add(ed.translate('ooki_usercss.edit'), "edit");
						}
					});
					
					t.m_btn = btn;
				return btn;
			}
		},
		
		editors_Update : function() {
			ooki_editors_reload();
		}
	});

	
	// Register plugin
	tinymce.PluginManager.add('ooki_usercss', tinymce.plugins.OokiUserCSS);
})();