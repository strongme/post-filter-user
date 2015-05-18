<?php  
global $wpdb;
$table_name = PFU_TABLE_GROUP_NAME;
$msg = '';
$msg_type = 'error';
function redirect(){
	$redirect = '<script type="text/javascript">';
	$redirect .= 'window.location = "' . menu_page_url(PFU_GROUP_LIST_PAGE,false). '"';
	$redirect .= '</script>';
	echo $redirect;
}

if(isset($_GET['edit'])) {
	$current_id = $_GET['edit'];
	$sql = "SELECT * FROM $table_name WHERE id=$current_id";
	$tmp = $wpdb->get_results($sql);	
	if(sizeof($tmp)==1) {
		$name = $tmp[0]->name;
		$description = $tmp[0]->description;
	}
}else {
	$current_id = '';
}

if(isset($_GET['delete'])) {
	$current_id = $_GET['delete'];
	if($current_id!='') {
		//删除分组
		$sql = "DELETE FROM $table_name WHERE id=$current_id";
		$wpdb->get_results($sql);
	}
	redirect();
}

if(isset($_POST['submit-save-exit']) || isset($_POST['submit-save'])) {
	$name = $_POST['name'];
	$description = $_POST['description'];

	if($current_id!='') {
		//更新
		$sql = "UPDATE $table_name SET name='$name',description='$description' WHERE id=$current_id";
		$wpdb->get_results($sql);
	}else {
		//检测是否已经存在此用户分组
		$sql_exist = "SELECT COUNT(id) count FROM $table_name WHERE name = '$name'";
		$is_exist = $wpdb->get_results($sql_exist);
		if($is_exist[0]->count != '0') {
			$msg = '已经存在此用户分组';
			if(isset($_POST['submit-save-exit'])) {$_POST['submit-save-exit']=null;}
		}else {
			//添加
			$sql = "INSERT INTO $table_name (name,description) values('$name','$description');";
			$save = $wpdb->get_results($sql);
			$msg = "添加用户分组成功";
			$msg_type = 'notice notice-success';
		}
	}


		redirect();
	// if(isset($_POST['submit-save-exit'])){
	// }
}



require_once('_header.php');	
?>
<div class="wrap">
	<?php echo $content['header'] ?>
	<h3>添加用户分组</h3>
	<br>
	<?php if ($msg!=''): ?>
		<div class="<?php echo $msg_type;$msg_type='error'; ?>">
			<?php echo $msg;$msg=''; ?>
		</div>
	<?php endif ?>
	<form action="" method="post">
		<input type="hidden" name="edit" value="<?php echo $current_id;?>" />
		<table class="add-user-group-table">
			<tr>
				<td>分组名称</td>
			</tr>
			<tr>
				<td><input type="text" class="width-100" name="name" value="<?php echo $name; ?>"></td>
			</tr>
			<tr>
				<td>分组描述</td>
			</tr>
			<tr>
				<td><textarea cols="30" rows="10" class="width-100" name="description"><?php echo $description; ?></textarea></td>
			</tr>
			<tr>
				<td align="left">
					<div class="func-submit">
						<?php submit_button('保存','secondary','submit-save', false); ?>&nbsp;
						<a href="<?php echo menu_page_url(PFU_GROUP_LIST_PAGE,false);?>" class="button secondary">取消</a>&nbsp;
					</div>
				</td>
			</tr>
		</table>
	</form>
	
</div>
