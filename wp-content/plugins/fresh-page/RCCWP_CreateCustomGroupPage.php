<?php
include_once('RCCWP_CustomGroupPage.php');
class RCCWP_CreateCustomGroupPage
{
	function Main()
	{
		global $flutter_domain;
		?>

		<div class="wrap">

		<h2><?php _e('Create Custom Group', $flutter_domain); ?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('finish-create-custom-group')?>" method="post" id="create-new-group-form">
		
		<?php RCCWP_CustomGroupPage::Content(); ?>
		
		<p class="submit" >
			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-group')?>" class="button"><?php _e('Cancel', $flutter_domain); ?></a> 
			<input type="submit" id="finish-create-custom-group" value="<?php _e('Finish', $flutter_domain); ?>" />
		</p>
		
		</form>

		</div>

		<?php
	}
}
?>