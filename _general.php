<?php  
require_once('_header.php');
?>
<div class="wrap">
	<?php echo $content['header'] ?>
	<h3>使用方法</h3>
	<ul>
		<li>1. 根据业务需求<a href="<?php menu_page_url(PFU_GROUP_LIST_PAGE); ?>&edit" >添加</a>相应的用户分组</li>
		<li>2. 为用户帐号设置相应的用户分组</li>
		<li>3. 编写文章，根据业务类型勾选能够看到此文章的用户分组</li>
		<li>4. 完成并登录相应帐号进行测试，查看是否正确按照用户分组显示文章</li>
		<li>5. 设置相应的菜单分组</li>
		<li>6. 默认添加一条游客分组</li>
	</ul>
	
	<hr>
	<div class="notice notice-success">
		主页 : <a href="http://strongme.cn" target="_blank">译邻</a><br>
		分站 : <a href="http://shareagle.com" target="_blank">Shareagle</a><br>
		微博 : <a href="http://www.weibo.com/strongwalter" target="_blank">@奔跑的阿水哥</a><br>
		邮件 : <a href="mailto:strongwalter2014@gmail.com" target="_blank">strongwalter2014@gmail.com</a>
	</div>
</div>