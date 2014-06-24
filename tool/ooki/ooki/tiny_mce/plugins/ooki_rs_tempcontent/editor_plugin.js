var ooki_tempcontentObjId = 'ookiRSTempContentTM';

(function() {

	tinymce.create('tinymce.plugins.OokiRSTempContent', {
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
			
			ed.addButton('rs_lastcontent', {
				title : 'ooki_rs_tempcontent.lc_desc',
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
				case "rs_tempcontent":
					var c, t = this, ed = t.editor, WM = ed.windowManager;
					
					tempcontentTM_init( ed.id, false );
					c = ed.controlManager.createButton('rs_tempcontent', {
						title : 'ooki_rs_tempcontent.desc',
						image : t.m_url+'/images/tempcontent.gif',
						onclick : function() {
							tempcontentTM_DataSave( ed.id );
							ed.controlManager.setDisabled('rs_tempcontent', true);
						}
					});
					
					ed.onLoadContent.add(function(ed, o) {
						if (tempcontentTM_bRecover) {
							tempcontentTM_bRecover = false;
							tempcontentTM_rs_DataFind(function(rec){
								if (rec && rec.C && rec.C != "") {
									var t = new Date(B_con_rectime2second(rec.t)*1000);
									var sMsg = "\"" + t.getFullYear() + "/" + (t.getMonth()+1) + "/" + t.getDate() + " " + t.getHours() + ":" + t.getMinutes() + ":" + t.getSeconds() + ed.getLang('ooki_tempcontent.IsEditAforeData');
									WM.confirm( sMsg, function(state) {
										if (state)
											ooki_last_OnDataRecover(rec);
											//ed.setContent(rec.C);
										else
											tempcontentTM_rs_DataDelete();
											//tempcontentTM_DataDelete(ed.id);
									});
								}
							});
						}
					});
					
					ed.onChange.add(function(ed, l) {
						tempcontentTM_DataChange( ed.id );
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if (tempcontentTM_bChang)
							cm.setDisabled('rs_tempcontent', false);
						else
							cm.setDisabled('rs_tempcontent', true);
					});
					
				return c;
			}
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_rs_tempcontent', tinymce.plugins.OokiRSTempContent);
})();