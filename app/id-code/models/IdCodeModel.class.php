<?php
/* 
 [QXDream] IdCodeModel.class.php 2011-02-27
*/

// ★ ------------------------------------------
// ↓ 作者      踏雪残情
// ★ ------------------------------------------
// ↓ 建立日期  2011-02-27
// ★ ------------------------------------------
// ↓ 更新日期  2011-02-27
// ★ ------------------------------------------
// ↓ 功能      验证码模型
// ★ ------------------------------------------
// ↓ 版本      ver 1.0
// ★ ------------------------------------------

class IdCodeModel extends Model {
	public $width;
	public $height;
	public $img;
	public $ttf;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function set($width = 130, $height = 50, $ttf = 'times.ttf') {
		$this->width = $width;
		$this->height = $height;
		$this->ttf = $ttf;
	}
	
	public function execute() {
		$rand = '';
		for($i=0;$i<4;$i++){
			if($i == rand(0,4)) {
				$rand.=dechex(rand(1,15));
			} else {
				$rand.=strtoupper(dechex(rand(1,15)));
			}
		}
		
		$_SESSION['check_code'] = $rand;//系统生成一个check_pic
	
		$this->img = imagecreatetruecolor($this->width, $this->height);//创建一个图片
		//设置颜色
		$bg=imagecolorallocate($this->img, 240, 243, 248);
		imagefill($this->img,0,0,$bg);//把背景填充颜色
		
		//一些颜色
		$purple    = imagecolorallocate($this->img,92,2,122);
		$green     = imagecolorallocate($this->img,84,105,4);
		$blue      = imagecolorallocate($this->img,15,24,115);
		$r_blue    = imagecolorallocate($this->img,15,24,200);
		$black     = imagecolorallocate($this->img,11,12,15);
		$red       = imagecolorallocate($this->img,200,12,0);
		$yellow    = imagecolorallocate($this->img,220,150,0);
		$rectangle = imagecolorallocate($this->img,200,200,232); //矩形边框颜色
		
		//把颜色加入到数值,随机取出
		$color_array = array($purple, $green, $blue, $black, $red, $r_blue, $yellow);
		
		imagerectangle($this->img,0,0,$this->width-1,$this->height-1,$rectangle); //画一矩形框
	
		for($i=0;$i<5;$i++){
			imageline($this->img,rand(0,$this->width),0,rand(0,$this->height),$this->height,$color_array[array_rand($color_array)]); //画线
		}
	
		for($i=0;$i<200;$i++){
			imagesetpixel($this->img,rand()%200,rand()%50,$color_array[array_rand($color_array)]);//画点
		}
		
		$str_total = strlen($rand);
		for($i = 0; $i < $str_total; $i++) {
			$str = substr($rand, $i, 1);
			imagettftext($this->img,rand(20,28),rand(-20,20),25*($i+1),rand(30,45),$color_array[array_rand($color_array)],QX_ROOT.PUBLIC_DIR.'fonts/'.$this->ttf,$str);
		}
	}
	
	public function image_pic() {
		$this->execute();
		header("Content-type:image/jpeg");//输入文件的图
		imagejpeg($this->img);
		imagedestroy($this->img);
	}
}
?>