<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2009-06-23 分页 $
	@version  $Id: Pagenation.class.php 2.8 2011-03-19
	          ver 1.4(增加了搜索分页功能)
	          ver 1.5(增加数字页码大小开启控制省略功能)
	          ver 1.9(把sql语句写在类外,增加对长文章分页功能)
	          ver 2.3(把分页链接接的URL写成参数,利于静态化)
	          ver 2.8(mysql独有的limit和php的array_slice两种不同的分页)
			  ver 2.9(增加每页大小为零不分页功能)
*/
defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Pagenation {
 	
	public $row_total;                            //总记录数,列表分页时要在数据列表后单独声明
	public $page_total;                           //总页数
	public $page_size;                            //一页有几条记录
	public $page_current;                         //当前页数
	public $page_para;                            //浏览器分页参数
	public $is_page_number         = 1;           //是否开启数字页码(对内容分页无效)
	public $page_number_show       = 6;           //显示多少数字页(完整页时)
	public $page_number_omitted    = 1;           //是否开启数字页码省略显示(对内容分页无效)
	public $data_arr;                             //内容数组
	public $url;                                  //分页链接地址,除分页page以外的地址,如:content.php或content.php?a=1
	public $content_page_split     = '[page]';    //长文章内容分页符,私有
	public $anchor = '';                          //锚记
	
	/*
	 * 列表分页初始化
	 * $page_size                一页记录数
	 * $row_total                记录总数
	 * $other_para               其他参数,如cms/23/,原始为cms=23
	 */
	public function list_init($page_size = 20, $other_para = ''){
		$this->page_size = $page_size;
		if(empty($page_size)) {  //每页大小零页时不分页
			$this->row_total = 0;
			return FALSE; 
		}
		//替换掉&page=2或page=2或?page=2之类的
		$this->other_para = $other_para;
		$this->page_para = 'p'; //分页参数
	}
	
	//返回总记录数
	public function get_row_total(){
		return $this->row_total;
	}
	
	//返回总页数
	public function get_page_total(){
		return $this->page_total = ceil($this->get_row_total()/$this->page_size);
	}
	
	//返回当前页
	public function get_page_current(){
		if(!isset($_GET["$this->page_para"]) || (int)$_GET["$this->page_para"] <= 0){
			return $this->page_current = 1;
		}elseif((int)$_GET["$this->page_para"] > $this->get_page_total()){
			return $this->page_current = $this->get_page_total();
		}else{
			return $this->page_current = (int)$_GET["$this->page_para"];
		}
	}
	
	//返回limit参数
	public function sql_limit(){
		if(empty($this->page_size)) { return ''; }
		$page = 0;
		$page = ($this->get_page_current() - 1) * $this->page_size;
		if($page <= 0) $page = 0; //防止出现负数
		$page .= ',';
		return $limit = ' LIMIT ' . $page . ' ' . $this->page_size;
	}
	
	//页码菜单,文字页信息
	public function menu_page(){
		//总记录数比一页有记录数多,才显示分页
		if($this->get_row_total()>$this->page_size){
			//前一页的页码
			$prevpage = ($this->get_page_current() - 1) <= 0 ? 1 : $this->get_page_current() - 1;
			//后一页的页码
			$nextpage = ($this->get_page_current() + 1) > $this->get_page_total() ? $this->get_page_total() : $this->get_page_current() + 1;
			//当前页之前的所有页码
			if($this->is_page_number) {
				$prevpages = ($this->get_page_current() - $this->page_number_show / 2) <= 0 ? 1 : $this->get_page_current() - $this->page_number_show / 2;
				//当前页之后的所有页码
				$nextpages = ($this->get_page_current() + $this->page_number_show / 2 - 1) > $this->get_page_total() ? $this->get_page_total() : $this->get_page_current() + $this->page_number_show / 2 - 1;
			}
			
			//判断分页链接地址是否带参数,其要与page=1之类的分页参数链接
			if(!empty($this->other_para)) { dir_path($this->other_para); }
			$url_str = preg_replace("/\/" . $this->page_para . "\/[^\/]*\//", '/', repair_url() . $this->other_para) . $this->page_para;
			
			$page_nav = '';
			//-------------------------页码导航开始---------------------//
			if(!$this->is_page_number || !$this->page_number_omitted) { //数字页码和省略都开启才不会显示这条
				if($this->get_page_current() == 1) {
					$page_nav = '<span class="page_disabled page_text page_first">First</span>';
				} else {
					$page_nav = '<span class="page_link page_text page_first"><a href="' . $url_str . '/1/' . $this->anchor . '">First</a></span>'; //当前页不为第一页时,才显示首页和上一页的链接状态
				} 
			}
			if($this->get_page_current() == 1) { 
				$page_nav .= '<span class="page_disabled page_text page_prev">Previous</span>';
			} else {
				$page_nav .= '<span class="page_link page_text page_prev"><a href="' . $url_str . '/' . $prevpage . '/' . $this->anchor . '">Previous</a></span>';
			}
			
			//数字页码
			if($this->is_page_number) {
				if($this->page_number_omitted) { //前省略
					if($prevpages > 2) { //显示的最前页大于等于3时，和第一页不是相邻页
						$page_nav .= '<span class="page_num"><a href="' . $url_str . '/1/' . $this->anchor . '">1</a></span><span class="page_ommit">...</span>';
					} elseif($prevpages == 2) { //说明显示的最前页和第一页中没有页数，即最前页和第一页是相邻页
						$page_nav .= '<span class="page_num"><a href="' . $url_str . '/1/' . $this->anchor . '">1</a></span>';
					}
				}
				for($i=$prevpages;$i<=$this->get_page_current()-1;$i++){
					$page_nav .= '<span class="page_num"><a href="' . $url_str . '/' . $i . '/' . $this->anchor . '">' . $i . '</a></span>';
				}
				$page_nav .= '<strong class="page_current">' . $this->get_page_current() . '</strong>';
				for($i=$this->get_page_current()+1;$i<=$nextpages;$i++){
					$page_nav .= '<span class="page_num"><a href="' . $url_str . '/' . $i . '/' . $this->anchor . '">' . $i . '</a></span>';
				}
				if($this->page_number_omitted) { //后省略
					if($nextpages == ($this->get_page_total() - 1)) { //最后显示页和最后一页是相邻页
						$page_nav .= '<span class="page_num"><a href="' . $url_str . '/' . $this->get_page_total() . '/' . $this->anchor . '">' . $this->get_page_total() . '</a></span>';
					} elseif($nextpages < ($this->get_page_total() - 1)) { //最后显示页和最后一页不是相邻页
						$page_nav .= '<span class="page_ommit">...</span><span class="page_num"><a href="' . $url_str . '/' . $this->get_page_total() . '/' . $this->anchor . '">' . $this->get_page_total() . '</a></span>';
					}
				}
			}
			
			if($this->get_page_current() == $this->get_page_total()) {
				$page_nav .= '<span class="page_disabled page_text page_next">Next</span>';
			} else {
				$page_nav .= '<span class="page_link page_text page_next"><a href="' . $url_str . '/' . $nextpage . '/'  . $this->anchor . '">Next</a></span>';
			}
			if(!$this->is_page_number || !$this->page_number_omitted) { 
				if($this->get_page_current() == $this->get_page_total()) {
					$page_nav .= '<span class="page_disabled page_text page_last">Last</span>';
				} else {
					$page_nav .= '<span class="page_link page_text page_last"><a href="' .$url_str . '/' . $this->get_page_total() . '/' . $this->anchor . '">Last</a></span>';
				}
			}
			return  $page_nav;
			//--------------------------页码导航结束--------------------//
		}
	}
	
	//一般页码显示
	public function page_normal() {
		$total = $this->get_row_total();
		if(empty($total)) return FALSE;
		$language = $GLOBALS['QXDREAM']['language'];
		/*
		'page_all' => '共',
		'page_record_display' => '条记录  每页显示',
		'page_current' => '条  当前为第',
		'page_current_end' => '页/共',
		'page' => '页',
		*/
		return $page_style = $language['page_all'] . $this->get_row_total() . $language['page_record_display'] . $this->page_size . $language['page_current'] . $this->get_page_current() . $language['page_current_end'] . $this->get_page_total() . $language['page'] . ' ' . $this->menu_page();
	}
	
	//不用mysql特有的limit显示分页内容,返回分页显示内容列表
	public function page_list($data_arr) {
		$page = 0;
		$this->row_total = count($data_arr);
		$page = ($this->get_page_current() - 1) * $this->page_size;
		return array_slice($data_arr, $page, $this->page_size);
	}
	
	//显示当前页的内容,每页大小是1
	//相应的数组下标对应的元素和page值相关联,如第一页,显示第一个数组,第二页显示第二个数组
	public function content($content, $other_para, $page_para='page') {
		$this->page_size = 1;
		$this->other_para = $other_para;
		$this->data_arr = $this->content_deal($content);
		$this->page_para = $page_para;
		return $this->data_arr[$this->get_page_current() - 1];//数组第一个元素键值是0,所以减-1
	}
	
	//对长文章内容进行分页处理
	//私有方法
	private function content_deal($content) {
		$data = explode('[page]', $content);
		 foreach($data as $k => $v) {
			if(empty($v)) unset($data[$k]);
		 }
		 //重建索引
		 $data = array_values($data);
		 return $data;
	}
	
	//长文章内容页码显示
	public function page_content() {
		$this->is_page_number       = 1;
		$this->page_number_omitted  = 1;
		return $this->menu_page();
	}
		
 }
?>