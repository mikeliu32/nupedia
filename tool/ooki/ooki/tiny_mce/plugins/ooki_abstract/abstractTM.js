// 
// 在 include 的程式必須設定以下參數

var abstractTM_sEditorID = null;
var abstractTM_LevelMax = 0;
var abstractTM_ShowTreeView = true;
var abstractTM_TableID = 'ooki_abstract_table';
var abstractTM_DataArray;

function abstract_getOnlyId(doc, o) {
	var sLs, x, id, PId, lv;
	
	PId = abstract_getParentId(doc, o);
	if (!PId) {
		PId = "abstract";
		lv = 0;
	}
	else {
		lv = abstract_Id2Level(PId)+1;
	}

	sLs = "," + abstract_getLevelIds(doc, lv, PId) + ",";
	for (x=1;;x++) {
		id = PId + "." + x;
		if (sLs.indexOf("," + id + ",") == -1)
			break;
	}
	return id;
}

function abstract_getLevelIds(doc, lv, PId) {
	var lPId, oEC, count, ss, id;
	oEC = doc.getElementsByTagName("div");
	if (!oEC) return "";
	
	if (lv > 0) PId += ".";
	lPId = PId.length;
	count = oEC.length;
	ss = "";
	for (x=0; x<count; x++) {
		id = oEC[x].id;
		if (abstract_Id2Level(id) != lv) continue;
		if (lv > 0 && id.substr(0,lPId) != PId) continue;
		ss += id + ",";
	}
	return (ss.length ? ss.substr(0,ss.length-1) : "");
}

function abstract_getLevelArray(doc) {
	var arg = new Object();
	arg.oEC = doc.getElementsByTagName("DIV");
	arg.xID = 0;
	return abstract_getLevelArray2(arg,0);
}

function abstract_getLevelArray2(arg, level) {
	var count, r, a, id, lv;
	count = arg.oEC.length;
	r = new Array();
	for (; arg.xID<count; arg.xID++) {
		id  = arg.oEC[arg.xID].id;
		if (!abstract_IsId(id)) continue;
		
		lv = abstract_Id2Level(id);
		if ( lv == level) {
			a = new Array(id, arg.oEC[arg.xID].title);
			r.push(a);
		}
		else if (lv > level) {
			if (r.length) {
				a = abstract_getLevelArray2(arg, lv);
				r[r.length-1].push(a);
				
				arg.xID--;
			}
		}
		else {
			break;
		}
	}
	return r;
}

function abstract_getLevelObjs(oPar, level) {
	var count, oEC, r, a, id, lv;
	oEC = oPar.getElementsByTagName("DIV");
	r = new Array();
	count = oEC.length;
	for (x=0; x<count; x++) {
		id  = oEC[x].id;
		if (!abstract_IsId(id)) continue;
		lv = abstract_Id2Level(id);
		if (lv != level) continue;
		
		r.push(oEC[x]);
	}
	return r;
}

function abstract_Array2Level(doc, aData, oPar) {
	var count, a, r, o, oObjs, bCag;
	count = aData.length;
	for (var x=0; x<count; x++) {
		a = aData[x];
		o = doc.getElementById(a[0]);
		if (o) 
			o.title = a[1];
		if (a.length > 2) 
			abstract_Array2Level(doc, a[2], o);
	}
	
	lv = abstract_Id2Level(oPar.id)+1;
	oObjs = abstract_getLevelObjs(oPar, lv);
	bCag = false;
	for (var x=0; x<count; x++) {
		a = aData[x];
		if (!bCag) {
			//alert(oObjs[x].id + "," + a[0]);
			if (oObjs[x].id != a[0]) {
				bCag = true;
				abstract_Array2Level_getData(doc, aData);
			}
			else
				continue;
		}
		
		oObjs[x].outerHTML = a[3];
	}
}

function abstract_Array2Level_getData(doc, aData) {
	var count, a, r, o, oObjs, bCag;
	count = aData.length;
	for (var x=0; x<count; x++) {
		a = aData[x];
		o = doc.getElementById(a[0]);
		if (o) {
			if (a.length < 3) a.push(new Array());
			a.push(o.outerHTML);
		}
	}
}

function abstract_Array2Html(aData, title) {
	var r;
	r = abstract_Array2Html2(aData);
	if (!r || !r.length) return "";
	
	return 	'<table id="' + abstractTM_TableID + '" class="abstractTM_dir" cellpadding=0 cellspacing=0 border=0>' + 
			'<tr><td>' + 
			'<div id="title"><h3>' + title + '</h3></div>' + 
			r +
			'</td></tr></table>';
}

function abstract_Array2Html2(aData) {

	if (!aData || !aData.length) return;
	
	var count, a, r;
	count = aData.length;
	r = "<ul>";
	for (var x=0; x<count; x++) {
		a = aData[x];
		r += '<li>'
		r += '<a style="text-decoration:none;" href="#' + a[0] + '">' + a[1] + '</a>';
		if (a.length > 2) r += abstract_Array2Html2(a[2]);
		r += '</li>';
	}
	r += "</ul>";
	return r;
}

function abstract_getParentId(doc, o) {
	do {
		o = o.parentElement;
	} while(o && !abstract_IsId(o.id));
	return (o ? o.id : null);
}

// 取得最接近的 '綱要' 指定層
function abstract_getLevelId(doc, lv, o) {
	var oEC, count, id;
	if (lv < 0) return;
	
	oEC = doc.all;
	count = oEC.length;
	for (x=0; x<count; x++) {
		if (oEC[x] == o) break;
		if (abstract_Is(oEC[x]) && abstract_Id2Level(oEC[x].id) == lv) {
			id = oEC[x].id;
		}
	}
	return id;
}

// 取得最接近的 '綱要'
function abstract_getPreId(doc, o) {
	var oEC, count, PId;
	oEC = doc.all;
	count = oEC.length;
	for (x=0; x<count; x++) {
		if (oEC[x] == o) break;
		if (abstract_Is(oEC[x])) 
			PId = oEC[x].id;
	}
	return PId;
}

function abstract_Is(o) {
	return o.tagName == "DIV" && o.id.substr(0,9) == 'abstract.';
}

function abstract_IsId(id) {
	return id.substr(0,9) == 'abstract.';
}

function abstract_Id2Level(id) {
	if (!id || !id.length) return -1;
	
	var l, x;
	l = x = 0;
	while((x=id.indexOf('.',x)) > -1) {
		l++; x++;
	}
	return l-1;
}
