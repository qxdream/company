//用于IE
function getSel(id) {
	if(!document.all) { return false; }
	var obj = FCKeditorAPI.GetInstance(id);
	obj.Focus();
	var range = obj.EditorDocument.selection.createRange();
	return range;
}
//插入内容到编辑器
function addhtml(content, id, sel){
	var oEditor = FCKeditorAPI.GetInstance(id);
	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG ) {
		document.all ? sel.pasteHTML(content) : oEditor.InsertHtml(content);
	} else {
		alert('请先转换到所见即所得模式') ;
	}
}

//获取顶级window对象
function getTopWin() {
	var p = window.parent;
	var c = window;
	while(p != c) {
		c = p;
		p = p.parent;
	}
	return c;
}

function getId(id) {
	return "string" == typeof id ? document.getElementById(id) : id;
}
function classCreate() {
	return function() {
		this.init.apply(this, arguments);
	}
}
function extend(defaults, curSettings) {
	for(var k in curSettings) {
		defaults[k] = curSettings[k];
	}
	return defaults;
}

function ltrim(s) {
	return s.replace( /^\s*/, "");
}
function rtrim(s) {
	return s.replace( /\s*$/, "");
}
function trim(s) {
	return rtrim(ltrim(s));
}
function setCookie(name,value,expires, path,domain,secure) {
	document.cookie = name + "=" + encodeURI(value) +
	( (expires) ? ";expires=" + expires : "" ) +
	( (path) ? ";path=" + path : "" ) +
	( (domain) ? ";domain=" + domain : "" ) +
	( (secure) ? ";secure" : "");
}
function getCookie(name){
	var cookie_string = document.cookie;
	var cookie_array = cookie_string.split(";");
	for(var i=0;i<cookie_array.length;i++){
		var cookie_num = cookie_array[i].split("=");
		var cookie_name = cookie_num[0];
		var cookie_value = cookie_num[1];
		if(trim(cookie_name)==name){
			return decodeURI(cookie_value);
		}					
	}
	return false;
}
var docObj = getTopWin();
//var existsDialog = false;
//生成背景
function create_bg(){
		var bg = docObj.document.createElement("div");
		bg.id = "dark_bg";
		with(bg.style){
			position = "absolute";
			top = "0px";
			left = "0px";
			width = docObj.document.documentElement.scrollWidth + "px";	
			height = Math.max(docObj.document.documentElement.scrollHeight, docObj.document.documentElement.clientHeight) + "px";
		}
		docObj.document.body.appendChild(bg);
		
		var overlay = docObj.document.createElement("div");
		overlay.id = "overlay";
		docObj.document.body.appendChild(overlay);
}
//生成对话框
function show(url,title){
		title = title || 'title';
		create_bg();
		var visual = docObj.document.createElement("div");
		visual.id = "new_dialogue";
		var html = "";
		html = '<h2><span id="dialogue_close" onclick="show_close()">x</span>' + title + '</h2>';
		html += '<div id="dialogue_content"><iframe src="' + url + '" id="test_iframe" name="test_iframe" scrolling="yes" frameborder="0"></iframe></div>';
		visual.innerHTML = html;
		docObj.document.body.appendChild(visual);
		//docObj.existsDialog = true;
		/*docObj.document.onkeydown = function(e) {
			if(docObj.existsDialog) {
				e = docObj.event || e;
				if(27 == e.keyCode) { show_close(); }
			}
		}
		document.onkeydown = function(e) {
			if(docObj.existsDialog) {
				e = e || event;
				if(27 == e.keyCode) { show_close(); }
			}
		}*/
}
//去掉刚才建立的节点
function show_close(){
		var new_dialogue = docObj.document.getElementById("new_dialogue");
		var dark_bg = docObj.document.getElementById("dark_bg");
		var overlay = docObj.document.getElementById("overlay");
		new_dialogue.parentNode.removeChild(new_dialogue);
		overlay.parentNode.removeChild(overlay);
		dark_bg.parentNode.removeChild(dark_bg);
		//docObj.existsDialog = false;
}