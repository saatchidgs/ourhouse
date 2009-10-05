<?php
include_once('RCCWP_CustomGroup.php');

class RCCWP_CustomGroupPage
{
	function Content($customGroup = null)
	{
		global $flutter_domain;
		$customGroupName = "";
		if (isset($_GET['custom-write-panel-id']) )
			$customWritePanelId = $_GET['custom-write-panel-id'];
		if (isset($_POST['custom-write-panel-id']) )
			$customWritePanelId = $_POST['custom-write-panel-id'];

		if ($customGroup != null)
		{
			$customGroupName = $customGroup->name;
			$customGroupDuplicate = $customGroup->duplicate;
			$customGroupAtRight = $customGroup->at_right;
		}
		
  		?>
		<?php if($customWritePanelId) { ?>
  			<input type="hidden" name="custom-write-panel-id" value="<?php echo $customWritePanelId?>">
		<?php } ?>

		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $flutter_domain); ?>:</th>
			<td><input name="custom-group-name" id="custom-group-name" size="40" type="text" value="<?php echo $customGroupName?>" /></td>
		</tr>

		<tr>
			<th scope="row" align="right"><?php _e('Duplication', $flutter_domain); ?>:</th>
			<td><input name="custom-group-duplicate" id="custom-group-duplicate" type="checkbox" value="1" <?php echo $customGroupDuplicate == 0 ? "":"checked" ?> />&nbsp;<?php _e('The group can be duplicated', $flutter_domain); ?></td>
		</tr>
		
		<tr>
			<th scope="row" align="right"><?php _e('Position', $flutter_domain); ?>:</th>
			<td><input name="custom-group-at_right" id="custom-group-at_right" type="checkbox" value="1" <?php echo $customGroupAtRight == 0 ? "":"checked" ?> />&nbsp;<?php _e('Add the group on the right.', $flutter_domain); ?></td>
		</tr>

		<?php
		if (!isset($customGroup)) :
		?>
		<tr>
			<th scope="row" align="right"><?php _e('Custom Fields', $flutter_domain); ?>:</th>
			<td><?php _e('Add custom fields later by editing this custom group.', $flutter_domain); ?></td>
		</tr>
		<?php
		endif;
		?>
		</tbody>
		</table>
        <br />
		
		<?php
	}
	
	function Edit()
	{
		global $flutter_domain;
		$customGroup = RCCWP_CustomGroup::Get((int)$_REQUEST['custom-group-id']);
		?>
		<div class="wrap">
		
		<h2><?php _e('Edit Group', $flutter_domain); ?> - <?php echo $customGroup->name?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-group')."&custom-group-id={$customGroup->id}"?>" method="post" id="edit-custom-group-form">
		
		<?php
		RCCWP_CustomGroupPage::Content($customGroup);
		?>
		
		<p class="submit" >
			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-edit-custom-group')?>" class="button"><?php _e('Cancel', $flutter_domain); ?></a> 
			<input type="submit" id="submit-edit-custom-group" value="<?php _e('Update', $flutter_domain); ?>" />
		</p>
		</form>
		
		</div>
		<br />
	        <a href="http://flutter.freshout.us"><img src="<?php echo FLUTTER_URI."/images/flutter_logo.jpg"?>" /></a>
		<?php
	}
	
	function GetCustomFields($customGroupId)
	{
		global $wpdb;
		$sql = "SELECT cf.id, cf.name, tt.name AS type, cf.description, cf.display_order, co.options, co.default_option AS default_value, tt.has_options, cp.properties, tt.has_properties, tt.allow_multiple_values FROM " . RC_CWP_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " tt ON cf.type = tt.id" . 
			" WHERE group_id = " . $customGroupId .
			" ORDER BY cf.display_order";
		$results =$wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		
		for ($i = 0; $i < $wpdb->num_rows; ++$i)
		{
			$results[$i]->options = unserialize($results[$i]->options);
			$results[$i]->properties = unserialize($results[$i]->properties);
			$results[$i]->default_value = unserialize($results[$i]->default_value);
		}
		
		return $results;
	}
	
	/*function View($param = 23)
	{

		if(isset($_GET['custom-write-panel-id']) && !empty($_GET['custom-write-panel-id']) )
			$customWritePanelId = (int)$_GET['custom-write-panel-id'];
		if(isset($_POST['custom-write-panel-id']) && !empty($_POST['custom-write-panel-id']) )
			$customWritePanelId = (int)$_POST['custom-write-panel-id'];

		if(isset($_GET['custom-group-id']) && !empty($_GET['custom-group-id']) )
			$customGroupId = (int)$_GET['custom-group-id'];
		if(isset($_POST['custom-group-id']) && !empty($_POST['custom-group-id']) )
			$customGroupId = (int)$_POST['custom-group-id'];

		$customGroup = RCCWP_CustomGroup::Get($customGroupId);

		?>

		<div class="wrap">

		<h2>Custom Group Info</h2>
		<h4><a href="<?php echo RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($customWritePanelId); ?>"> « Back to Module</a></h4>
		<form action="" method="post" id="view-group-form">
		
		<input type="hidden" name="custom-group-id" value="<?php echo $customGroupId?>" />
			
  		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
  		<tbody>
  		<tr>
			<th scope="row" align="right">Name:</th>
			<td><?php echo $customGroup->name ?></td>
		</tr>
  		</tbody>
  		</table>
		  
		
		<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-group')."&custom-group-id=$customGroupId"?>" class="button-secondary"><?php _e('Edit Group'); ?></a>		
		
		
		<h3>Custom Fields</h3>

		<div class="tablenav"><div class="alignright">
			<input name="flutter_action" type="hidden" value="create-custom-field" />
			<input name="create-custom-field" type="submit" id="create-custom-field" value="Create Custom Field"  class="button-secondary"  />
		</div></div>
		<br class="clear"/>
		
  		<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
  		<thead>
  		<tr>
  			<th scope="col">Order</th>
			<th scope="col">Name</th>
			<th scope="col">Type</th>
<!--			<th scope="col">Description</th> -->
			<th scope="col" colspan="2">Action</th>
		</tr>
  		</thead>
  		<tbody>
  		<?php
  		$custom_fields = RCCWP_CustomGroup::GetCustomFields($customGroupId);
  		foreach ($custom_fields as $field) :
  			$class = $class == '' ? 'alternate' : '';
  		?>
  			<tr class="<?php echo $class?>">
  				<td align="right" width="3"><?php echo $field->display_order?></td>
  				<td align="center"><?php echo $field->name?></td>
  				<td><?php echo $field->type?></td>
<!--  				<td><?php echo $field->description?></td> -->
  				<td><a href="<?php echo RCCWP_ManagementPage::GetCustomFieldEditUrl($customWritePanelId, $customGroupId, $field->id)?>" class="edit">Edit</a></td>
  				<td><a href="<?php echo RCCWP_ManagementPage::GetCustomFieldDeleteUrl($customGroupId, $field->id)?>" class="delete">Delete</a></td>
  			</tr>
  		<?php
  		endforeach;
  		?>
  		</tbody>
  		</table>
			
		</form>

		</div>
		
		<?php
	}*/
}
?>
