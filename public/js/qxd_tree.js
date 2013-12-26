// �� ------------------------------------------
// �� JS�������޼��˵� v1.1
// �� ̤ѩ����
// �� http://www.qxhtml.cn
// �� ����:2011-02-13
// �� ����:2011-02-20
// �� ------------------------------------------

var qxdTree = new classCreate();

qxdTree.prototype = {
	init: function(treeRoot, treeData, obj, options) {
		var o = extend(this.defaults, options || {});
		if(!o.updateParent) {
			this.treeRoot = getId(treeRoot); //��������
		}
		this.treeData = treeData; //��������
		this._obj = obj;
		this.saveHasDrop = o.saveHasDrop;
		this.saveNoDrop = o.saveNoDrop;
		this.target = o.target;
		this.isOpen = o.isOpen;
		
		this._setCookieExpire();
		if(this.saveHasDrop) {
			this._nodeHasDropLast = getCookie(this._obj + '_qxd_has_drop'); //���Ӳ˵������״̬
			this._dropExists = getCookie(this._obj + '_qxd_drop_exists'); //���Ӳ˵��Ľڵ��Ƿ��¼��cookie
		} else {
			this._nodeHasDropLast = false;
			this._dropExists = false;
		}
		this._nodeNoDropLast = this.saveNoDrop ? getCookie(this._obj + '_qxd_no_drop') : false; //���Ӳ˵������״̬
		
		if(!o.updateParent) {
		this.output(this.addNode(o.rootId));
		} else {
		this.treeStr =this.addNode(o.rootId);
		}
		//���Ӳ˵��Ľڵ��Ƿ��¼��cookie����ʱ,����������Ϻ�Ӧ�Ѽ�¼��cookie����Ϊ1
		if(this.saveHasDrop) { this._cookieDropExist(); }
	},
	defaults: {
		saveHasDrop:   true, //��¼���Ӳ˵��Ľڵ�״̬
		saveNoDrop:    false, //��¼���Ӳ˵��Ľڵ�״̬
		target:        '_self', //�˵���Ŀ��
		isOpen:        false, //Ĭ�ϲ˵��Ƿ��
		updateParent:  false,
		rootId:        0 //���ڵ�ID
	},
	//��ȡ�ӽڵ�
	getChildNode: function(nodeId) {
		var arr = new Array();
		for(var i = 0; i < this.treeData.length; i++) {
			if(this.treeData[i].pid == nodeId) { arr[arr.length] = this.treeData[i]; }
		}
		return arr;
	},
	//��ȡ���ڵ�
	getParentNode: function(nodeId) {
		for(var i = 0; i < this.treeData.length; i++) {
			if(this.treeData[i].id == nodeId.pid) { return this.treeData[i]; }
		}
	},
	//�������ͽṹ
	addNode: function(pNode) {
		var str = ''
		var childNodeArr = this.getChildNode(pNode);
		for(var i = 0; i < childNodeArr.length; i++) {
			this.checkNode(childNodeArr[i]);
			str += this.nodeAttr(childNodeArr[i]);
		}
		return str;
	},
	//���ɽڵ������
	nodeAttr: function(node) {
		var linkClassName = '';
		var divClassName = 'qxd_tree_node';
		var url = '';
		var target = '';
		var clickEvent = '';
		var str = '';
		if(node.hasChild) { //���Ӳ˵�
			linkClassName = 'qxd_has_drop';
			linkClassName += node.isOpen ? ' qxd_drop_show' : ' qxd_drop_hidden';
			if(node.isLast) { 
				linkClassName += node.isOpen ? ' qxd_unfold_last_menu' : ' qxd_fold_last_menu';
			}
			url = 'javascript: void(0)';
			target = '';
			clickEvent = ' onclick="' + this._obj + '.setHasDrop(' + node.id + ')"';
			if(1 != this._dropExists) { this.updateHasDropCookie(node.id, node.isOpen); }
		} else { //���Ӳ˵�
			linkClassName = 'qxd_no_drop';
			if(node.isLast) { linkClassName += ' qxd_last_menu'; }
			if(this._nodeNoDropLast && node.id == this._nodeNoDropLast) { linkClassName += ' qxd_cur'; }
			url = node.url;
			target = node.target;
			clickEvent = ' onclick="' + this._obj + '.setNoDrop(' + node.id + ');"';
		}
		
		//�������һ����Ĳ˵�������
		var parentNode = '';
		if(0 != node.pid && (parentNode = this.getParentNode(node)) && !parentNode.isOpen) { divClassName += ' collapse'; }
		
		//�����˵���ͬ���ĵ�һ���˵����а�������ʼDIV
		if(0 == node.pid) { str += '<div class="' + divClassName + '">'; }
		if(0 != node.pid && node.isFirst) { str = '<div class="' + divClassName + ('' !== parentNode ? '" id="' + this._obj  + '_qxd_dropc_' + parentNode.id : '') + '">'; }
		//��ȥÿ�����һ��˵�չ��ʱ�ı�������,���ܳ��ֽڵ��ǵ�һ��Ҳ�����һ��,ע��˳��
		if(node.isLast && node.hasChild) { str += '<div class="qxd_tn_last">'; }
		str += '<a href="' + url + '" id="' + this._obj  + '_qxd_menu_' + node.id + '" class="' + linkClassName + '" target="' + target + '"' + clickEvent + '><span>' + node.menuName + '</span></a>';
		str += this.addNode(node.id);
		if(node.isLast && node.hasChild ) { str += '</div>'; }
		//�����˵���ͬ�������һ���˵����а���������DIV
		if(0 == node.pid || node.isLast) { str += '</div>'; }
		
		return str;
	},
	//ȷ�Ͻڵ�����target��isOpen��Ա
	//ȷ�Ͻڵ��Ƿ����ӽڵ�
	//ȷ�Ͻڵ��Ƿ���ͬ�������һ��
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
	//�������Ӳ˵��Ĳ˵�
	setHasDrop: function(index) {
		var searchMenuShow = /(^|\s)qxd_drop_show(\s|$)/;
		var searchMenuHidden = /(^|\s)qxd_drop_hidden(\s|$)/;
		var searchUnfoldLastMenu = /(^|\s)qxd_unfold_last_menu(\s|$)/;
		var searchFoldLastMenu = /(^|\s)qxd_fold_last_menu(\s|$)/;
		var searchDrop = /(^|\s)collapse(\s|$)/;
		var menu = getId(this._obj  + '_qxd_menu_' + index);
		var drop = getId(this._obj  + '_qxd_dropc_' + index);
		var isOpen;
		if(menu.className.match(searchMenuShow)) { //ִ������
			menu.className = menu.className.replace(searchMenuShow, ' qxd_drop_hidden ');
			menu.className = menu.className.replace(searchUnfoldLastMenu, ' qxd_fold_last_menu ');
			drop.className += ' ' + 'collapse';
			isOpen = false;
		} else if(menu.className.match(searchMenuHidden)) { //ִ��չ��
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
	//����cookie����ʱ��
	_setCookieExpire: function() {
		//����cookie����ʱ��
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
	//����cookie״̬
	updateHasDropCookie: function(index, isOpen) {
		var indexOpenStr = ''; //Ҫ������cookie������
		if(isOpen) {
			if(this._nodeHasDropLast) {
				var indexOpenArr = this._nodeHasDropLast.split(',');
				var indexExists = false;
				for(var i = 0; i < indexOpenArr.length; i++) { //ѭ������ǰ����֮�������
					//��ǰ��������cookie��ʱ
					if(index == indexOpenArr[i]) { indexExists = true; continue;}
					if('' != indexOpenArr[i]) { indexOpenStr += indexOpenArr[i] + ','; }
				}
				if(!indexExists) { indexOpenStr += index + ','; } //��ǰ����������,�ͱ���
			} else { //cookieΪ��ʱ,ֱ�ӱ���
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
	//�Ƿ���չ��״̬
	loadHasDropStatus: function(node) {
		//���Ӳ˵��Ľڵ�û��¼��cookie����û����cookie������
		if(1 != this._dropExists || !this.saveHasDrop) { return; }
		if(this._nodeHasDropLast) {
			var indexOpenArr = this._nodeHasDropLast.split(',');
			for(var i = 0; i < indexOpenArr.length; i++) {
				if(node.id == indexOpenArr[i]) { node.isOpen = true; return; }
			}
		}
		node.isOpen = false;
	},
	//�������Ӳ˵��Ĳ˵�
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
	//���
	output: function(str) {
		this.treeRoot.innerHTML = str;
	}
}