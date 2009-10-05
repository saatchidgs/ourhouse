<?php
include_once('RCCWP_CustomWriteModule.php');

class RCCWP_CustomWriteModulePage
{
	function Content($customWriteModule = null)
	{
		global $flutter_domain;
		
		$customWriteModuleName = "";
		
		if ($customWriteModule != null)
		{
			$customWriteModuleName = $customWriteModule->name;
		?>
		<input type="hidden" name="custom-write-module-id" value="<?php echo $_REQUEST['custom-write-module-id']?>" />
		<?php
		}
		
  		?>

		<?php
		if (isset($_GET['err_msg'])) :
			switch ($_GET['err_msg']){
				case -1:
		?>
			<div class="updated fade"><p> <?php _e('A module with the same name already exists. Please choose a different name.', $flutter_domain); ?></p></div>

		<?php
			}
		endif;
		?>

		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $flutter_domain); ?>:</th>
			<td><input name="custom-write-module-name" id="custom-write-module-name" size="40" type="text" value="<?php echo $customWriteModuleName?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Description', $flutter_domain); ?>:</th>
			<td><textarea name="custom-write-module-description" id="custom-write-module-description" rows="3" cols="40"><?php echo $customWriteModule->description?></textarea></td>
		</tr>

		</tbody>
		</table>
		
		<?php
	}
	
	function Edit()
	{
		global $flutter_domain;
		$customWriteModule = RCCWP_CustomWriteModule::Get((int)$_REQUEST['custom-write-module-id']);
		?>



		<div class="wrap">
		
		<h2><?php _e('Edit Custom Write Module', $flutter_domain); ?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($_REQUEST['custom-write-module-id']) ?>" method="post" id="edit-custom-write-module-form">
		
		<?php
		RCCWP_CustomWriteModulePage::Content($customWriteModule);
		?>
		
		<p class="submit" >
			<input name="cancel-edit-custom-write-module" type="submit" id="cancel-edit-custom-write-module" value="<?php _e('Cancel',$flutter_domain); ?>" /> 
			<input name="submit-edit-custom-write-module" type="submit" id="submit-edit-custom-write-module" value="<?php _e('Update',$flutter_domain); ?>" />
		</p>
		</form>
		
		</div>
		
		<?php
	}
	
	
	function View($param = 23)
	{
		global $flutter_domain;
		if(isset($_GET['custom-write-module-id']) && !empty($_GET['custom-write-module-id']) )
			$customWriteModuleId = (int)$_GET['custom-write-module-id'];
		if(isset($_POST['custom-write-module-id']) && !empty($_POST['custom-write-module-id']) )
			$customWriteModuleId = (int)$_POST['custom-write-module-id'];

		$customWriteModule = RCCWP_CustomWriteModule::Get($customWriteModuleId);
		?>

		<div class="wrap">

		<h2><?php _e('Custom Write Module Info', $flutter_domain); ?></h2>
		<h4><a href="?page=FlutterManageModules&view-modules=1"> « <?php _e('Back to Custom Modules List', $flutter_domain); ?></a></h4>
		<form action="" method="post" id="view-write-module-form">
		
		<input type="hidden" name="custom-write-module-id" value="<?php echo $customWriteModuleId?>" />
			

  		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
  		<tbody>
  		<tr>
			<th scope="row" align="right"><?php _e('Name', $flutter_domain); ?>:</th>
			<td><?php echo $customWriteModule->name?></td>
		</tr>
		<tr>
			<th scope="row" align="right"><?php _e('Description', $flutter_domain); ?>:</th>
			<td><?php echo $customWriteModule->description?></td>
		</tr>
  		</tbody>
  		</table>
		  
		<script type="text/javascript" language="javascript">
			function confirmBeforeDelete()
			{
				return confirm("<?php _e('Are you sure you want to delete this module? Please notice that all the template files of this module will be deleted too.', $flutter_domain); ?>");							
			}
		</script>
		<p class="submit" >
			<input name="edit-custom-write-module" type="submit" id="edit-custom-write-module" value="<?php _e('Edit Module', $flutter_domain); ?>" />
			<input onclick="return confirmBeforeDelete();" name="delete-custom-write-module" type="submit" id="delete-custom-write-module" value="<?php _e('Delete Module', $flutter_domain); ?>" />
		</p>
		
		</form>
		
		<form action="" method="post" id="view-module-duplicates">
			<br /><br />
			<h3><?php _e('Module Duplicates for Layout', $flutter_domain); ?></h3>
			<div class="tablenav"><div class="alignright">
				<input name="create-module-duplicate" type="submit" id="create-module-duplicate" value="<?php _e('Create Duplicate', $flutter_domain); ?>" class="button-secondary"  />
			</div></div>
			<br class="clear"/>

			<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
			<thead>
			<tr>
				<th scope="col"><?php _e('Name', $flutter_domain); ?></th>
				<th scope="col" colspan="2"><?php _e('Action', $flutter_domain); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$customWriteModuleDuplicates = RCCWP_ModuleDuplicate::GetCustomModulesDuplicates($customWriteModuleId);
			foreach ($customWriteModuleDuplicates as $customWriteModuleDuplicate) :
				$class = $class == '' ? 'alternate' : '';
			?>
				<tr class="<?php echo $class?>">
					<td><?php echo $customWriteModuleDuplicate->duplicate_name?></td>
					<td><a href="<?php echo RCCWP_ManagementPage::GetModuleDuplicateEditUrl($customWriteModuleId, $customWriteModuleDuplicate->duplicate_id)?>" class="edit"><?php _e('Rename', $flutter_domain); ?></a></td>
					<td><a href="<?php echo RCCWP_ManagementPage::GetModuleDuplicateDeleteUrl($customWriteModuleId, $customWriteModuleDuplicate->duplicate_id)?>" class="delete"><?php _e('Delete', $flutter_domain); ?></a></td>
				</tr>
			<?php
			endforeach;
			?>
			</tbody>
			</table>
		  
		</form>

		</div>
		
		<?php
	}

	function Import()
	{
		global $flutter_domain;
		include_once('RCCWP_CustomWriteModule.php');
		
		if(isset($_FILES['import-module-file']) && !empty($_FILES['import-module-file']['tmp_name']) ) {
			$zipFilePath = $_FILES['import-module-file']['tmp_name'];
		}
		else {
			die(_e('Error uploading file!', $flutter_domain));
		}

		$moduleName = basename($_FILES['import-module-file']['name'], ".zip");
		$moduleID = RCCWP_CustomWriteModule::Import($zipFilePath, $moduleName);
		unlink($zipFilePath);
		
		echo "<h3>".__('The module was imported successfuly.', $flutter_domain)."</h3>";
		echo '<p><a href="' . RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($moduleID).'"> '.__('Click here', $flutter_domain).' </a>'.__(' to edit the module.', $flutter_domain).' </p>';
		
	}
	
}
?>