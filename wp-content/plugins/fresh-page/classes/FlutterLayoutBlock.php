<?php

class FlutterLayoutBlock{
	
	var $variables;
	
	function FlutterLayoutBlock(){
		$this->variables = array();
	}

	function GetModuleSettings($moduleID, $templateName=false){
		global $wpdb;
		if (!$templateName) $templateName = get_option('template');
		
		// Load settings
		$blockID = $wpdb->get_var(
						" SELECT block_id ".
						" FROM ".FLUTTER_TABLE_LAYOUT_MODULES.
						" WHERE theme = '".$templateName."' AND module_id = $moduleID");
		
		if (empty($blockID)) return false;
		return FlutterLayoutBlock::GetModuleSettingsByBlock($blockID, $templateName);
		
	}
	
	function GetModuleSettingsByBlock($blockID, $templateName=false){
		global $wpdb;
		if (!$templateName) $templateName = get_option('template');
		
		$moduleSettings = new FlutterLayoutBlock();
		
		// Load settings
		$moduleSettingsInfo = $wpdb->get_row(
						" SELECT * FROM ".FLUTTER_TABLE_LAYOUT_MODULES.
						" WHERE block_id = $blockID ");
		if (!$moduleSettingsInfo) return false;

		$moduleSettingsInfo->module_name = $wpdb->get_var(
						" SELECT name".
						" FROM ".RC_CWP_TABLE_MODULES.
						" WHERE id=".$moduleSettingsInfo->module_id);

		if ($moduleSettingsInfo->duplicate_id > 0 )
			$moduleSettingsInfo->title = $wpdb->get_var("SELECT duplicate_name FROM ".FLUTTER_TABLE_MODULES_DUPLICATES." WHERE duplicate_id = '$moduleSettingsInfo->duplicate_id'");
		else
			$moduleSettingsInfo->title = $moduleSettingsInfo->module_name;
		
		
		$moduleSettingsID = $moduleSettingsInfo->block_id;

		// Load Variables
		$moduleVariables = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE parent = '$moduleSettingsID' ORDER BY variable_id");
		foreach($moduleSettingsInfo as $key => $val){
			$moduleSettings->{$key} = $val;
		}
		if (empty($moduleVariables)) $moduleVariables = array();
		$moduleSettings->variables = $moduleVariables;
		
		// Load Variables options		
		foreach($moduleSettings->variables as $varKey => $variable) {
			$moduleSettings->variables[$varKey]->options = unserialize($moduleSettings->variables[$varKey]->options);
			if (!is_array($moduleSettings->variables[$varKey]->options)) $moduleSettings->variables[$varKey]->options = array();
			if (isset($moduleSettings->variables[$varKey]->options['dbvalue'])){
				$moduleSettings->variables[$varKey]->options = FlutterLayoutBlock::GenerateOptionsFromDatabase(trim($moduleSettings->variables[$varKey]->options['dbvalue']), trim(stripslashes($moduleSettings->variables[$varKey]->options['query'])));
			}			
			
			switch($moduleSettings->variables[$varKey]->type){
				case 'checkbox_list':
				case 'listbox' :
					$moduleSettings->variables[$varKey]->value = unserialize($moduleSettings->variables[$varKey]->value);
					break;
			}
		}
		
		
		return $moduleSettings;
		
	}
	
	function SaveValues(){
		global $wpdb;
		foreach($this->variables as $varKey => $variable) {
			if (is_array($variable->value)){
				$varValue = serialize($variable->value);
			}
			else
				$varValue = $variable->value;
				
			$varValue = addslashes($varValue);
			$variableID = $variable->variable_id;
			$wpdb->query("UPDATE ".FLUTTER_TABLE_LAYOUT_VARIABLES.
							" SET value = '$varValue'". 
							" WHERE variable_id = '$variableID' ");
		}
	}
	
	function SaveSettings(){
		global $wpdb;
		
		$wpdb->query("UPDATE ".FLUTTER_TABLE_LAYOUT_MODULES.
					 " SET position = '".$this->position."'".
					 " WHERE block_id = ".$this->block_id);
	}
	
	/**
	 * Processes the variables values to be suitable for direct use,
	 * e.g. adding full URL to uploaded files.
	 *
	 * @return array of variables where the key of each item is the variable name
	 */
	function GetProcessedVariables(){
		global $FIELD_TYPES;
		
		$vars = array();
		foreach($this->variables as $varKey => $variable) {
			$variable->properties['format'] = "m.d.y";
			$vars[$variable->variable_name] = GetProcessedFieldValue($variable->value, $FIELD_TYPES[$variable->type], $variable->properties);
		}
		
		return $vars;
	}
	
