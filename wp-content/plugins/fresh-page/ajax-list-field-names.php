<?

require( dirname(__FILE__) . '/../../../wp-config.php' );
if (!(is_user_logged_in()))
	die("Athentication failed!");

if(isset($_GET['getFieldsByLetters']) && isset($_GET['letters'])){
	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	
    // Search for similar fields
	global $wpdb;
	global $flutter_domain;
	require_once("RCCWP_CustomField.php");
	$sql = "SELECT id, group_id FROM " . RC_CWP_TABLE_GROUP_FIELDS .
		" WHERE name like '%$letters%' "; 
	$results =$wpdb->get_results($sql);
	
	foreach($results as $result){
		$fieldGroup = RCCWP_CustomGroup::Get($result->group_id);
		if ($_GET['panel_id'] != $fieldGroup->panel_id){
			$currentField = RCCWP_CustomField::Get($result->id);
			$fieldDescription  = "<b>{$currentField->description}</b> <br />&nbsp;&nbsp; (";
			$fieldDescription .= __("Type", $flutter_domain).":";
			$fieldDescription .= " {$currentField->type}, ";
			$fieldDescription .= __("Name", $flutter_domain).":";
			$fieldDescription .= " {$currentField->name})";
			echo $result->id."###".$fieldDescription."|";
		}
	}
}
?>
