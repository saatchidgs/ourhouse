<?php
include_once('RCCWP_Application.php');

class RCCWP_ManagementPage
{
	function AssignCustomWritePanel()
	{
		global $flutter_domain;
		$postId = (int)$_GET['assign-custom-write-panel'];
		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		$customWritePanelOptions = RCCWP_Options::Get();
		$message = 'The Post that you\'re about to edit is not associated with any Custom Write Panel.';
		?>
		
		<div id="message" class="updated"><p><?php _e($message); ?></p></div>
		
		<div class="wrap">
		<h2><?php _e('Assign Custom Write Panel'); ?></h2>
		
		<form action="" method="post" id="assign-custom-write-panel-form">
		
		<table class="optiontable">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Custom Write Panel', $flutter_domain); ?>:</th>
			<td>
				<select name="custom-write-panel-id" id="custom-write-panel-id">
					<option value=""><?php _e('(None)', $flutter_domain); ?></option>
				<?php
				$defaultCustomWritePanel = $customWritePanelOptions['default-custom-write-panel'];
				foreach ($customWritePanels as $panel) :
					$selected = $panel->id == $defaultCustomWritePanel ? 'selected="selected"' : '';
				?>
					<option value="<?php echo $panel->id?>" <?php echo $selected?>><?php echo $panel->name?></option>
				<?php
				endforeach;
				?>
				</select>
			</td>
		</tr>
		</tbody>
		</table>
		
		<input type="hidden" name="post-id" value="<?php echo $postId?>" />
		<p class="submit" >
			<input name="edit-with-no-custom-write-panel" type="submit" value="<?php _e("Don't Assign Custom Write Panel", $flutter_domain); ?>" />
			<input name="edit-with-custom-write-panel" type="submit" value="<?php _e('Edit with Custom Write Panel', $flutter_domain); ?>" />
		</p>
		
		</form>
		
		</div>
		
		<?php
	}
	
	function GetCustomFieldEditUrl($customWriteModuleId, $customGroupId, $customFieldId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&edit-custom-field=' . $customFieldId . '&custom-group-id=' . $customGroupId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetCustomFieldDeleteUrl($customGroupId, $customFieldId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&delete-custom-field=' . $customFieldId . '&custom-group-id=' . $customGroupId;
		return $url;
	}

	function GetModuleDuplicateEditUrl($customWriteModuleId, $duplicateId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&edit-module-duplicate=' . $duplicateId . '&module-duplicate-id=' . $duplicateId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetModuleDuplicateDeleteUrl($customWriteModuleId, $duplicateId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&delete-module-duplicate=' . $duplicateId . '&module-duplicate-id=' . $duplicateId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetCustomWritePanelEditUrl($customWritePanelId)
	{
		$url = '?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&view-custom-write-panel=' . $customWritePanelId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}
	
	
	
	function GetCustomWritePanelDeleteUrl($customWritePanelId)
	{
		$url = '?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&delete-custom-write-panel=' . $customWritePanelId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}

	function GetCustomWriteModuleEditUrl($moduleId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&view-custom-write-module=' . $moduleId . '&custom-write-module-id=' . $moduleId;
		return $url;
	}
	
	function GetCustomWriteModuleDeleteUrl($moduleId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&delete-custom-write-module=' . $moduleId . '&custom-write-module-id=' . $moduleId;
		return $url;
	}


	function GetCustomGroupEditUrl($groupId, $moduleId)
	{
		$url = '?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&flutter_action=view-custom-group&custom-group-id=' . $groupId. '&custom-write-module-id=' . $moduleId;
		return $url;
	}
	
	function GetCustomGroupDeleteUrl($groupId)
	{
		$url = '?page=' . 'FlutterManageModules' . '&delete-custom-group=' . $groupId . '&custom-group-id=' . $groupId;
		return $url;
	}

	function GetCustomPanelModuleDeleteUrl($customWritePanelId, $panelModuleId)
	{
		$url = '?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&delete-custom-panel-module=' . $panelModuleId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}
	
	
	function GetCustomWriteModuleGenericUrl($flutterAction, $moduleId = null)
	{
		if (empty($moduleId) && isset($_REQUEST['custom-write-module-id'])){
			$moduleId = $_REQUEST['custom-write-module-id'];
		}
			
		if (!empty($moduleId)){
			$url = RCCWP_ManagementPage::GetModulePage() . "&custom-write-module-id=$moduleId&flutter_action=$flutterAction";
		}
		else{
			$url = RCCWP_ManagementPage::GetModulePage() . "&flutter_action=$flutterAction";
		}
		
		return $url;
	}
	
	/**
	 * Generates a url containing the write panel id and the action
	 *
	 * @return unknown
	 */
	function GetCustomWritePanelGenericUrl($flutterAction, $customWritePanelId = null)
	{
		if (empty($customWritePanelId) && isset($_REQUEST['custom-write-panel-id'])){
			$customWritePanelId = $_REQUEST['custom-write-panel-id'];
		}
			
		if (!empty($customWritePanelId)){
			$url = RCCWP_ManagementPage::GetPanelPage() . "&custom-write-panel-id=$customWritePanelId&flutter_action=$flutterAction";
		}
		else{
			$url = RCCWP_ManagementPage::GetPanelPage() . "&flutter_action=$flutterAction";
		}
		
		return $url;
	}
	
	function GetPanelPage(){
		return '?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php');
	}
	
