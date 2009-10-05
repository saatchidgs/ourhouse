<?php

/**
 * @package FlutterDatabaseObjects
 */

/**
 * Create/edit/delete write panels.
 * @package FlutterDatabaseObjects
 */
class RCCWP_CustomWritePanel
{

	/**
	 * Get all Write Panels.
	 *
	 * @return array of objects containing all write panels. Each object contains
	 * 			id, name, description, display_order, capability_name, type, always_show
	 */
	function GetCustomWritePanels()
	{
		global $wpdb;
	
		$sql = "SELECT id, name, description, display_order, capability_name, type, single  FROM " . RC_CWP_TABLE_PANELS;
			
		$sql .= " ORDER BY display_order ASC";
		$results = $wpdb->get_results($sql);
		if (!isset($results)) 
			$results = array();
	
		return $results;
	}
	
	/**
	 * Assign a specified write panel to a role. 
	 *
	 * @param integer $customWritePanelId panel id
	 * @param string $roleName role name (see roles in wordpress)
	 */
	function AssignToRole($customWritePanelId, $roleName)
	{
		$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		$capabilityName = $customWritePanel->capability_name;
		$role = get_role($roleName);
		$role->add_cap($capabilityName);	
	}
	
	/**
	 * Create a new write panel.
	 *
	 * @param string $name write panel name
	 * @param string $description write panel description
	 * @param array $standardFields a list of standard fields ids that are to be displayed in 
	 * 							in the panel. Use $STANDARD_FIELDS defined in RCCWP_Constant.php
	 * @param array $categories array of category ids that are checked by default when the user
	 * 							opens Write tab for that panel.
	 * @param integer $display_order the order of the panel in Flutter > Write Panels tab
	 * @param string $type 'post' or 'page'
	 * @param boolean $createDefaultGroup indicates whether to create a default group.
	 * @return the id of the write panel
	 */
	function Create($name, $description = '', $standardFields = array(), $categories = array(), $display_order = 1, $type = FALSE, $createDefaultGroup=true,$single_post = 0, $default_theme_page)
	{
		include_once('RC_Format.php');
		global $wpdb;

		$capabilityName = RCCWP_CustomWritePanel::GetCapabilityName($name);
		if (!$type) $type = $_POST['radPostPage'];
		$sql = sprintf(
			"INSERT INTO " . RC_CWP_TABLE_PANELS .
			" (name, description, display_order, capability_name, type,single)" .
			" values" .
			" (%s, %s, %d, %s, %s,%d)", 
			RC_Format::TextToSql($name), 
			RC_Format::TextToSql($description),
			$display_order,
			RC_Format::TextToSql($capabilityName),
			RC_Format::TextToSql($type),
            $single_post           
		);
		
		$wpdb->query($sql);
		$customWritePanelId = $wpdb->insert_id;
		
		if (!isset($categories))
			$categories = array();
		foreach ($categories as $cat_id)
		{
			$sql = sprintf(
				"INSERT INTO " . RC_CWP_TABLE_PANEL_CATEGORY .
				" (panel_id, cat_id)" .
				" values (%d, %d)",
				$customWritePanelId,
				$cat_id 
				);
			$wpdb->query($sql);
		}
		
		if (!isset($standardFields))
			$standardFields = array();
		foreach ($standardFields as $standard_field_id)
		{
			$sql = sprintf(
				"INSERT INTO " . RC_CWP_TABLE_PANEL_STANDARD_FIELD .
				" (panel_id, standard_field_id)" .
				" values (%d, %d)",
				$customWritePanelId,
				$standard_field_id 
				);
			$wpdb->query($sql);
		}
				
		// Create default group
		if ($createDefaultGroup){
			include_once('RCCWP_CustomGroup.php');
			RCCWP_CustomGroup::Create($customWritePanelId, '__default', false, false);
		}
		
		if($default_theme_page){
			$theme_key="t_".$name;
			$sql = "INSERT INTO ". $wpdb->postmeta .
								" (meta_key, meta_value) ".
								" VALUES ('".$theme_key."', '".$default_theme_page."')";
			$wpdb->query($sql);
			
			
		}
		
		RCCWP_CustomWritePanel::AssignToRole($customWritePanelId, 'administrator');
		
		return $customWritePanelId;
	}
	
