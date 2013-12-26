<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-27 缩略图类 $
	@version  $Id: Image.class.php 1.0 2010-11-27
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Image {
	public $enable_thumb       = 1; //是否开启缩略图
	public $thumb_quality      = 75; //缩略图的品质,仅为jpg,jpeg有效
	private $thumbnails_dirname = 'thumbnails'; //缩略图存放目录名称
	public $detect_thumb       = 0; //开启缩略图检测,开启的话不会重复生成缩略图
	
	//设置数据
	public function set($company_uid) {
		$this->company_uid = $company_uid;
	}
	
	//生成缩略图
	//@src_img string 原始图片
	//@need_width int 需要缩成的宽
	//@need_height int 需要缩成的高
	//@auto_cut int 是否裁剪
	//@return string 返回缩略图片的url地址
	public function thumb($src_img_file, $need_width = 160, $need_height = 120, $auto_cut = 0) {
		if(!$this->enable_thumb || !$this->check_image($src_img_file)) { return FALSE; }
		if(!dir_create(UPLOAD_ROOT . $this->thumbnails_dirname . '/' . $this->company_uid . '/')) return FALSE;
		$src_img_filename = basename($src_img_file);
		
		$image_data = array();
		$image_data = $this->get_imageinfo($src_img_file);
		if($need_width >= $image_data['width'] && $need_height >= $image_data['height']) { return FALSE; }
		
		//取得缩小比例,如果取大的话,就会缩略后的值超出默认的
		$scale_width  = $need_width / $image_data['width'];
		$scale_height = $need_height / $image_data['height'];
		
		//需缩成宽200         需缩成高50
		//原始宽  400         原始高  80
		//宽比例  0.5         高比例  0.625
		//200除以400为0.5,0.5乘以80为40,没超出
		//50除以80约为0.625,0.625乘以400为250,超出
		//不裁剪时不能超出的比例为标准,裁剪的话相反,要按超出的比例为标准
		$scale = min($scale_width, $scale_height);
		//$turecolor_width为空白板宽,$thumb_width为最终的缩略图宽
		$truecolor_width  = $thumb_width  = intval($image_data['width'] * $scale);
		$truecolor_height = $thumb_height = intval($image_data['height'] * $scale);
		$src_x = 0; //imagecopyresampled从水平什么位置开始剪切
		$src_y = 0;
		
		//裁剪控制,也就是空白板的大小就是$need_width,$need_height
		if($auto_cut && (($accord_to_h = $scale_width < $scale_height && $need_height <= $image_data['height']) || 
						 ($accord_to_w = $scale_width >= $scale_height && $need_width <= $image_data['width']))) {
			//如果按高缩,@need_height需小于等于原始图的高
			if($accord_to_h) {
				$thumb_width  = intval($image_data['width'] * $scale_height);
				$thumb_height = $need_height;
			}
			if(isset($accord_to_w)) { //或左边的条件为真时,右边不会再判断了,所以这里用isset判断
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
		if('gif' == $append_image_func) { //使缩略的gif图保持透明度
			$thumbnail = imagecreate($truecolor_width, $truecolor_height); //建立一块空白板
			$background_color  =  imagecolorallocate($thumbnail,  0, 255, 0);//指派一个绿色  
			imagecolortransparent($thumbnail, $background_color);//设置为透明色
		} else {
			$thumbnail = imagecreatetruecolor($truecolor_width, $truecolor_height); //建立一块背景为黑色真彩图(空白板)
		}
		if('png' == $append_image_func) {
			//设置标记以在保存PNG图像时保存完整的alpha通道信（与单一透明色相反),不合并颜色
			imagealphablending($thumbnail, FALSE);
			//先必须用alphablending清位,GD 2.0.1至少
			imagesavealpha($thumbnail, TRUE);
		}
		
		//载入图片
		$get_image_func = 'imagecreatefrom' . $append_image_func;
		$src_img = $get_image_func($src_img_file);;
		$resampled_func = function_exists('imagecopyresampled') ? 'imagecopyresampled' : 'imagecopyresized';
		$resampled_func($thumbnail, $src_img, -1 * $src_x, -1 * $src_y, 0, 0, $thumb_width, $thumb_height, $image_data['width'], $image_data['height']);
		
		$output_image_func = 'image' . $append_image_func;
		//生成缩略图
		'jpeg' == $append_image_func ? $output_image_func($thumbnail, $output_image, $this->thumb_quality) : $output_image_func($thumbnail, $output_image);
		return $output_image_url;
	}
	
	//获取图片信息
	//@img string 原始图片
	//@return array 图片信息
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
	
	//确认是否能执行
	//@img string 原始图片
	//@return boolen 能返回真,不能返回假
	public function check_image($img) {
		return extension_loaded('gd') && preg_match("/\.jpg|jpeg|gif|png/i", $img) && is_file($img);
	}
}
?>