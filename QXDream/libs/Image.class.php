<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-27 ����ͼ�� $
	@version  $Id: Image.class.php 1.0 2010-11-27
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Image {
	public $enable_thumb       = 1; //�Ƿ�������ͼ
	public $thumb_quality      = 75; //����ͼ��Ʒ��,��Ϊjpg,jpeg��Ч
	private $thumbnails_dirname = 'thumbnails'; //����ͼ���Ŀ¼����
	public $detect_thumb       = 0; //��������ͼ���,�����Ļ������ظ���������ͼ
	
	//��������
	public function set($company_uid) {
		$this->company_uid = $company_uid;
	}
	
	//��������ͼ
	//@src_img string ԭʼͼƬ
	//@need_width int ��Ҫ���ɵĿ�
	//@need_height int ��Ҫ���ɵĸ�
	//@auto_cut int �Ƿ�ü�
	//@return string ��������ͼƬ��url��ַ
	public function thumb($src_img_file, $need_width = 160, $need_height = 120, $auto_cut = 0) {
		if(!$this->enable_thumb || !$this->check_image($src_img_file)) { return FALSE; }
		if(!dir_create(UPLOAD_ROOT . $this->thumbnails_dirname . '/' . $this->company_uid . '/')) return FALSE;
		$src_img_filename = basename($src_img_file);
		
		$image_data = array();
		$image_data = $this->get_imageinfo($src_img_file);
		if($need_width >= $image_data['width'] && $need_height >= $image_data['height']) { return FALSE; }
		
		//ȡ����С����,���ȡ��Ļ�,�ͻ����Ժ��ֵ����Ĭ�ϵ�
		$scale_width  = $need_width / $image_data['width'];
		$scale_height = $need_height / $image_data['height'];
		
		//�����ɿ�200         �����ɸ�50
		//ԭʼ��  400         ԭʼ��  80
		//�����  0.5         �߱���  0.625
		//200����400Ϊ0.5,0.5����80Ϊ40,û����
		//50����80ԼΪ0.625,0.625����400Ϊ250,����
		//���ü�ʱ���ܳ����ı���Ϊ��׼,�ü��Ļ��෴,Ҫ�������ı���Ϊ��׼
		$scale = min($scale_width, $scale_height);
		//$turecolor_widthΪ�հװ��,$thumb_widthΪ���յ�����ͼ��
		$truecolor_width  = $thumb_width  = intval($image_data['width'] * $scale);
		$truecolor_height = $thumb_height = intval($image_data['height'] * $scale);
		$src_x = 0; //imagecopyresampled��ˮƽʲôλ�ÿ�ʼ����
		$src_y = 0;
		
		//�ü�����,Ҳ���ǿհװ�Ĵ�С����$need_width,$need_height
		if($auto_cut && (($accord_to_h = $scale_width < $scale_height && $need_height <= $image_data['height']) || 
						 ($accord_to_w = $scale_width >= $scale_height && $need_width <= $image_data['width']))) {
			//���������,@need_height��С�ڵ���ԭʼͼ�ĸ�
			if($accord_to_h) {
				$thumb_width  = intval($image_data['width'] * $scale_height);
				$thumb_height = $need_height;
			}
			if(isset($accord_to_w)) { //����ߵ�����Ϊ��ʱ,�ұ߲������ж���,����������isset�ж�
				$thumb_width  = $need_width;
				$thumb_height = intval($image_data['height'] * $scale_width);
			}
			$truecolor_width  = $need_width;
			$truecolor_height = $need_height;
			$src_x = ($thumb_width - $truecolor_width) / 2; 
			$src_y = ($thumb_height - $truecolor_height) / 2;
			$output_image     =  UPLOAD_ROOT . $this->thumbnails_dirname . '/' . $this->company_uid . '/thumb_' . $need_width . '_' . $need_height . '_' . $src_img_filename;
			$output_image_url = UPLOAD_URL . $this->thumbnails_dirname . '/' . $this->company_uid . '/thumb_' . $need_width . '_' . $need_height . '_' . $src_img_filename;
		} else {
			$output_image     =  UPLOAD_ROOT . $this->thumbnails_dirname . '/' . $this->company_uid . '/thumb_' . $thumb_width . '_' . $thumb_height . '_' . $src_img_filename;
			$output_image_url = UPLOAD_URL . $this->thumbnails_dirname . '/' . $this->company_uid . '/thumb_' . $thumb_width . '_' . $thumb_height . '_' . $src_img_filename;
		}
		if($this->detect_thumb && is_file($output_image)) { return $output_image_url; }
		
		$append_image_func = ('jpg' == $image_data['extension'] ?  'jpeg' : $image_data['extension']);
		if('gif' == $append_image_func) { //ʹ���Ե�gifͼ����͸����
			$thumbnail = imagecreate($truecolor_width, $truecolor_height); //����һ��հװ�
			$background_color  =  imagecolorallocate($thumbnail,  0, 255, 0);//ָ��һ����ɫ  
			imagecolortransparent($thumbnail, $background_color);//����Ϊ͸��ɫ
		} else {
			$thumbnail = imagecreatetruecolor($truecolor_width, $truecolor_height); //����һ�鱳��Ϊ��ɫ���ͼ(�հװ�)
		}
		if('png' == $append_image_func) {
			//���ñ�����ڱ���PNGͼ��ʱ����������alphaͨ���ţ��뵥һ͸��ɫ�෴),���ϲ���ɫ
			imagealphablending($thumbnail, FALSE);
			//�ȱ�����alphablending��λ,GD 2.0.1����
			imagesavealpha($thumbnail, TRUE);
		}
		
		//����ͼƬ
		$get_image_func = 'imagecreatefrom' . $append_image_func;
		$src_img = $get_image_func($src_img_file);;
		$resampled_func = function_exists('imagecopyresampled') ? 'imagecopyresampled' : 'imagecopyresized';
		$resampled_func($thumbnail, $src_img, -1 * $src_x, -1 * $src_y, 0, 0, $thumb_width, $thumb_height, $image_data['width'], $image_data['height']);
		
		$output_image_func = 'image' . $append_image_func;
		//��������ͼ
		'jpeg' == $append_image_func ? $output_image_func($thumbnail, $output_image, $this->thumb_quality) : $output_image_func($thumbnail, $output_image);
		return $output_image_url;
	}
	
	//��ȡͼƬ��Ϣ
	//@img string ԭʼͼƬ
	//@return array ͼƬ��Ϣ
	public function get_imageinfo($image) {
		 $image_data = array();
		 $image_data = getimagesize($image);
		 $img_suffix = strtolower(substr(image_type_to_extension($image_data[2]), 1));
		 return array(
		 	'width'      => $image_data[0],
			'height'     => $image_data[1],
			'size'       => filesize($image),
			'extension'  => $img_suffix,
			'mime'       => $image_data['mime']
		 );
	}
	
	//ȷ���Ƿ���ִ��
	//@img string ԭʼͼƬ
	//@return boolen �ܷ�����,���ܷ��ؼ�
	public function check_image($img) {
		return extension_loaded('gd') && preg_match("/\.jpg|jpeg|gif|png/i", $img) && is_file($img);
	}
}
?>