	/**
	 * Delete a write panel without deleting its modules
	 *
	 * @param integer $customWritePanelId write panel id
	 */
	function Delete($customWritePanelId = null)
	{
		if (isset($customWritePanelId))
		{
			global $wpdb;
			
			$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
					  	
  			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANELS .
				" WHERE id = %d",
				$customWritePanel->id
				);
			$wpdb->query($sql);
			
			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANEL_CATEGORY .
				" WHERE panel_id = %d",
				$customWritePanel->id
				);
			$wpdb->query($sql);
			
			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANEL_STANDARD_FIELD .
				" WHERE panel_id = %d",
				$customWritePanelId
				);
			$wpdb->query($sql);
			
		}
	}
	
	/**
	 * Get the properties of a write panel
	 *
	 * @param unknown_type $customWritePanelId
	 * @return an object containing the properties of the write panel which are
	 * 			id, name, description, display_order, capability_name, type
	 */
	function Get($customWritePanelId)
	{
		global $wpdb;
	
		$sql = "SELECT id, name, description, display_order, capability_name, type,single FROM " . RC_CWP_TABLE_PANELS .
			" WHERE id = " . (int)$customWritePanelId;
		
		$results = $wpdb->get_row($sql);
		
		return $results;
	}
	
	/**
	 * Get the properties of a write panel
	 *
	 * @param unknown_type $customWritePanelId
	 * @return an object containing the properties of the write panel which are
	 * 			id, name, description, display_order, capability_name, type
	 */
	function GetThemePage($customWritePanelName)
	{
		global $wpdb;
	
		$sql = "SELECT meta_value FROM " . $wpdb->postmeta . 
						" WHERE meta_key = 't_".$customWritePanelName."' AND post_id = 0" ;
		
		$results = $wpdb->get_row($sql);
		
		return $results->meta_value;
	}
	
	/**
	 * Get a list of the ids of teh categories assigned to  a write panel
	 *
	 * @param integer $customWritePanelId write panel id
	 * @return array of ids
	 */
	function GetAssignedCategoryIds($customWritePanelId)
	{
		$results = RCCWP_CustomWritePanel::GetAssignedCategories($customWritePanelId);
		$ids = array();
		foreach ($results as $r)
		{
			$ids[] = $r->cat_id;
		}
		
		return $ids;
	}
	
	/**
	 * Get a list of categories assigned to a write panel
	 *
	 * @param integer $customWritePanelId write panel id
	 * @return array of objects, each object contains cat_id and cat_name 
	 */
	function GetAssignedCategories($customWritePanelId)
	{
		global $wpdb;
		/*
			$sql = "SELECT rc.cat_id, cat_name FROM " . RC_CWP_TABLE_PANEL_CATEGORY . 
			" rc JOIN $wpdb->categories wp ON rc.cat_ID = wp.cat_ID" . 
			" WHERE panel_id = " . $customWritePanelId;
		*/
		
		if( $wpdb->terms != '' )
		{
			$sql = "SELECT rc.cat_id, wp.name AS cat_name FROM " . 
				RC_CWP_TABLE_PANEL_CATEGORY . "
				rc JOIN $wpdb->terms wp ON rc.cat_ID = wp.term_id" . "
				WHERE panel_id = " . $customWritePanelId;
		}
		else
		{
			$sql = "SELECT rc.cat_id, cat_name FROM " . 
				RC_CWP_TABLE_PANEL_CATEGORY . "
				rc JOIN $wpdb->categories wp ON rc.cat_ID = wp.cat_ID 
				WHERE panel_id = " . $customWritePanelId;
		}
		

		$results = $wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		
		return $results;
	}
	
	/**
	 * Create a capability name for a write panel given its name. This function is 
	 * copied from WP's sanitize_title_with_dashes($title) (formatting.php)
	 *
	 * @param string $customWritePanelName panel name
	 * @return string capability name
	 */
	function GetCapabilityName($customWritePanelName)
	{
		// copied from WP's sanitize_title_with_dashes($title) (formatting.php)
		$capabilityName = strip_tags($customWritePanelName);
		// Preserve escaped octets.
		$capabilityName = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $capabilityName);
		// Remove percent signs that are not part of an octet.
		$capabilityName = str_replace('%', '', $capabilityName);
		// Restore octets.
		$capabilityName = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $capabilityName);
   
		$capabilityName = remove_accents($capabilityName);
		if (seems_utf8($capabilityName)) 
		{
			if (function_exists('mb_strtolower')) 
			{
				$capabilityName = mb_strtolower($capabilityName, 'UTF-8');
			}
			$capabilityName = utf8_uri_encode($capabilityName, 200);
		}
   
		$capabilityName = strtolower($capabilityName);
		$capabilityName = preg_replace('/&.+?;/', '', $capabilityName); // kill entities
		$capabilityName = preg_replace('/[^%a-z0-9 _-]/', '', $capabilityName);
		$capabilityName = preg_replace('/\s+/', '_', $capabilityName);
		$capabilityName = preg_replace('|-+|', '_', $capabilityName);
		$capabilityName = trim($capabilityName, '_');
   
		return $capabilityName;
	}



		

	/**
	 * Get a list of the standard fields of a the write panel
	 *
	 * @param integer $customWritePanelId panel id
	 * @return array of ids of the standard fields (see $STANDARD_FIELDS defined in RCCWP_Constant.php) 
	 */
	function GetStandardFields($customWritePanelId)
	{
		global $wpdb;
		$sql = "SELECT standard_field_id FROM " . RC_CWP_TABLE_PANEL_STANDARD_FIELD . 
				" WHERE panel_id = " . $customWritePanelId;
		$results = $wpdb->get_col($sql);
		if (!isset($results))
			$results = array();
		
		return $results;
	}
	
	/**
	 * Updates the properties of a write panel
	 *
	 * @param integer $customWritePanelId panel id
	 * @param string $name write panel name
	 * @param string $description write panel description
	 * @param array $standardFields a list of standard fields ids that are to be displayed in 
	 * 							in the panel. Use $STANDARD_FIELDS defined in RCCWP_Constant.php
	 * @param array $categories array of category ids that are checked by default when the user
	 * 							opens Write tab for that panel.
	 * @param integer $display_order the order of the panel in Flutter > Write Panels tab
	 * @param string $type 'post' or 'page'
	 */
	function Update($customWritePanelId, $name, $description = '', $standardFields = array(), $categories = array(), $display_order = 1, $type = FALSE, $createDefaultGroup=true,$single_post = 0, $default_theme_page)
	{
		include_once('RC_Format.php');
		global $wpdb;
		
		$capabilityName = RCCWP_CustomWritePanel::GetCapabilityName($name);
	
		$sql = sprintf(
			"UPDATE " . RC_CWP_TABLE_PANELS .
			" SET name = %s" .
			" , description = %s" .
			" , display_order = %d" .
			" , capability_name = %s" .
			" , type = %s" .
			" , single = %s" .
			" where id = %d",
			RC_Format::TextToSql($name), 
			RC_Format::TextToSql($description),
			$display_order,
			RC_Format::TextToSql($capabilityName),
			RC_Format::TextToSql($_POST['radPostPage']),
			$single_post,
			$customWritePanelId );
		
		$wpdb->query($sql);
		
		if (!isset($categories) || empty($categories))
		{
			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANEL_CATEGORY .
				" WHERE panel_id = %d",
				$customWritePanelId
				);
			
			$wpdb->query($sql);
		}
		else
		{
			$currentCategoryIds = array();
			$currentCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($customWritePanelId);
			
			$keepCategoryIds = array_intersect($currentCategoryIds, $categories);
			$deleteCategoryIds = array_diff($currentCategoryIds, $keepCategoryIds);
			$insertCategoryIds = array_diff($categories, $keepCategoryIds);
			
			foreach ($insertCategoryIds as $cat_id)
			{
				$sql = sprintf(
					"INSERT INTO " . RC_CWP_TABLE_PANEL_CATEGORY .
					" (panel_id, cat_id)" .
					" values (%d, %d)",
					$customWritePanelId,
					$cat_id 
					);
				$wpdb->query($sql);
			}
			
			if (!empty($deleteCategoryIds))
			{
				$sql = sprintf(
					"DELETE FROM " . RC_CWP_TABLE_PANEL_CATEGORY .
					" WHERE panel_id = %d" .
					" AND cat_id IN (%s)",
					$customWritePanelId,
					implode(',', $deleteCategoryIds)
					);
				
				$wpdb->query($sql);
			}
		}
		
		if (!isset($standardFields) || empty($standardFields))
		{			
			$sql = sprintf(
				"DELETE FROM " . RC_CWP_TABLE_PANEL_STANDARD_FIELD .
				" WHERE panel_id = %d",
				$customWritePanelId
				);
			$wpdb->query($sql);
		}
		else
		{
			$currentStandardFieldIds = array();
			$currentStandardFieldIds = RCCWP_CustomWritePanel::GetStandardFields($customWritePanelId);
			
			$keepStandardFieldIds = array_intersect($currentStandardFieldIds, $standardFields);
			$deleteStandardFieldIds = array_diff($currentStandardFieldIds, $keepStandardFieldIds);
			$insertStandardFieldIds = array_diff($standardFields, $keepStandardFieldIds);
			
			foreach ($insertStandardFieldIds as $standard_field_id)
			{
				$sql = sprintf(
					"INSERT INTO " . RC_CWP_TABLE_PANEL_STANDARD_FIELD .
					" (panel_id, standard_field_id)" .
					" values (%d, %d)",
					$customWritePanelId,
					$standard_field_id 
					);
				$wpdb->query($sql);
			}
			
			if (!empty($deleteStandardFieldIds))
			{
				$sql = sprintf(
					"DELETE FROM " . RC_CWP_TABLE_PANEL_STANDARD_FIELD .
					" WHERE panel_id = %d" .
					" AND standard_field_id IN (%s)",
					$customWritePanelId,
					implode(',', $deleteStandardFieldIds)
					);
				
				$wpdb->query($sql);
			}
		}
		
		if($default_theme_page){
			$theme_key="t_".$name;
			
			//check if exist template in postmeta
			$check_template ="SELECT meta_id FROM ".$wpdb->postmeta." WHERE meta_key='".$theme_key."' ";
			$query_template= $wpdb->query($check_template);
			
			if($query_template){
				$sql = "UPDATE ". $wpdb->postmeta .
					" SET meta_value = '".$default_theme_page."' ".
					" WHERE meta_key = '".$theme_key."' AND post_id = '0' ";
			}else{
				$sql = "INSERT INTO ". $wpdb->postmeta .
								" (meta_key, meta_value) ".
								" VALUES ('".$theme_key."', '".$default_theme_page."')";				
			}
			$wpdb->query($sql);
			
			
		}
	
	}
	
	/**
	 * Retrieves the groups of a module
	 *
	 * @param integer $customWriteModuleId module id
	 * @return array of objects representing basic information of the group, 
	 * 				each object contains id, name and module_id   
	 */
	function GetCustomGroups($customWritePanelId)
	{
		global $wpdb;
		$sql = "SELECT * FROM " . RC_CWP_TABLE_PANEL_GROUPS .
			" WHERE panel_id = " . $customWritePanelId .
			" ORDER BY name";

		$results =$wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		
		return $results;
	}
	
	
	/**
	 * Import a write panel given the file path.
	 * @param string $panelFilePath the full path of the panel file
	 * @param string $writePanelName the write panel name, if this value if false, the function will
	 * 							use the pnl filename as the write panel name. The default value is false
	 * @return the panel id, or false in case of error.
	 */
	function Import($panelFilePath, $writePanelName = false)
	{
		include_once('RCCWP_CustomGroup.php');
		include_once('RCCWP_CustomField.php');
		include_once('RCCWP_Application.php');

		if (!$writePanelName)
			//use filename
			$writePanelName = basename($panelFilePath, ".pnl");

		if ($writePanelName == '') return false;

		// Append a number if the panel already exists,
		$i = 1;
		$newWritePanelName = $writePanelName;
		//while (file_exists(FLUTTER_MODULES_DIR.$newModuleName)){
		//	$newModuleName = $moduleName. "_" . $i++;
		//}
		//$moduleName = $newModuleName;

		// Unserialize file
		$imported_data = unserialize(file_get_contents($panelFilePath));
		$types_results = RCCWP_CustomField::GetCustomFieldTypes();
		$types = array();
		foreach($types_results as $types_result){
			$types[$types_result->name] = $types_result->id;
		}
		
		// Prepare categories list
		$assignedCategories = array();
		foreach($imported_data['panel']->assignedCategories as $cat_name){
			$assignedCategories[] = wp_create_category($cat_name);
		}
		
		//Create write panel
		$writePanelID = RCCWP_CustomWritePanel::Create($writePanelName, $imported_data['panel']->description, $imported_data['panel']->standardFieldsIDs, $assignedCategories,$imported_data['panel']->display_order, $imported_data['panel']->type, false,$imported_data['panel']->single,$imported_data['panel']->theme);
		
		foreach($imported_data['fields'] as $groupName => $group){
			// For backward compatability
			if (!isset($group->fields)) {
				$newGroup->fields = $group;
				$group = $newGroup; 
			}
			
			// Import group
			$groupID = RCCWP_CustomGroup::Create($writePanelID, $groupName, $group->duplicate, $group->at_right);
			
			// Import group fields
			foreach ($group->fields as $field){
				$fieldOptions = @implode("\n", $field->options);
				$fieldDefault = @implode("\n", $field->default_value);
				RCCWP_CustomField::Create($groupID, $field->name, $field->description, $field->display_order, $field->required_field, $types[$field->type], $fieldOptions, $fieldDefault, $field->properties, $field->duplicate);
			}
		}


		return $writePanelID;
	}
	

	/**
	 * Export a write panel to file
	 *
	 * @param integer $panelID
	 * @param string $exportedFilename the full path of the file to which the panel will be exported
	 */
	function Export($panelID, $exportedFilename){
		include_once('RCCWP_CustomWriteModule.php');
		include_once('RCCWP_CustomGroup.php');
		include_once('RCCWP_CustomField.php');
	
		$exported_data = array();

		$writePanel = RCCWP_CustomWritePanel::Get($panelID);
		$writePanel->standardFieldsIDs = RCCWP_CustomWritePanel::GetStandardFields($panelID);
		$writePanel->assignedCategories = array();
		$writePanel->theme = RCCWP_CustomWritePanel::GetThemePage($writePanel->name);
				
		$assignedCategories = RCCWP_CustomWritePanel::GetAssignedCategories($panelID);
		foreach($assignedCategories as $assignedCategory){
			$writePanel->assignedCategories[] = $assignedCategory->cat_name;
		}
		
		$moduleGroups = RCCWP_CustomWritePanel::GetCustomGroups($panelID);
		foreach( $moduleGroups as $moduleGroup){
			$groupFields[$moduleGroup->name]->fields = RCCWP_CustomGroup::GetCustomFields($moduleGroup->id);
 			$groupFields[$moduleGroup->name]->duplicate = $moduleGroup->duplicate;
			$groupFields[$moduleGroup->name]->at_right = $moduleGroup->at_right;
		}

		$exported_data['panel'] = $writePanel;
		$exported_data['fields'] = $groupFields;
		
		$handle = fopen($exportedFilename, "w");
		$result = fwrite($handle, serialize($exported_data));
		@fclose($handle);
	}
	
	
}
?>
