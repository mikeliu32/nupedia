tinyMCEPopup.requireLangPack('syntax');

var SyntaxDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.code_box.innerHTML = 
			tinyMCEPopup.editor.selection.getContent({	format : 'text'});
		// f.language.style.height = 
		//	tinyMCEPopup.getWindowArg('some_custom_arg');
	},
	insert : function() {
		// Insert the contents from the input into the document
		var code_box_DOM = document.forms[0].code_box;
		var language_DOM = document.forms[0].language;
		var wrapped_code = code_box_DOM.value.replace("<",'&lt;');
		wrapped_code = 
			'<pre class="brush: ' + 
			language_DOM.options[language_DOM.selectedIndex].value + 
			'">' +
			wrapped_code  +
			'</pre><p/>' 
			;
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, wrapped_code);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(SyntaxDialog.init, SyntaxDialog);