	function GetModuleTemplateFile(){
		
		if (empty($this->template_size)) $this->template_size = MODULE_TEMPLATE_SIZE_SMALL;
		if (empty($this->template_name)) $this->template_name = "default"; 
		
		switch($this->template_size){
			case MODULE_TEMPLATE_SIZE_SMALL:
				$template_size = "small";
				break;
			case MODULE_TEMPLATE_SIZE_MEDIUM:
				$template_size = "medium";
				break;
			case MODULE_TEMPLATE_SIZE_LARGE:
				$template_size = "large";
				break;
			case MODULE_TEMPLATE_SIZE_FULL:
				$template_size = "full";
				break;
		}
		
		$template_name = $this->template_name;
		$module_name = $this->module_name;
	
		return FLUTTER_MODULES_DIR."$module_name/templates/$template_name/$template_size/default.php";
	}
	
	//-----
	// Static functions
	//-----

	function GetModuleSettingsID($moduleID, $pageName='', $templateName=false) {
		global $wpdb;
		$moduleSettingsID = '';
		if (!$templateName) $templateName = get_option('template');

		$moduleSettingsID = $wpdb->get_var(
                                            "SELECT block_id FROM ".FLUTTER_TABLE_LAYOUT_MODULES.
                    						" WHERE theme = '$templateName' AND module_id = '$moduleID' AND page='$pageName'"
                                          );

		return 	$moduleSettingsID;
	}
	
	function DeleteModule($customWriteModuleId) {
		global $wpdb;
	
		if ($blocks = $wpdb->get_col("SELECT block_id FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE module_id = ".$customWriteModuleId)){
			foreach($blocks as $block_id){
				$wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE block_id = '$block_id'");
				$wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE parent = '$block_id'"); 
			}
		}
	}
	
	function DeleteDuplicate($duplicateId) {
		global $wpdb;
	
		if ($blocks = $wpdb->get_col("SELECT block_id FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE duplicate_id = ".$duplicateId)){
			foreach($blocks as $block_id){
				$wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE block_id = '$block_id'");
				$wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE parent = '$block_id'"); 
			}
		}
	}
	
