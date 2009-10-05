<?php
include_once('RCCWP_CustomGroup.php');

class RCCWP_ModuleDuplicatePage
{
	function Content($moduleDuplicate = null)
	{
		global $flutter_domain;
		$customGroupName = "";
		if (isset($_GET['custom-write-module-id']) )
			$moduleID = $_GET['custom-write-module-id'];
		if (isset($_POST['custom-write-module-id']) )
			$moduleID = $_POST['custom-write-module-id'];

		if ($moduleDuplicate != null)
		{
			$moduleDuplicateName = $moduleDuplicate->duplicate_name;
		?>
		<input type="hidden" name="module-duplicate-id" value="<?php echo $_REQUEST['module-duplicate-id']?>" />
		<?php
		}
		
  		?>
		<?php if($moduleID) { ?>
  			<input type="hidden" name="custom-write-module-id" value="<?php echo $moduleID?>">
		<?php } ?>

		<table class="optiontable" border="0">
		<tbody>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $flutter_domain); ?>:</th>
			<td><input name="module-duplicate-name" id="module-duplicate-name" size="40" type="text" value="<?php echo $moduleDuplicateName?>" /></td>
		</tr>

		</tbody>
		</table>
		<?php
	}
	
	function Edit()
	{
		global $flutter_domain;
		$moduleDuplicate = RCCWP_ModuleDuplicate::Get((int)$_REQUEST['module-duplicate-id']);
		?>
		<div class="wrap">
		
		<h2><?php _e('Edit Module Duplicate', $flutter_domain); ?></h2>
		
		<form action="" method="post" id="edit-module-duplicate-form">
		
		<?php
		RCCWP_ModuleDuplicatePage::Content($moduleDuplicate);
		?>
		
		<p class="submit" >
			<input name="cancel-edit-module-duplicate" type="submit" id="cancel-edit-module-duplicate" value="<?php _e('Cancel',$flutter_domain); ?>" /> 
			<input name="submit-edit-module-duplicate" type="submit" id="submit-edit-module-duplicate" value="<?php _e('Update',$flutter_domain); ?>" />
		</p>
		</form>
		
		</div>
		
		<?php
	}
	
}
?>
