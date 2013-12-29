<?php
/* 
 [QXDream] index.tpl.php 2011-05-02
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="index_page">
<!--[if IE]>
<style type="text/css">
html { overflow: hidden; }
</style>
<![endif]-->
<?php if($GLOBALS['QXDREAM']['qx_group_id'] > 1) { ?>
<script type="text/javascript" src="<?php echo QX_PATH . PUBLIC_DIR; ?>js/qxd_tree.js"></script>
<?php } ?>
<script type="text/javascript">
//防止被框架引用
if (top.location != self.location) top.location=self.location;
$(function(){
		//块级导航
		$('#block_menu a').click(function() {
			$('#block_menu a').attr('className', '');
			$(this).attr('className', 'bm_cur sprites');
			$('#nav_tool .block_menu_content').removeClass('bmc_cur');
			$('#nav_tool .block_menu_content').eq($('#block_menu a').index($(this))).addClass('bmc_cur');
			return false;
		}).bind('focus',function(){ 
                 this.blur();
        });
		
		//左侧导航
		$('#nav_tool .sub_menu li a').click(function(){
				$('#qxd_tree1 a').removeClass('qxd_cur');
				$('#nav_tool li').removeClass('current');
				$(this).parent().addClass('current');
		}).bind('focus',function(){//去虚框
                 this.blur();
        });
		$('#nav_tool h3.top_menu a').click(function(){
				$('#qxd_tree1 a').removeClass('qxd_cur');
				$('#nav_tool li').removeClass('current');
				$(this).parent().next().children('li:eq(0)').addClass('current');
		})
		var isIe = $.browser.msie;
		$('.drop_control').toggle(function(){//右上菜单
				var obj = $(this).removeClass('drop_control').addClass('drop_control_options_show').parent().next('.sub_menu');
				if(!isIe) { obj.slideUp('fast'); } else { obj.hide(); }
		},function(){
				var obj = $(this).addClass('drop_control').removeClass('drop_control_options_show').parent().next('.sub_menu');
				if(!isIe) { obj.slideDown('fast'); } else { obj.show(); }
		});
		$('.drop_control_options_show').toggle(function(){//左侧菜单
				var obj = $(this).removeClass('drop_control_options_show').addClass('drop_control').parent().next('.sub_menu');
				if(!isIe) { obj.slideDown('fast'); } else { obj.show(); }
		},function(){
				var obj = $(this).addClass('drop_control_options_show').removeClass('drop_control').parent().next('.sub_menu');
				if(!isIe) { obj.slideUp('fast'); } else { obj.hide(); }
		});
		ifr();
		$('#qxd_tree1 a').click(function() {
			$('#nav_tool li').removeClass('current');
		});
});
function ifr() {
	var screenHeight = parseInt(document.documentElement.clientHeight) - 73;
	$('#right').css('height',screenHeight);
	$('#nav_tool').css('height',screenHeight);
}
window.onresize = function() { //改变窗口大小时
    ifr();
}
</script>

<div id="wrapper">

		<!--头部开始-->
		<div id="branding">
				<div id="tool" class="f_r">
						<div id="func" class="f_l">
								<a href="<?php echo app_url() . 'user/edit/user_id/' . $qx_user_id; ?>"><?php echo $qx_user_name; ?></a>(<?php echo $qx_role; ?>) | <a href="<?php echo app_url() . 'login/logout/'; ?>"><?php echo $admin_language['quit']; ?></a> | <a href="<?php echo SITE_URL . (REWRITE ? '' : 'index.php/') . (!empty($qx_company_uid) ? $qx_company_uid . '/' : ''); ?>" target="_blank"><?php echo $admin_language['site_index']; ?></a>
						</div>
				</div>
				<h1 class="f_l"><a href="" class="a_text_hidden" target="right">倾行后台管理系统</a></h1>
				<ul id="block_menu" class="f_l list_f_l">
						<?php
						foreach($top_menu as $k => $v) {
						?>
						<li><a<?php if(1 == $k) { echo ' class="bm_cur sprites"'; } ?> href="<?php echo $v['url']; ?>"><?php echo $v['name']; ?></a></li>
						<?php
						}
						?>
				</ul>
				<div class="clear"></div>
		</div>
		<!--头部结束-->
		
		<!--导航菜单开始-->
		<div id="nav_tool" class="f_l">
				<div id="nav_tool_in"> 
						<div id="panel"><a href="<?php echo app_url(); ?>"><?php echo $admin_language['panel_center']; ?></a></div>
		
						<div class="split sprites"></div>
						<!-- 没有子菜单*****|<h3 class="top_menu top_menu_no_options"><a href="#"></a></h3>|***** 
							 含有子菜单*****|<h3 class="top_menu"><span class="drop_control f_r sprites "></span><a href="#"></a></h3>|*****
							 class为collapse子菜单默认就是收拢的
						 -->						 
						<?php echo $sidebar_menu; ?>
				</div>
		</div>
		<!--导航菜单结束-->
		
		<!--主体开始-->
		<div id="content">
				<!-- 勿修改此浮动框架ID -->
				<iframe name="right" id="right" scrolling="yes" frameborder="0" src="<?php echo app_url() . 'index/home/'; ?>"></iframe>
		</div>
		<!--主体结束-->
</div>
<!--[if IE 6]>
<script type="text/javascript"> 
document.execCommand('BackgroundImageCache', false, true);
</script>
<![endif]-->
<script type="text/javascript">
//针对火狐
if(!$.browser.msie) document.getElementById('right').scrolling = 'auto';
</script>
</body>
</html>