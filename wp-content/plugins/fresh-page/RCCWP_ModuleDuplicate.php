<?php

/**
 * Create/edit/delete module duplicates 
 *
 */

class RCCWP_ModuleDuplicate
{
	/**
	 * Get all module duplicates
	 *
	 * @param integer $modulelId
	 * @return array of objects containing module duplicates
	 */	
	function GetCustomModulesDuplicates($modulelId)
	{
		global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM ".FLUTTER_TABLE_MODULES_DUPLICATES." WHERE module_id = '$modulelId'");

		return $results;
	}
	
	
	
	/**
	 * Create a duplicate. 
	 *
	 * @param integer $customWriteModuleId
	 * @param string $duplicate_name the name of the duplicate, if false,
	 *           the name "[MODULE NAME] copy [x] will be given to the duplicate. 
	 */ 
	function Create($customWriteModuleId, $duplicate_name=false)
	{
		global $wpdb;
		
		// Get module name
		$customModule = RCCWP_CustomWriteModule::Get($customWriteModuleId);
		
		if (!$duplicate_name){
			// go ahead and rename, then duplicate
			$duplicate_name = $customModule->name;	
			
			if($other_blocks = $wpdb->get_results("SELECT duplicate_name FROM ".FLUTTER_TABLE_MODULES_DUPLICATES." WHERE duplicate_name LIKE '".preg_replace('/\scopy\s[0-9]*/', '', $duplicate_name)." %' ORDER BY duplicate_id DESC")) {
				$duplicate_name = $other_blocks[0]->duplicate_name;
				$testcase = substr($duplicate_name, -1, 1);
				$duplicate_name[strlen($duplicate_name) - 1] = intval($testcase) + 1;
			}
			else
				$duplicate_name .= ' copy 2';
		}
				
		$wpdb->query("INSERT INTO ".FLUTTER_TABLE_MODULES_DUPLICATES." (module_id, duplicate_name) VALUES ($customWriteModuleId, '$duplicate_name')");
        FlutterLayoutBlock::UpdateAllModulesSettings();

		return $wpdb->insert_id;
	}
	
	/**
	 * Delete duplicate
	 *
	 * @param integer $duplicateId
	 */
	function Delete($duplicateId)
	{
		global $wpdb;
			
		$sql = sprintf(
			"DELETE FROM " . FLUTTER_TABLE_MODULES_DUPLICATES .
			" WHERE duplicate_id = %d",
			$duplicateId
			);
		$wpdb->query($sql);
	}
	
	/**
	 * Get duplicate
	 *
	 * @param integer $duplicateId
	 * @return an object containing the duplicate properties
	 */
	function Get($duplicateId){
		global $wpdb;

		$results = $wpdb->get_row("SELECT * FROM ".FLUTTER_TABLE_MODULES_DUPLICATES ." WHERE duplicate_id = $duplicateId");
		return $results;
	}

	/**
	 * Update duplicate
	 *
	 * @param integer $duplicateId
	 * @param string $name new duplicate name
	 */
	function Update($duplicateId, $name){
		global $wpdb;
		
		$sql = sprintf(
			"UPDATE " . FLUTTER_TABLE_MODULES_DUPLICATES .
			" SET duplicate_name = '%s'".
			" where duplicate_id = %d",
			$name,
			$duplicateId);
		
		$wpdb->query($sql);
	}

}
?>
