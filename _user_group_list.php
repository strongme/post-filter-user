<?php  

require_once('class-pfu-user-group-table.php');

global $wpdb;
$msg = '';
$table_name = PFU_TABLE_GROUP_NAME;


if(isset($_GET['action']) && isset($_GET['action2'])){
	if($_GET['action']=='delete' || $_GET['action2']=='delete'){
		if(isset($_GET['tpl'])){
	        foreach($_GET['tpl'] as $tpl){
	        	delete_user_group($tpl,$wpdb);
	        }
        }
	}
}
if(isset($_GET['delete'])){
	delete_user_group($_GET['delete'],$wpdb);
}

function delete_user_group($id,$wpdb){
	$sql = "DELETE FROM ".PFU_TABLE_GROUP_NAME." WHERE id=$id";
	$wpdb->get_results($sql);
}

$sql = "SELECT * FROM $table_name ORDER BY id";
$tmp = $wpdb->get_results($sql);
$data = array();
foreach($tmp as $d) {
	$data[]=array('id'=>$d->id, 'name'=>$d->name, 'description'=>$d->description);
}

$wp_list_table = new PFU_User_Group_Table($data);
$wp_list_table->prepare_items();
require_once('_header.php');
?>
<div class="wrap">
	<?php echo $content['header'] ?>
	<h3>用户分组列表</h3>
	<a href="<?php menu_page_url(PFU_GROUP_LIST_PAGE); ?>&edit" class="button">添加分组</a>
	<br>
	<?php if ($msg!=''): ?>
		<div class="error">
			<?php echo $msg;$msg=''; ?>
		</div>
	<?php endif ?>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo PFU_GROUP_LIST_PAGE; ?>">
		<?php $wp_list_table->display(); ?>		
	</form>
	
</div>