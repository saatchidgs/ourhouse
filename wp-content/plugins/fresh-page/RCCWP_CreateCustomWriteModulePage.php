<?php
include_once('RCCWP_CustomWriteModulePage.php');
class RCCWP_CreateCustomWriteModulePage
{
	function Main()
	{
		global $flutter_domain;
		?>

		<div class="wrap">

		<h2><?php _e('Create Custom Write Module', $flutter_domain); ?></h2>
		
		<form action="?page=FlutterManageModules&view-modules=1" method="post" id="create-new-write-module-form">
		
		<?php RCCWP_CustomWriteModulePage::Content(); ?>
		
		<p class="submit" >
			<input name="cancel-create-custom-write-module" type="submit" id="cancel-create-custom-write-module" value="<?php _e('Cancel', $flutter_domain); ?>" /> 
			<input name="finish-create-custom-write-module" type="submit" id="finish-create-custom-write-module" value="<?php _e('Finish', $flutter_domain); ?>" />
		</p>
		
		</form>

		</div>

		<?php
	}
}
?>