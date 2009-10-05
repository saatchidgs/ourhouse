<?php
class RCCWP_CustomFieldPage
{
	function Edit()
	{
		global $FIELD_TYPES;
		global $flutter_domain;
		$custom_field = RCCWP_CustomField::Get((int)$_GET['custom-field-id']);
		$customGroupID = $custom_field->group_id;	
		
		if (in_array($custom_field->type, array('Image'))) $cssVlaue = $custom_field->CSS;
		
  		?>
	  	
  		<div class="wrap">
  		<h2><?php _e('Edit Custom Field',$flutter_domain); ?> - <?php echo $custom_field->description ?></h2>
  		
  		<br class="clear" />
  		<?php
		if (isset($_GET['err_msg'])) :
			switch ($_GET['err_msg']){
				case -1:
				?>
				<div class="error"><p> <?php _e('A field with the same name already exists in this write panel. Please choose a different name.',$flutter_domain); ?></p></div>
				<?php
				}
		endif;
		?>
  		
	  	
  		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-field')."&custom-group-id=$customGroupID"?>" method="post" id="edit-custom-field-form"  onsubmit="return checkEmpty();">
  		<input type="hidden" name="custom-field-id" value="<?php echo $custom_field->id?>">
		
		
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Name',$flutter_domain); ?>:</th>
			<td><input name="custom-field-name" id="custom-field-name" size="40" type="text" value="<?php echo htmlspecialchars($custom_field->name)?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Label',$flutter_domain); ?>:</th>
			<td><input name="custom-field-description" id="custom-field-description" size="40" type="text" value="<?php echo htmlspecialchars($custom_field->description)?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e('Can be duplicated',$flutter_domain); ?>:</th>
			<td><input name="custom-field-duplicate" id="custom-field-duplicate" type="checkbox" value="1" <?php echo $custom_field->duplicate==0 ? "":"checked" ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Order',$flutter_domain); ?>:</th>
			<td>
				<input name="custom-field-order" id="custom-field-order" size="2" type="text" value="<?php echo $custom_field->display_order?>" />
			</td>	
		</tr>
		
		<?php if (in_array($custom_field->type_id, 
							array(  $FIELD_TYPES['textbox'],
									$FIELD_TYPES['multiline_textbox'],
									$FIELD_TYPES['dropdown_list'],
									$FIELD_TYPES['listbox'],
									$FIELD_TYPES['file'],
									$FIELD_TYPES['image'],
									$FIELD_TYPES['audio']
							))){  ?>
		<tr valign="top">
			<th scope="row"><?php _e('Required',$flutter_domain); ?>:</th>
			<td>
				<select name="custom-field-required" id="custom-field-required">
					<option value="0" <?php echo ($custom_field->required_field == 0 ? 'selected="selected"' : ''); ?> ><?php _e('Not Required - can be empty',$flutter_domain); ?></option>
					<option value="1" <?php echo ($custom_field->required_field == 1 ? 'selected="selected"' : ''); ?> ><?php _e('Required - can not be empty',$flutter_domain); ?></option>
				</select>
			</td>	
		</tr>
		
		<?php } ?>
		
		<?php if (in_array($custom_field->type, array('Textbox', 'Listbox'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Size',$flutter_domain); ?>:</th>
			<td><input type="text" name="custom-field-size" id="custom-field-size" size="2" value="<?php echo $custom_field->properties['size']?>" /></td>
		</tr>	
		<?php endif; ?>

		<?php if (in_array($custom_field->type, array('Multiline Textbox'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Height',$flutter_domain); ?>:</th>
			<td><input type="text" name="custom-field-height" id="custom-field-height" size="2" value="<?php echo $custom_field->properties['height']?>" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><?php _e('Width',$flutter_domain); ?>:</th>
			<td><input type="text" name="custom-field-width" id="custom-field-width" size="2" value="<?php echo $custom_field->properties['width']?>" /></td>
		</tr>	
		<?php endif; ?>

		<?php if (in_array($custom_field->type, array('Date'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Format',$flutter_domain); ?>:</th>
			<td>
				<select name="custom-field-date-format" id="custom-field-date-format">
					<option value="m/d/Y" <?php if ($custom_field->properties['format'] == "m/d/Y" ) echo " selected ";?>>4/20/2008</option>
					<option value="l, F d, Y" <?php if ($custom_field->properties['format'] == "l, F d, Y" ) echo " selected ";?>>Sunday, April 20, 2008</option>
					<option value="F d, Y" <?php if ($custom_field->properties['format'] == "F d, Y" ) echo " selected ";?>>April 20, 2008</option>
					<option value="m/d/y" <?php if ($custom_field->properties['format'] == "m/d/y" ) echo " selected ";?>>4/20/08</option>
					<option value="Y-d-m" <?php if ($custom_field->properties['format'] == "Y-m-d" ) echo " selected ";?>>2008-04-20</option>
					<option value="d-M-y" <?php if ($custom_field->properties['format'] == "d-M-y" ) echo " selected ";?>>20-Apr-08</option>
					<option value="m.d.Y" <?php if ($custom_field->properties['format'] == "m.d.Y" ) echo " selected ";?>>4.20.2008</option>
					<option value="m.d.y" <?php if ($custom_field->properties['format'] == "m.d.y" ) echo " selected ";?>>4.20.08</option>
				</select>
			</td>
		</tr>	
		<?php endif; ?>
		
		<?php if (in_array($custom_field->type, array('Slider'))) : ?>	
		<tr valign="top">
			<th scope="row"><?=_e('Value min', $flutter_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-min" id="custom-field-slider-min" size="2" value="<?php echo $custom_field->properties['min']?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?=_e('Value max', $flutter_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-max" id="custom-field-slider-max" size="2" value="<?php echo $custom_field->properties['max']?>" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><?=_e('Stepping', $flutter_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-step" id="custom-field-slider-step" size="2" value="<?php echo $custom_field->properties['step']?>" /></td>
		</tr>
		<?php endif; ?>


		<?php
		if ($custom_field->has_options == "true") :
			$options = implode("\n", (array)$custom_field->options)
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Options',$flutter_domain); ?>:</th>
			<td>
				<textarea name="custom-field-options" id="custom-field-options" rows="2" cols="38"><?php echo htmlspecialchars($options)?></textarea><br />
				<em><?php _e('Separate each option with a newline.',$flutter_domain); ?></em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Default Value',$flutter_domain); ?>:</th>
			<td>
				<?php
				$default_value = implode("\n", (array)$custom_field->default_value);
				if ($custom_field->allow_multiple_values == "true") :
				?>
				<textarea name="custom-field-default-value" id="custom-field-default-value" rows="2" cols="38"><?php echo htmlspecialchars($default_value)?></textarea><br />
				<em><?php _e('Separate each value with a newline.',$flutter_domain); ?></em>
				<?php
				else:
				?>
				<input type="text" name="custom-field-default-value" id="custom-field-default-value" size="25" value="<?php echo htmlspecialchars($default_value)?>" />
				<?php
				endif;
				?>
			</td>
		</tr>
		<?php
		endif;
		?>
		
		<tr valign="top">
			<th scope="row"><?php _e('Type',$flutter_domain); ?>:</th>
			<td>

				<!-- START :: Javascript for Image/Photo' Css Class -->
				<script type="text/javascript" language="javascript">
					submitForm = false;
					function fun(name)
					{
						if(name == "Image")
						{
							document.getElementById('divCSS').style.display = 'inline';
							document.getElementById('divLbl').style.display = 'inline';
							document.getElementById('lblHeight').style.display = 'inline';
							document.getElementById('txtHeight').style.display = 'inline';
							document.getElementById('lblWidth').style.display = 'inline';
							document.getElementById('txtWidth').style.display = 'inline';
						}
						else
						{
							document.getElementById('divCSS').style.display = 'none';
							document.getElementById('divLbl').style.display = 'none';
							document.getElementById('lblHeight').style.display = 'none';
							document.getElementById('txtHeight').style.display = 'none';
							document.getElementById('lblWidth').style.display = 'none';
							document.getElementById('txtWidth').style.display = 'none';
						}
					}
					function checkEmpty()
					{
						if (submitForm && (document.getElementById('custom-field-name').value == "" || document.getElementById('custom-field-description').value == "")){
							alert("<?php _e('Please fill in the name and the label of the field',$flutter_domain); ?>");	
							return false;
						}
						return true;
						
					}
				</script>
				<!-- END :: Javascript for Image/Photo' Css Class -->

				<?php
				$field_types = RCCWP_CustomField::GetCustomFieldTypes();
				foreach ($field_types as $field) :
					$checked = 
						$field->name == $custom_field->type ?
						'checked="checked"' : '';
				?>
					<label><input name="custom-field-type" value="<?php echo $field->id?>" type="radio" <?php echo $checked?> onclick='fun("<?php echo $field->name?>");'/>
					<?php echo $field->name?></label><br />
				<?php
				endforeach;
				?>
			</td>
		</tr>
		<!-- START :: For Image/Photo' Css -->
		<?php
			$isDisplay = $custom_field->type == "Image" ? 'display:inline;' : 'display:none;';
		?>
		<?php 
			$size = explode("&",$custom_field->properties['params']);

if(isset($size[3])){
$c=$size[3];
}
			if (substr($size[1],0 ,1) == "h"){
				$h = substr($size[1], 2);
			}
			elseif (substr($size[1],0 ,1) == "w"){
				$w = substr($size[1], 2);
			}

			if (substr($size[2],0 ,1) == "h"){
				$h = substr($size[2], 2);
			}
			elseif (substr($size[2],0 ,1) == "w"){
				$w = substr($size[2], 2);
			}
			
			$cssVlaue = $custom_field->CSS;
		?>
		<tr valign="top">
			<th scope="row"><span id="lblHeight" style="<?php echo $isDisplay;?>"><?php _e('Max Height',$flutter_domain); ?>:</span></th>
			<td><span id="txtHeight" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-photo-height" id="custom-field-photo-height" size="3" value="<?php echo $h; ?>" /></span></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><span id="lblWidth" style="<?php echo $isDisplay;?>"><?php _e('Max Width',$flutter_domain); ?>:</span></th>
			<td><span id="txtWidth" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-photo-width" id="custom-field-photo-width" size="3" value="<?php echo $w; ?>" /></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><span id="lblWidth" style="<?php echo $isDisplay;?>"><?php _e('Custom',$flutter_domain); ?>:</span></th>
			<td><span id="txtWidth" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-custom-params" id="custom-field-custom-params" value="<?php echo $c; ?>" /></span>
		
		</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><div id="divLbl" style="<?php echo $isDisplay;?>"><?php _e('Css Class',$flutter_domain); ?>:</div></th>
			<td>
				<div id="divCSS" style="<?php echo $isDisplay;?>">
				<input name="custom-field-css" id="custom-field-css" size="40" type="text" value="<?php echo $cssVlaue?>" />
				</div>
			</td>
		</tr>

		<!-- END :: For Image/Photo' Css -->		
		</tbody>
		</table>
		
		<input name="flutter_action" type="hidden" value="submit-edit-custom-field" />
  		<p class="submit" >
  			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-field')."&custom-group-id=$customGroupID"?>" class="button"><?php _e('Cancel',$flutter_domain); ?></a> 
  			<input type="submit" id="submit-edit-custom-field" value="<?php _e('Update',$flutter_domain); ?>" onclick="submitForm=true;" />
  		</p>
	  	
  		</form>
	  	
  		</div>
	  	
  		<?php
	}
}
?>