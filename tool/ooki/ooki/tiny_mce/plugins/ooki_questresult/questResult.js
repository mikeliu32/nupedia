var g_oArg = window.opener.dialogArguments;
var g_ei = g_oArg.ei;
var g_item = "";

var sys_ed = tinyMCE.editor;
var sys_bObj = false;
var sys_selText = "";

attachEvent("onload", Init);
function Init()
{
	var n, selText, bBoj=false;
	
	n = sys_ed.selection.getNode();
	sys_selText = sys_ed.selection.getContent({format:'text'});
	if ( n && n.nodeName == 'A' ) {
		sys_bObj = true;
		sys_selText = n.title;
		if ( sys_selText == "" )
			sys_selText = n.innerText;
	}
	
	$(document).find('#q').val(sys_selText);
}

function GetSelectItem()
{
	var ss = "";
	var chkItems = document.getElementsByName("chkItem");
	var count = chkItems.length;
	for (i=0; i<count; i++) {
		if (!chkItems[i].checked) continue;
		var tr = chkItems[i].parentElement.parentElement;
		var oItem = tr.all("divItem");
		ss += oItem.outerHTML;
	}
	return ss;
}

function GetSelectEngine()
{
	var ss = "";
	var oEC = document.getElementsByName("rdoEng");
	for (i=0; i<oEC.length; i++) {
		if (!oEC[i].checked) continue;
		form1.I.value = oEC[i].value;
		return oEC[i].value;
	}
	oEC[i].value = "";
	return "";
}

function BnInsert() 
{
	var ss = GetSelectItem();
	if (!ss.length) return;
	
	var I = GetSelectEngine();
	var bImg = (I == "g_img" || I == "y_img" || I == "f_img");
	
	var mce = window.opener.tinyMCE;
	var inst = sys_ed;
	mce.selectedInstance =  sys_ed;
 	var oSel = mce.selectedElement;
	var oImgs = B_ParentElement_ID(oSel,"qr_img");
	
	//alert("oImgs="+oImgs+", bImg="+bImg+", oSel="+oSel.tagName+", ");
	if (oImgs)
	{
		if (bImg)
			oImgs.insertAdjacentHTML("beforeEnd", ss);
		else
			oImgs.insertAdjacentHTML("afterEnd", ss);
	}
	else
	{
		if (bImg) {
			if (oSel) {
				ss = "<div id=\"qr_img999\" style=\"width:100%;\">" + ss + "</div>";
				mce.execCommand("mceInsertContent", false, ss);
				var oNew = inst.getDoc().getElementById("qr_img999");
				oNew.id = "qr_img";
				inst.selection.selectNode(oNew);
			}
			else {
				ss = "<div id=\"qr_img\" style=\"width:100%;\">" + ss + "</div>";
				mce.execCommand("mceInsertContent", false, ss);
			}
		}
		else 
			mce.execCommand("mceInsertContent", false, ss);
	}
	
}

function BnCancel() {
	window.close();
}

function checkData()
{
	if (!form1.q.value.length) return false;
	
	GetSelectEngine();
	g_item = form1.I.value;
	divResult.innerHTML = "<div style=\"padding:5 5 5 5;\"><img align=absmiddle src=\"images/wait.gif\" border=0> &nbsp;搜尋中...請稍後...</div>";
	return true;
}

function rdo_Change()
{
	if ( checkData() )
		form1.submit();
}

function checkData2(o)
{
	document.body.style.cursor = "wait";
	return true;
}

function fs_OK()
{
	document.body.style.cursor = "auto";
	divResult.innerHTML = sr_if.document.body.innerHTML;
	divResult.scrollTop = 0;
	
	ReserImgSize();
}

function ReserImgSize()
{
	var asl = new Array(2);
	switch (g_item) {
		case "g_img":
		case "y_img":
		case "f_img":
			asl[0] = 150;
			asl[1] = 150;
			break;
			
		case "y_vdo":
		case "t_vdo":
			asl[0] = 120;
			asl[1] = 90;
			break;
		
		default:
			return;
	}

	var EC = document.getElementsByTagName("img");
	var count = EC.length;
	for (x=0; x<count; x++) {
		var as = new Array(EC[x].width, EC[x].height);
		GetLimitSize(as,asl);
		EC[x].width = as[0];
		EC[x].height = as[1];
	}
}

function GetLimitSize(as, asl)
{
	if (as[0] > asl[0] || as[1] > asl[1]) {
		r0 = asl[0] / as[0];
		r1 = asl[1] / as[1];
		r = r0 < r1 ? r0 : r1;
		as[0] = as[0] * r;
		as[1] = as[1] * r;
	}
}

function B_ParentElement_ID(o,id)
{
	while (o && o.id != id) {
		o = o.parentElement;
	}
	return o;
}

