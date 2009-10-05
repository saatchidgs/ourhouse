<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );

global $flutter_domain;
if (!(is_user_logged_in() && current_user_can(FLUTTER_CAPABILITY_MODULES)))
	die(__('Athentication failed!',$flutter_domain));
	
require_once('RCCWP_CustomWriteModule.php');
require_once('RCCWP_CustomWritePanel.php');
require_once('RCCWP_Application.php');
require_once('RCCWP_CustomWritePanel.php');

$moduleID = (int)$_REQUEST['custom-write-module-id'];
$module = RCCWP_CustomWriteModule::Get($moduleID);

if (isset($_POST["write_panels"])){
	
	//$write_panels = json_decode(stripslashes($_POST["write_panels"]));
	
	$modulePath = FLUTTER_MODULES_DIR.$module->name.DIRECTORY_SEPARATOR;
	$tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR;
	
	
	// Copy dir to tmp folder
	dircopy($modulePath, $tmpPath. $module->name);
	$moduleTmpPath = "$tmpPath{$module->name}";
	chmod_R($moduleTmpPath, 0777);
	
	// Export write panels
	//check if arrary the write modules is empty
	if($_POST["write_panels"] != NULL){
		$write_panels = split(",",$_POST["write_panels"]);
		foreach($write_panels as $panelID){
			$writePanel = RCCWP_CustomWritePanel::Get($panelID);
			$exportedFilename = $moduleTmpPath.DIRECTORY_SEPARATOR. '_'.$writePanel->name . '.pnl';
			RCCWP_CustomWritePanel::Export($panelID, $exportedFilename);
		}
	}
	
	// Export duplicates and description
	$moduleInfoFilename = $moduleTmpPath.DIRECTORY_SEPARATOR.'module_info.exp';
	$moduleInfo_exported_data['duplicates'] = RCCWP_ModuleDuplicate::GetCustomModulesDuplicates($moduleID);
	$moduleInfo_exported_data['moduleinfo'] = RCCWP_CustomWriteModule::Get($moduleID); 
	$handle = fopen($moduleInfoFilename, "w");
	$result = @fwrite($handle, serialize($moduleInfo_exported_data));
	@fclose($handle);
	
	// -- Create zip file
	$zipFile = "$tmpPath{$module->name}.zip";
	chdir($moduleTmpPath.DIRECTORY_SEPARATOR);
	if (RCCWP_Application::CheckCompressionProgramZip()) 
		$command = "zip -r $zipFile  ./*"; 
	else{
		_e('Cannot find zip program',$flutter_domain);
		return;
	}
	exec($command, $out, $err);
	
	
	// send file in header
	header('Content-type: binary');
	header('Content-Disposition: attachment; filename="'.$module->name.'.zip"');
	readfile($zipFile);
	
	// Remove file and directory
	unlink($zipFile);
	advancedRmdir($moduleTmpPath);
	exit();
}

$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/MarkUp/DTD/xhtml-basic11.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<title><?php _e('Export Module', $flutter_domain); ?></title>
	<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/css/global.css' type='text/css' />
	<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/wp-admin.css' type='text/css' />
	<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/css/colors-fresh.css' type='text/css' />
	
	<script type="text/javascript">
		function startExport(){
		
			// Collect write panels
			var write_panels = []; 
			var write_panels_elements = document.export_module_form.elements["write_panels[]"];
			if (write_panels_elements != undefined){
				if (write_panels_elements.length != undefined){
					for(i=0;i<write_panels_elements.length;i++)
					{
						if (write_panels_elements[i].checked)
							write_panels.push(write_panels_elements[i].value);
					}
				}
				else
				{
					if (write_panels_elements.checked)
						write_panels.push(write_panels_elements.value);
				}
			}
			//alert(write_panels);
			// Submit data through a hidden form in the parent window
			var par = window.parent.document;
			par.forms['do_export'].elements["custom-write-module-id"].value= "<?php echo $moduleID ?>";
			//par.forms['do_export'].elements["write_panels"].value= self.parent.Object.toJSON(write_panels);
			par.forms['do_export'].elements["write_panels"].value= write_panels;
			par.forms['do_export'].submit();
			
			self.parent.tb_remove();
			return true;
			
		}
		
	</script>
	
</head>
<body>

<div class="wrap">

<h2><?php _e('Export', $flutter_domain); ?> <?php echo $module->name ?></h2>
		
<form action="" method="post" name="export_module_form" id="export-module-form" >
	<strong> <?php _e('If you want to export Write Panels along with the module, check them below:',$flutter_domain)?> </strong>
	<ul>
	<?php
	foreach ($customWritePanels as $panel) :
	?>
		<li>
		<input type="checkbox" name="write_panels[]" value="<?php echo $panel->id?>">&nbsp;<?php echo $panel->name;?></input>
		</li>
	<?php
	endforeach;
	?>
	</ul>
	<p class="submit" > 
		<input type="button" name="submit-export-module" id="submit-export-module" value="<?php _e('Export',$flutter_domain); ?>" onclick="startExport()" />
	</p>
	
</form>


</div>

</body>
</html>
	