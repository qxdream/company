// ★ ------------------------------------------
// ↓ JS树形无限级菜单 v1.1
// ↓ 踏雪残情
// ↓ http://www.qxhtml.cn
// ↓ 建立:2011-02-13
// ↓ 更新:2011-02-20
// ★ ------------------------------------------

var qxdTree = new classCreate();

qxdTree.prototype = {
	init: function(treeRoot, treeData, obj, options) {
		var o = extend(this.defaults, options || {});
		if(!o.updateParent) {
			this.treeRoot = getId(treeRoot); //树形容器
		}
		this.treeData = treeData; //树形数据
		this._obj = obj;
		this.saveHasDrop = o.saveHasDrop;
		this.saveNoDrop = o.saveNoDrop;
		this.target = o.target;
		this.isOpen = o.isOpen;
		
		this._setCookieExpire();
		if(this.saveHasDrop) {
			this._nodeHasDropLast = getCookie(this._obj + '_qxd_has_drop'); //有子菜单的最后状态
			this._dropExists = getCookie(this._obj + '_qxd_drop_exists'); //有子菜单的节点是否记录过cookie
		} else {
			this._nodeHasDropLast = false;
			this._dropExists = false;
		}
		this._nodeNoDropLast = this.saveNoDrop ? getCookie(this._obj + '_qxd_no_drop') : false; //无子菜单的最后状态
		
		if(!o.updateParent) {
		this.output(this.addNode(o.rootId));
		} else {
		this.treeStr =this.addNode(o.rootId);
		}
		//有子菜单的节点是否记录过cookie开启时,树形生成完毕后应把记录过cookie设置为1
		if(this.saveHasDrop) { this._cookieDropExist(); }
	},
	defaults: {
		saveHasDrop:   true, //记录有子菜单的节点状态
		saveNoDrop:    false, //记录无子菜单的节点状态
		target:        '_self', //菜单打开目标
		isOpen:        false, //默认菜单是否打开
		updateParent:  false,
		rootId:        0 //根节点ID
	},
	//获取子节点
	getChildNode: function(nodeId) {
		var arr = new Array();
		for(var i = 0; i < this.treeData.length; i++) {
			if(this.treeData[i].pid == nodeId) { arr[arr.length] = this.treeData[i]; }
		}
		return arr;
	},
	//获取父节点
	getParentNode: function(nodeId) {
		for(var i = 0; i < this.treeData.length; i++) {
			if(this.treeData[i].id == nodeId.pid) { return this.treeData[i]; }
		}
	},
	//生成树型结构
	addNode: function(pNode) {
		var str = ''
		var childNodeArr = this.getChildNode(pNode);
		for(var i = 0; i < childNodeArr.length; i++) {
			this.checkNode(childNodeArr[i]);
			str += this.nodeAttr(childNodeArr[i]);
		}
		return str;
	},
	//生成节点的属性
	nodeAttr: function(node) {
		var linkClassName = '';
		var divClassName = 'qxd_tree_node';
		var url = '';
		var target = '';
		var clickEvent = '';
		var str = '';
		if(node.hasChild) { //有子菜单
			linkClassName = 'qxd_has_drop';
			linkClassName += node.isOpen ? ' qxd_drop_show' : ' qxd_drop_hidden';
			if(node.isLast) { 
				linkClassName += node.isOpen ? ' qxd_unfold_last_menu' : ' qxd_fold_last_menu';
			}
			url = 'javascript: void(0)';
			target = '';
			clickEvent = ' onclick="' + this._obj + '.setHasDrop(' + node.id + ')"';
			if(1 != this._dropExists) { this.updateHasDropCookie(node.id, node.isOpen); }
		} else { //无子菜单
			linkClassName = 'qxd_no_drop';
			if(node.isLast) { linkClassName += ' qxd_last_menu'; }
			if(this._nodeNoDropLast && node.id == this._nodeNoDropLast) { linkClassName += ' qxd_cur'; }
			url = node.url;
			target = node.target;
			clickEvent = ' onclick="' + this._obj + '.setNoDrop(' + node.id + ');"';
		}
		
		//处理除第一级外的菜单的类名
		var parentNode = '';
		if(0 != node.pid && (parentNode = this.getParentNode(node)) && !parentNode.isOpen) { divClassName += ' collapse'; }
		
		//顶级菜单或同级的第一个菜单才有包容器开始DIV
		if(0 == node.pid) { str += '<div class="' + divClassName + '">'; }
		if(0 != node.pid && node.isFirst) { str = '<div class="' + divClassName + ('' !== parentNode ? '" id="' + this._obj  + '_qxd_dropc_' + parentNode.id : '') + '">'; }
		//除去每级最后一块菜单展开时的背景竖线,可能出现节点是第一个也是最后一个,注意顺序
		if(node.isLast && node.hasChild) { str += '<div class="qxd_tn_last">'; }
		str += '<a href="' + url + '" id="' + this._obj  + '_qxd_menu_' + node.id + '" class="' + linkClassName + '" target="' + target + '"' + clickEvent + '><span>' + node.menuName + '</span></a>';
		str += this.addNode(node.id);
		if(node.isLast && node.hasChild ) { str += '</div>'; }
		//顶级菜单或同级的最后一个菜单才有包容器结束DIV
		if(0 == node.pid || node.isLast) { str += '</div>'; }
		
		return str;
	},
	//确认节点有无target与isOpen成员
	//确认节点是否有子节点
	//确认节点是否是同级的最后一个
	checkNode: function(node) {
		var tempNodeId;
		var isFirstCount = 0;
		for(var i = 0; i < this.treeData.length; i++) {
			if(undefined === node.target) { node.target = this.target; }
			this.loadHasDropStatus(node);
			if(undefined === node.isOpen) { node.isOpen = this.isOpen; }
			if(this.treeData[i].pid == node.id) { node.hasChild = true; }
			if(this.treeData[i].pid == node.pid) { 
				tempNodeId = this.treeData[i].id;
				if(1 == ++isFirstCount) {
					node.isFirst = this.treeData[i].id == node.id ? true : false;
				}
			}
		}
		if(undefined === node.hasChild) { node.hasChild = false; }
		node.isLast = tempNodeId == node.id ? true : false;
	},
	//处理有子菜单的菜单
	setHasDrop: function(index) {
		var searchMenuShow = /(^|\s)qxd_drop_show(\s|$)/;
		var searchMenuHidden = /(^|\s)qxd_drop_hidden(\s|$)/;
		var searchUnfoldLastMenu = /(^|\s)qxd_unfold_last_menu(\s|$)/;
		var searchFoldLastMenu = /(^|\s)qxd_fold_last_menu(\s|$)/;
		var searchDrop = /(^|\s)collapse(\s|$)/;
		var menu = getId(this._obj  + '_qxd_menu_' + index);
		var drop = getId(this._obj  + '_qxd_dropc_' + index);
		var isOpen;
		if(menu.className.match(searchMenuShow)) { //执行收起
			menu.className = menu.className.replace(searchMenuShow, ' qxd_drop_hidden ');
			menu.className = menu.className.replace(searchUnfoldLastMenu, ' qxd_fold_last_menu ');
			drop.className += ' ' + 'collapse';
			isOpen = false;
		} else if(menu.className.match(searchMenuHidden)) { //执行展开
			menu.className = menu.className.replace(searchMenuHidden, ' qxd_drop_show ');
			menu.className = menu.className.replace(searchFoldLastMenu, ' qxd_unfold_last_menu ');
			drop.className = drop.className.replace(searchDrop, ' ');
			isOpen = true;
		}
		
		if(this.saveHasDrop) { 
			this.updateHasDropCookie(index, isOpen);
			this._cookieDropExist();
		}
	},
	//设置cookie到期时间
	_setCookieExpire: function() {
		//设置cookie保存时间
		var date = new Date();
		date.setFullYear(date.getFullYear() + 1);
		this._date = date.toUTCString();
	},
	_cookieDropExist: function() {
		if(1 != this._dropExists) {
			setCookie(this._obj  + '_qxd_drop_exists', 1, this._date);
			this._dropExists = 1;
		}
	},
	//更新cookie状态
	updateHasDropCookie: function(index, isOpen) {
		var indexOpenStr = ''; //要保存入cookie的索引
		if(isOpen) {
			if(this._nodeHasDropLast) {
				var indexOpenArr = this._nodeHasDropLast.split(',');
				var indexExists = false;
				for(var i = 0; i < indexOpenArr.length; i++) { //循环处理当前索引之外的索引
					//当前索引存在cookie里时
					if(index == indexOpenArr[i]) { indexExists = true; continue;}
					if('' != indexOpenArr[i]) { indexOpenStr += indexOpenArr[i] + ','; }
				}
				if(!indexExists) { indexOpenStr += index + ','; } //当前索引不存在,就保存
			} else { //cookie为空时,直接保存
				indexOpenStr += index + ',';
			}
		} else {
			if(this._nodeHasDropLast) {
				var indexOpenArr = this._nodeHasDropLast.split(',');
				for(var i = 0; i < indexOpenArr.length; i++) {
					if(index != indexOpenArr[i] && '' != indexOpenArr[i]) { indexOpenStr += indexOpenArr[i] + ','; }
				}
			}
		}
		setCookie(this._obj  + '_qxd_has_drop', indexOpenStr, this._date);
		this._nodeHasDropLast = indexOpenStr;
	},
	//是否是展开状态
	loadHasDropStatus: function(node) {
		//有子菜单的节点没记录过cookie或其没开启cookie均返回
		if(1 != this._dropExists || !this.saveHasDrop) { return; }
		if(this._nodeHasDropLast) {
			var indexOpenArr = this._nodeHasDropLast.split(',');
			for(var i = 0; i < indexOpenArr.length; i++) {
				if(node.id == indexOpenArr[i]) { node.isOpen = true; return; }
			}
		}
		node.isOpen = false;
	},
	//处理无子菜单的菜单
	setNoDrop: function(index) {
		if(this._nodeNoDropLast) {
			getId(this._obj  + '_qxd_menu_' + this._nodeNoDropLast).className = getId(this._obj  + '_qxd_menu_' + this._nodeNoDropLast).className.replace(/(^|\s)qxd_cur(\s|$)/, ' ');
		}
		getId(this._obj  + '_qxd_menu_' + index).className += ' ' + 'qxd_cur';
		var cookieDate = '';
		if(this.saveNoDrop) {
			setCookie(this._obj  + '_qxd_no_drop', index, this._date);
		} else {
			setCookie(this._obj  + '_qxd_no_drop', index);
		}
		this._nodeNoDropLast = index;
	},
	//输出
	output: function(str) {
		this.treeRoot.innerHTML = str;
	}
}