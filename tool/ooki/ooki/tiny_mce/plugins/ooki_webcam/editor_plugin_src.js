
(function() {
	tinymce.create('tinymce.plugins.OokiWebcam', {
		getInfo : function() {
			return {
				longname : 'Webcam plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;

			// Register commands
			//ed.addCommand('mceAudio', function() {
			//});

			// Register buttons
			ed.addButton('audio', {
				title : 'ooki_webcam.audio_desc',
				image : url+'/images/audio.gif',
				//cmd : 'mceAudio'
				onclick : function() {
					t._Command(ed, url, 'mceAudio');
				}
			});
			ed.addButton('video', {
				title : 'ooki_webcam.video_desc',
				image : url+'/images/video.gif',
				//cmd : 'mceVideo'
				onclick : function() {
					t._Command(ed, url, 'mceVideo');
				}
			});

			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');

			ed.onNodeChange.add(function(ed, cm, n) {
				var r = t._parseObj(n);
				cm.setActive('audio', r.cmd == "audio");
				cm.setActive('video', r.cmd == "video");
			});
			

		},

		_parse : function(s) {
			return tinymce.util.JSON.parse('{' + s + '}');
		},
		
		_parseObj : function(o) {
			var r = {};
			if (o.nodeName == "IMG" && o.className == "mceItemWindowsMedia") {
				var p = this._parse(o.title);
				var f = p.src.toLowerCase();
				if (f.substring(f.length-4) == ".wav") 
					r.cmd = "audio";
				if (f.substring(f.length-4) == ".avi") 
					r.cmd = "video";
				r.f = f;
			}
			return r;
		},
		
		_Command : function(ed, url, cmd) {
			var t = this;
			var ElemSel = ed.selection.getNode();
			var r = t._parseObj(ElemSel);
			var file = "";
			var id = "";
			var sw = 0;
			var sh = 0;
			switch (cmd) {
				// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
				case "mceAudio":
					id = "webcam_audio";
					sw = 200;
					sh = 45;
					if (ElemSel) {
						if (r.cmd == "audio") {
							file = r.f;
						}
						else if (ElemSel.nodeName == "A" && ElemSel.id == "webcam_audio") {
							file = t.b_GetFileName(ElemSel.href);
						}
						
						if (file == "") ElemSel = null;
					}
					break;
				
				case "mceVideo":
					id = "webcam_video";
					sw = 320;
					sh = 300;
					if (ElemSel) {
						if (r.cmd == "video") {
							file = r.f;
						}
						else if (ElemSel.nodeName == "A" && ElemSel.id == "webcam_video") {
							file = t.b_GetFileName(ElemSel.href);
						}
						
						if (file.length == 0) ElemSel = null;
					}
					break;
				
				default:
					return false;
			}
			
			var sResult = external.EA_GetWebcam(cmd, file);
			if (!sResult.length) return true;
			
			var a = sResult.split("\t");
			var m = a[0];	// mode
			var f = a[1];	// file
			f = f.replace("\\", "/");
			
			var text = "";
			// insert player
			if (m == "0") {
				text = "<img id=" + id + " class=mceItemWindowsMedia title=\"src:'" + f + "',id:'" + id + "',width:'" + sw + "',height:'" + sh + "',autostart:false\" width=" + sw + " height=" + sh + " src=\"/sopedia/tiny_mce/themes/advanced/images/spacer.gif\">";
			}
			// insert link
			else {
				var n = f.substring(f.lastIndexOf("/")+1);
				text = "<a id=" + id + " href=\"" + f + "\">" + n + "</a>";
			}
			
			if (ElemSel)
				ElemSel.outerHTML = text;
			else
				tinyMCE.execCommand("mceInsertContent", false, text);
				
			return true;
		},
			
		b_GetFileName : function(f) {
			var x = f.lastIndexOf("/");
			if (x == -1) x = f.lastIndexOf("\\");
			if (x == -1) return f;
			return f.substring(x+1);
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_webcam', tinymce.plugins.OokiWebcam);
})();