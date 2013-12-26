<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2009-06-23 ��ҳ $
	@version  $Id: Pagenation.class.php 2.8 2011-03-19
	          ver 1.4(������������ҳ����)
	          ver 1.5(��������ҳ���С��������ʡ�Թ���)
	          ver 1.9(��sql���д������,���ӶԳ����·�ҳ����)
	          ver 2.3(�ѷ�ҳ���ӽӵ�URLд�ɲ���,���ھ�̬��)
	          ver 2.8(mysql���е�limit��php��array_slice���ֲ�ͬ�ķ�ҳ)
			  ver 2.9(����ÿҳ��СΪ�㲻��ҳ����)
*/
defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Pagenation {
 	
	public $row_total;                            //�ܼ�¼��,�б��ҳʱҪ�������б�󵥶�����
	public $page_total;                           //��ҳ��
	public $page_size;                            //һҳ�м�����¼
	public $page_current;                         //��ǰҳ��
	public $page_para;                            //�������ҳ����
	public $is_page_number         = 1;           //�Ƿ�������ҳ��(�����ݷ�ҳ��Ч)
	public $page_number_show       = 6;           //��ʾ��������ҳ(����ҳʱ)
	public $page_number_omitted    = 1;           //�Ƿ�������ҳ��ʡ����ʾ(�����ݷ�ҳ��Ч)
	public $data_arr;                             //��������
	public $url;                                  //��ҳ���ӵ�ַ,����ҳpage����ĵ�ַ,��:content.php��content.php?a=1
	public $content_page_split     = '[page]';    //���������ݷ�ҳ��,˽��
	public $anchor = '';                          //ê��
	
	/*
	 * �б��ҳ��ʼ��
	 * $page_size                һҳ��¼��
	 * $row_total                ��¼����
	 * $other_para               ��������,��cms/23/,ԭʼΪcms=23
	 */
	public function list_init($page_size = 20, $other_para = ''){
		$this->page_size = $page_size;
		if(empty($page_size)) {  //ÿҳ��С��ҳʱ����ҳ
			$this->row_total = 0;
			return FALSE; 
		}
		//�滻��&page=2��page=2��?page=2֮���
		$this->other_para = $other_para;
		$this->page_para = 'p'; //��ҳ����
	}
	
	//�����ܼ�¼��
	public function get_row_total(){
		return $this->row_total;
	}
	
	//������ҳ��
	public function get_page_total(){
		return $this->page_total = ceil($this->get_row_total()/$this->page_size);
	}
	
	//���ص�ǰҳ
	public function get_page_current(){
		if(!isset($_GET["$this->page_para"]) || (int)$_GET["$this->page_para"] <= 0){
			return $this->page_current = 1;
		}elseif((int)$_GET["$this->page_para"] > $this->get_page_total()){
			return $this->page_current = $this->get_page_total();
		}else{
			return $this->page_current = (int)$_GET["$this->page_para"];
		}
	}
	
	//����limit����
	public function sql_limit(){
		if(empty($this->page_size)) { return ''; }
		$page = 0;
		$page = ($this->get_page_current() - 1) * $this->page_size;
		if($page <= 0) $page = 0; //��ֹ���ָ���
		$page .= ',';
		return $limit = ' LIMIT ' . $page . ' ' . $this->page_size;
	}
	
	//ҳ��˵�,����ҳ��Ϣ
	public function menu_page(){
		//�ܼ�¼����һҳ�м�¼����,����ʾ��ҳ
		if($this->get_row_total()>$this->page_size){
			//ǰһҳ��ҳ��
			$prevpage = ($this->get_page_current() - 1) <= 0 ? 1 : $this->get_page_current() - 1;
			//��һҳ��ҳ��
			$nextpage = ($this->get_page_current() + 1) > $this->get_page_total() ? $this->get_page_total() : $this->get_page_current() + 1;
			//��ǰҳ֮ǰ������ҳ��
			if($this->is_page_number) {
				$prevpages = ($this->get_page_current() - $this->page_number_show / 2) <= 0 ? 1 : $this->get_page_current() - $this->page_number_show / 2;
				//��ǰҳ֮�������ҳ��
				$nextpages = ($this->get_page_current() + $this->page_number_show / 2 - 1) > $this->get_page_total() ? $this->get_page_total() : $this->get_page_current() + $this->page_number_show / 2 - 1;
			}
			
			//�жϷ�ҳ���ӵ�ַ�Ƿ������,��Ҫ��page=1֮��ķ�ҳ��������
			if(!empty($this->other_para)) { dir_path($this->other_para); }
			$url_str = preg_replace("/\/" . $this->page_para . "\/[^\/]*\//", '/', repair_url() . $this->other_para) . $this->page_para;
			
			$page_nav = '';
			//-------------------------ҳ�뵼����ʼ---------------------//
			if(!$this->is_page_number || !$this->page_number_omitted) { //����ҳ���ʡ�Զ������Ų�����ʾ����
				if($this->get_page_current() == 1) {
					$page_nav = '<span class="page_disabled page_text page_first">First</span>';
				} else {
					$page_nav = '<span class="page_link page_text page_first"><a href="' . $url_str . '/1/' . $this->anchor . '">First</a></span>'; //��ǰҳ��Ϊ��һҳʱ,����ʾ��ҳ����һҳ������״̬
				} 
			}
			if($this->get_page_current() == 1) { 
				$page_nav .= '<span class="page_disabled page_text page_prev">Previous</span>';
			} else {
				$page_nav .= '<span class="page_link page_text page_prev"><a href="' . $url_str . '/' . $prevpage . '/' . $this->anchor . '">Previous</a></span>';
			}
			
			//����ҳ��
			if($this->is_page_number) {
				if($this->page_number_omitted) { //ǰʡ��
					if($prevpages > 2) { //��ʾ����ǰҳ���ڵ���3ʱ���͵�һҳ��������ҳ
						$page_nav .= '<span class="page_num"><a href="' . $url_str . '/1/' . $this->anchor . '">1</a></span><span class="page_ommit">...</span>';
					} elseif($prevpages == 2) { //˵����ʾ����ǰҳ�͵�һҳ��û��ҳ��������ǰҳ�͵�һҳ������ҳ
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
				if($this->page_number_omitted) { //��ʡ��
					if($nextpages == ($this->get_page_total() - 1)) { //�����ʾҳ�����һҳ������ҳ
						$page_nav .= '<span class="page_num"><a href="' . $url_str . '/' . $this->get_page_total() . '/' . $this->anchor . '">' . $this->get_page_total() . '</a></span>';
					} elseif($nextpages < ($this->get_page_total() - 1)) { //�����ʾҳ�����һҳ��������ҳ
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
			//--------------------------ҳ�뵼������--------------------//
		}
	}
	
	//һ��ҳ����ʾ
	public function page_normal() {
		$total = $this->get_row_total();
		if(empty($total)) return FALSE;
		$language = $GLOBALS['QXDREAM']['language'];
		/*
		'page_all' => '��',
		'page_record_display' => '����¼  ÿҳ��ʾ',
		'page_current' => '��  ��ǰΪ��',
		'page_current_end' => 'ҳ/��',
		'page' => 'ҳ',
		*/
		return $page_style = $language['page_all'] . $this->get_row_total() . $language['page_record_display'] . $this->page_size . $language['page_current'] . $this->get_page_current() . $language['page_current_end'] . $this->get_page_total() . $language['page'] . ' ' . $this->menu_page();
	}
	
	//����mysql���е�limit��ʾ��ҳ����,���ط�ҳ��ʾ�����б�
	public function page_list($data_arr) {
		$page = 0;
		$this->row_total = count($data_arr);
		$page = ($this->get_page_current() - 1) * $this->page_size;
		return array_slice($data_arr, $page, $this->page_size);
	}
	
	//��ʾ��ǰҳ������,ÿҳ��С��1
	//��Ӧ�������±��Ӧ��Ԫ�غ�pageֵ�����,���һҳ,��ʾ��һ������,�ڶ�ҳ��ʾ�ڶ�������
	public function content($content, $other_para, $page_para='page') {
		$this->page_size = 1;
		$this->other_para = $other_para;
		$this->data_arr = $this->content_deal($content);
		$this->page_para = $page_para;
		return $this->data_arr[$this->get_page_current() - 1];//�����һ��Ԫ�ؼ�ֵ��0,���Լ�-1
	}
	
	//�Գ��������ݽ��з�ҳ����
	//˽�з���
	private function content_deal($content) {
		$data = explode('[page]', $content);
		 foreach($data as $k => $v) {
			if(empty($v)) unset($data[$k]);
		 }
		 //�ؽ�����
		 $data = array_values($data);
		 return $data;
	}
	
	//����������ҳ����ʾ
	public function page_content() {
		$this->is_page_number       = 1;
		$this->page_number_omitted  = 1;
		return $this->menu_page();
	}
		
 }
?>