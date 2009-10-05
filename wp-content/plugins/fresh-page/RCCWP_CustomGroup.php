<?php
/**
 * @package FlutterDatabaseObjects
 */

/**
 * Create/Edit/Delete groups. Groups are just a collection of fields.
 * @package FlutterDatabaseObjects
 */

class RCCWP_CustomGroup
{
	
	/**
	 * Create a new group in a write panel
	 *
	 * @param unknown_type $customWritePanelId
	 * @param unknown_type $name group name
	 * @param unknown_type $duplicate a boolean indicating whether the group can be duplicated
	 * @param unknown_type $at_right a boolean indicating whether the group should be placed at right side.
	 * @return the id of the new group
	 */
	function Create($customWritePanelId, $name, $duplicate, $at_right)
	{
		require_once('RC_Format.php');
		global $wpdb;
		$sql = sprintf(
			"INSERT INTO " . RC_CWP_TABLE_PANEL_GROUPS .
			" (panel_id, name, duplicate, at_right) values (%d, %s, %d, %d)",
			$customWritePanelId,
			RC_Format::TextToSql($name),
			$duplicate,
			$at_right
			);
		$wpdb->query($sql);
		
		$customGroupId = $wpdb->insert_id;
		return $customGroupId;
	}
	
	/**
	 * Delete a group given id
	 *
	 * @param integer $customGroupId
	 */
	function Delete($customGroupId = null)
	{
		include_once ('RCCWP_CustomField.php');
		if (isset($customGroupId))
		{
			global $wpdb;
			
			$customFields = RCCWP_CustomGroup::GetCustomFields($customGroupId);
			foreach ($customFields as $field) 
			{
				RCCWP_CustomField::Delete($field->id);
  			}
		  	
  			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANEL_GROUPS .
				" WHERE id = %d",
				$customGroupId
				);
			$wpdb->query($sql);
		}
	}
	
	/**
	 * Get group properties
	 *
	 * @param integer $groupId
	 * @return an object representing the group
	 */
	
	function Get($groupId)
	{
		global $wpdb;
	
		$sql = "SELECT * FROM " . RC_CWP_TABLE_PANEL_GROUPS;
		$sql .=	" WHERE id = " . (int)$groupId;
		$results = $wpdb->get_row($sql);
		return $results;
	}
	
	/**
	 * Get a list of the custom fields of a group
	 *
	 * @param integer $customGroupId the group id
	 * @return an array of objects containing information about fields. Each object contains 
	 * 			3 objects: properties, options and default_value   
	 */
	function GetCustomFields($customGroupId)
	{
		global $wpdb;
		$sql = "SELECT cf.id, cf.name, tt.name AS type, cf.description, cf.display_order, cf.required_field,cf.css, co.options, co.default_option AS default_value, tt.has_options, cp.properties, tt.has_properties, tt.allow_multiple_values, cf.duplicate FROM " . RC_CWP_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" JOIN " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " tt ON cf.type = tt.id" . 
			" WHERE group_id = " . $customGroupId .
			" ORDER BY cf.display_order,cf.id ASC";

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
	
	/**
	 * Update the group
	 *
	 * @param unknown_type $customWritePanelId
	 * @param unknown_type $name group name
	 * @param unknown_type $duplicate a boolean indicating whether the group can be duplicated
	 * @param unknown_type $at_right a boolean indicating whether the group should be placed at right side. 
	 */	
	function Update($customGroupId, $name, $duplicate, $at_right)
	{
		require_once('RC_Format.php');
		global $wpdb;
		//$capabilityName = RCCWP_CustomWriteModule::GetCapabilityName($name);
	
		$sql = sprintf(
			"UPDATE " . RC_CWP_TABLE_PANEL_GROUPS .
			" SET name = %s , duplicate = %d, at_right = %d".
			" where id = %d",
			RC_Format::TextToSql($name),
			$duplicate,
			$at_right,
			$customGroupId );
		$wpdb->query($sql);
		
	}
}
?>
