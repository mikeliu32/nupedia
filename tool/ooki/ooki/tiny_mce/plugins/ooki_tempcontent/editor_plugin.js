var ooki_tempcontentObjId = 'ookiTempContentTM';

(function() {

	// 只能在 NUBraim 上面使用
	if ( !ooki_bCXHtmlView ) 
		return;
		

	tinymce.create('tinymce.plugins.OokiTempContent', {
		getInfo : function() {
			return {
				longname : 'Timely Content plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this, DOM = tinymce.DOM;
			t.editor = ed;
			t.m_url = url;
			
			ed.addButton('lastcontent', {
				title : 'ooki_tempcontent.lc_desc',
				image : url+'/images/lastcontent.gif',
				onclick : function() 
				{
					var u = url + '/last_list.html';
					ed.windowManager.open({
						file : u,
						width : 600,
						height : 500,
						scrollbars : false,
						inline : 1
					}, {
						plugin_url : url
					});
				}
			});

		},
		
		createControl : function(n, cf) {
			switch (n) {
				case "tempcontent":
					var c, t = this, ed = t.editor, WM = ed.windowManager;
					
					
					tempcontentTM_init( ed.id );
					c = ed.controlManager.createButton('tempcontent', {
						title : 'ooki_tempcontent.desc',
						image : t.m_url+'/images/tempcontent.gif',
						onclick : function() {
							tempcontentTM_DataSave( ed.id );
							ed.controlManager.setDisabled('tempcontent', true);
						}
					});
					
					ed.onLoadContent.add(function(ed, o) {
						if (tempcontentTM_bRecover) {
							tempcontentTM_bRecover = false;
							tempcontentTM_DataFind(ed.id, function(old_C){
								if (old_C && old_C != "") {
									var old_a = old_C.split("\n");
									var t = new Date(parseInt(old_a[0])*1000);
									var sMsg = "\"" + t.getFullYear() + "/" + (t.getMonth()+1) + "/" + t.getDate() + " " + t.getHours() + ":" + t.getMinutes() + ":" + t.getSeconds() + ed.getLang('ooki_tempcontent.IsEditAforeData');
									WM.confirm( sMsg, function(state) {
										if (state)
											ed.setContent( old_a[1] );
										else
											tempcontentTM_DataDelete(ed.id);
									});
									old_C = "";
								}
							});
						}
					});
					
					ed.onChange.add(function(ed, l) {
						tempcontentTM_DataChange( ed.id );
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if ( tempcontentTM_o[ed.id].bChang )
							cm.setDisabled('tempcontent', false);
						else
							cm.setDisabled('tempcontent', true);
					});
					
				return c;
			}
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_tempcontent', tinymce.plugins.OokiTempContent);
})();