	function GetModulePage(){
		return '?page=FlutterManageModules';
	}
	
	

	// ----------- Modules
	function ViewModules()
	{
		global $flutter_domain;
		$customWriteModules = RCCWP_CustomWriteModule::GetCustomModules();
		?>
<script type='text/javascript' src='../../wp-includes/js/thickbox/thickbox.js'></script>
<link rel='stylesheet' href='../../wp-includes/js/thickbox/thickbox.css' type='text/css' media='all' />



		<div class="wrap">
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWriteModuleGenericUrl('import-module')?>" method="post"  id="posts-filter" name="ImportModuleForm" enctype="multipart/form-data">
			<h2><?php _e('Modules'); ?></h2>
			<p id="post-search">					
				<input id="import-module-file" name="import-module-file" type="file" /> 
				<a href="#none" class="button-secondary" style="display:inline" onclick="document.ImportModuleForm.submit();"><?php _e('Import a Module',$flutter_domain); ?></a>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWriteModuleGenericUrl('create-custom-write-module'); ?>" class="button-secondary" style="display:inline">+ <?php _e('Create a Module',$flutter_domain); ?></a>
			</p>	
		</form>

		<br class="clear"/>
		<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
		<thead>
		<tr>
			<th scope="col" width="70%"><?php _e('Name'); ?></th>
			<th scope="col" colspan="2" ><?php _e('Actions'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($customWriteModules as $module) :
			$class = $class == '' ? 'alternate' : '';
		?>
		<tr class="<?php echo $class?>">
			<td><?php echo $module->name ?></td>
			<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($module->id); ?>" class="edit"><?php _e('Edit') ?></a></td>
			<td><a href="<?php echo FLUTTER_URI."RCCWP_ExportModule.php?custom-write-module-id={$module->id}";?>&amp;TB_iframe=true&amp;height=500&amp;width=700" class="thickbox" title='Export Module'><?php _e('Export',$flutter_domain); ?></a></td>
		</tr>
		<?php
		endforeach;
		?>
		</tbody>
		</table>

		<form style="display:none" id="do_export" name="do_export" action="<?php echo FLUTTER_URI."RCCWP_ExportModule.php" ?>" method="post" >
			<input type="text" name="write_panels"/>
			<input type="text" name="custom-write-module-id"/>
		</form>	
		
		</div>
		<?php 
	}
}
?>