	function UpdateAllModulesSettings(){
		global $wpdb;
		
		$customModules = RCCWP_CustomWriteModule::GetCustomModules();
		$currDuplicates = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_MODULES_DUPLICATES);
		
		
		$currModules = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_LAYOUT_MODULES);

		foreach($currModules as $currModule){
			
			// -- Delete obselete modules
			if ($currModule->module_id>-1){
				$found = false;
				foreach($customModules as $customModule){
					if ($customModule->id == $currModule->module_id) $found = true;
				}
				if (!$found){
					FlutterLayoutBlock::DeleteModule($currModule->module_id);
				}
			}

			// -- Delete obselete duplicates
			$found = false;
			foreach($currDuplicates as $currDuplicate){
				if ($currDuplicate->duplicate_id == $currModule->duplicate_id) $found = true;
			}
			if (!$found && $currModule->duplicate_id != 0){
				FlutterLayoutBlock::DeleteDuplicate($currModule->duplicate_id);
			}
		}

		// -- Insert/Update modules in the Layout table	
		foreach($customModules as $customModule){
			$filename = FLUTTER_MODULES_DIR.$customModule->name."/configure.xml";
			FlutterLayoutBlock::UpdateModuleSettings($filename, $customModule->id);
			
			//Insert duplicates
			$currDuplicates = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_MODULES_DUPLICATES." WHERE module_id = '$customModule->id'");
			foreach($currDuplicates as $currDuplicate){ 
				FlutterLayoutBlock::UpdateModuleSettings($filename, $customModule->id, false, false, $currDuplicate->duplicate_id);
			}
		}
		
	}
	
	function UpdateModuleSettings($filename, $moduleID, $pageName=false, $templateName=false, $duplicateID=0) {

		global $wpdb;
		
		$whereCond = " WHERE module_id = '$moduleID' ";
		if ($templateName) $whereCond .= " AND theme = '$templateName'";
		if ($pageName) $whereCond .= " AND page='$pageName'";
		if ($duplicateID>0) $whereCond .= " AND duplicate_id='$duplicateID'";
		
		//if (!$templateName) $templateName = get_option('template');
		
		if (!file_exists($filename)) return false;
		
		$themeSettings = new simplexml;
  		$themeSettings->xml_load_file($filename);
  		$themeSettings = $themeSettings->result;
  		
  		  		
  		// Add a module row
  		$themeSettingsIDs = $wpdb->get_col("SELECT block_id FROM ".FLUTTER_TABLE_LAYOUT_MODULES.
										  $whereCond);
  		
  		if (empty($themeSettingsIDs)){
  			if (!$templateName) $templateName = get_option('template');
  			if (!$pageName) $pageName = "index.php";
  			 		
	  		$wpdb->query("INSERT INTO ".FLUTTER_TABLE_LAYOUT_MODULES." (theme, module_id, page, duplicate_id) VALUES ('$templateName', '$moduleID', '$pageName', '$duplicateID')");
			$themeSettingsIDs[0] = $wpdb->insert_id;
  		}
  		
  		foreach($themeSettingsIDs as $themeSettingsID){
		
			// Import variables
		    if (!empty($themeSettings['variables'])){
					foreach($themeSettings['variables'][0]['variable'] as $variable) {
						
						// --- Add/Update Variable
						$name = trim(str_replace(array(' ','.'), '_', addslashes(array_pop($variable['name']))));
						$type = trim(array_pop($variable['type']));
						
						
						$description = trim(addslashes(array_pop($variable['description'])));
						
						// default value
						$default = $variable['default'];
						if (!empty($default)) {
							$default = array_pop($variable['default']);
							if ($type=='checkbox_list' || $type=='listbox'){
								$defaultArray = array();
								foreach($default['value'] as $value) {
									$defaultArray[] = trim($value);
								}
					           	$default = addslashes(serialize($defaultArray));
							}
							else{
								$default = trim(addslashes($default));	
							}
						}
						else
							$default = '';
						
						// options
						$options = $variable['options'];
						if (!empty($options)) {
							$options = array_pop($variable['options']);
							$optionsArray = array();
							if (isset($options['dbvalue'])){
								$optionsArray = $options;
							}
							else{
								foreach($options['value'] as $value) {
									$optionsArray[] = trim($value);
								}
							}
				           	$options = addslashes(serialize($optionsArray));
						}
						else
							$options = '';
						
						$variableID = $wpdb->get_var("SELECT variable_id FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE variable_name = '$name' AND parent = '$themeSettingsID'");
						if (empty($variableID)) {
							$wpdb->query("INSERT INTO ".FLUTTER_TABLE_LAYOUT_VARIABLES." (variable_name,parent,type,value,default_value,description,options) VALUES ('$name','$themeSettingsID','$type','$default','$default','$description','$options')");
							$variableID = $wpdb->insert_id;
						}else{
							$wpdb->query("UPDATE ".FLUTTER_TABLE_LAYOUT_VARIABLES.
								" SET type = '$type', default_value = '$default', description = '$description' , options='$options' ". 
								" WHERE variable_id = '$variableID' ");
						}					
						
					}
		    }
		    
		    // -- Delete obselete variables
			$blockVars = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE parent = '$themeSettingsID'");
			foreach($blockVars as $blockVar){
				$found = false;
		    	if (!empty($themeSettings['variables'])){
					foreach($themeSettings['variables'][0]['variable'] as $variable) {
						$name = trim(str_replace(array(' ','.'), '_', addslashes(array_pop($variable['name']))));
						if ($name == $blockVar->variable_name) $found = true;
					}
				}
				if (!$found){
					$wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE variable_id = ". $blockVar->variable_id);
				}
			}
  		}
  		
  		return $themeSettingsIDs;
				
	}

	// Processes variable Options that are defined as database query
	function GenerateOptionsFromDatabase($dbvalue, $where_cond = '') {
		global $wpdb, $canvas;
		$ALLOW_TABLES = array("sitecategories");
		preg_match('/(.*)\-\>(.*)\:(.*)/', $dbvalue, $matches);
	
		$content = '';
		if(!isset($matches[3])) $matches[3] = $matches[2];
		if ($where_cond != '') {
			$where_cond = str_replace('%prefix%', $wpdb->prefix, $where_cond);
			$where_cond = " WHERE " . $where_cond;
		}
	
	
		// We need to find out whether the table name requires user prefix or global prefix
		$req_table_prefix = $wpdb->prefix;
		if (!$wpdb->get_var("SHOW TABLES LIKE '".$req_table_prefix.$matches[1]."'") == $req_table_prefix.$matches[1]){
			if (in_array($matches[1], $ALLOW_TABLES))
				$req_table_prefix = (isset($wpdb->base_prefix)?$wpdb->base_prefix:$wpdb->prefix);
		}
		
		$result = array();
		if($wpdb->get_var("SHOW TABLES LIKE '".$req_table_prefix.$matches[1]."'") == $req_table_prefix.$matches[1]) {
			//echo("<br/>SELECT ".$matches[2].", ".$matches[3]." FROM ".$req_table_prefix.$matches[1]."  TBL ".$where_cond);
			if($data = $wpdb->get_results("SELECT ".$matches[2].", ".$matches[3]." FROM ".$req_table_prefix.$matches[1]."  TBL ".$where_cond)) {
				$counter = 0;
				foreach($data as $row) {
					$result[$counter]->value = $row->$matches[3];
					$result[$counter]->label = $row->$matches[2];
					$counter++; 
				}
			}
		}
		
		return $result;
	}
	
	
	function GetModules($pageName, $position='', $templateName=false){
		global $wpdb;
		
		if (!$templateName) $templateName = get_option('template');
		
		$blocksID = $wpdb->get_col(
				" SELECT block_id ".
				" FROM ".FLUTTER_TABLE_LAYOUT_MODULES.
				" WHERE theme = '$templateName' AND position = '$position' AND page='$pageName'");
		
		$resutls = array();
		foreach($blocksID as $blockID){
			$resutls[] = FlutterLayoutBlock::GetModuleSettingsByBlock($blockID, $templateName);
		}
		
		return $resutls;
	}
}





?>
