<?php
include_once('RCCWP_CustomWriteModulePage.php');


class RCCWP_CreatePanelModulePage
{
	function Main()
	{
		global $flutter_domain;
		$modules = RCCWP_CustomWriteModule::GetCustomModules();

		if(isset($_GET['custom-write-panel-id']) && !empty($_GET['custom-write-panel-id']) )
			$customWritePanelId = (int)$_GET['custom-write-panel-id'];
		if(isset($_POST['custom-write-panel-id']) && !empty($_POST['custom-write-panel-id']) )
			$customWritePanelId = (int)$_POST['custom-write-panel-id'];
		?>

		<div class="wrap">

		<h2><?php _e('Add Module',$flutter_domain); ?></h2>
		
		<form action="" method="post" id="add-new-module-form">
		
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name',$flutter_domain); ?>:</th>
			<td>
		
				<input type="hidden" name="custom-write-panel-id" value="<?php echo $customWritePanelId?>" />
				<select tabindex="3" name="custom-write-module-id"  id="custom-write-module-id">
					<?php
					foreach ($modules as $module) :
					?>
					
						<option value="<?php echo $module->id?>"><?php echo $module->name?></option>
					
					<?php
					endforeach;
					?>
				</select>
			</td>
		</tr>
		</tbody>
		</table>
		
		<p class="submit" >
			<input name="cancel-add-module" type="submit" id="cancel-add-module" value="<?php _e('Cancel',$flutter_domain); ?>" /> 
			<input name="finish-add-module" type="submit" id="finish-add-module" value="<?php _e('Finish',$flutter_domain); ?>" />
		</p>
		
		</form>

		</div>

		<?php
	}
}
?>