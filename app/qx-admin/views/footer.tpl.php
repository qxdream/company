<?php
/* 
 [QXDream] footer.tpl.php 2010-02-25
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
?>
<script type="text/javascript">
window.onload = function() {
	if (top.location != self.location) parent.document.getElementById('right').height = document.body.scrollHeight;
}
$(function(){
	$('.table_data_box tbody tr:even').addClass('tr_even');
	$('.check_all').click(function(){
		if($(this).attr('checked') == true) {
			$('input:checkbox').attr("checked", true);
		} else {
			$('input:checkbox').attr("checked", false);
		}
	});
});
</script>
<!--[if IE]>
<style type="text/css">
html { overflow-y: auto; }
</style>
<![endif]-->