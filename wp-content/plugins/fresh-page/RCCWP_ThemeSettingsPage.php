<?php
/**
 * Admin Theme Settings Page
 *
 *
 */
class RCCWP_ThemeSettingsPage
{
	
	function ApplyHead(){
		if (!empty($_GET['page']) && $_GET['page']!='Flutter_ThemeSettings') return;
		RCCWP_WritePostPage::CustomFieldsCSSScripts();
		
	}
	function AddScripts(){
        if (!empty($_GET['page'])  && $_GET['page']!='Flutter_ThemeSettings') return;
		//wp_enqueue_script('editor');
		wp_enqueue_script('tiny_mce');
	}

    /**
     * This function show the panel for 
     * add new panels and change his values
     *
     * @author David Valdez <david@freshout.us>
     *
     */           
	function Main()	{
        global $wpdb,$flutter_domain;
		//FlutterLayoutBlock::UpdateModuleSettings(get_template_directory().'/configure.xml', -1, '-', get_option('template'));
        $template = get_option('template');
        $template_module_id =  $wpdb->get_var("SELECT module_id  FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'"); 

		$moduleSettings = FlutterLayoutBlock::GetModuleSettings($template_module_id);
		if (!$moduleSettings) die(__('This theme has no settings', $flutter_domain));
		
		if (isset($_POST['_savesetttings'])){
			foreach($moduleSettings->variables as $varKey => $variable) {
				$moduleSettings->variables[$varKey]->value = $_POST[$variable->variable_name]; 
			}
			$moduleSettings->SaveValues();
		}
		
		?>
		
		<script type="text/javascript">
			var wp_root         = "<?php echo get_bloginfo('wpurl');?>";
		</script>
		<script type="text/javascript" src="<?=FLUTTER_URI?>js/ui.core.js"></script>
		<div class="wrap">
		<h2><?php _e('Flutter Theme Settings', $flutter_domain); ?></h2>
		<input type='hidden' id='editorcontainer'/>
		<form name="themesettings_form" action="?page=Flutter_ThemeSettings" id="themesettings_form" method="post">
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<?php
		foreach($moduleSettings->variables as $variable) {
			$variable->properties = array();
			$inputName = $variable->variable_name;
			$variableValue = $variable->value;
			?>
			<tr valign="top">
				<th scope="row"><?php echo $variable->description ?></th>
				<td>
				<?php
					switch ($variable->type)
					{ 
						case 'textbox' :
							$variable->properties['size'] = "";
							RCCWP_WritePostPage::TextboxInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'multiline_textbox' :
							$variable->properties['height'] = "10";
							$variable->properties['width'] = "10";
							RCCWP_WritePostPage::MultilineTextboxInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'checkbox' :
							RCCWP_WritePostPage::CheckboxInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'checkbox_list' :
							RCCWP_WritePostPage::CheckboxListInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'radiobutton_list' :
							RCCWP_WritePostPage::RadiobuttonListInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'dropdown_list' :
							RCCWP_WritePostPage::DropdownListInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'listbox' :
							$variable->properties['size'] = "";
							RCCWP_WritePostPage::ListboxInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'file' :
							RCCWP_WritePostPage::FileInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'image' :
							RCCWP_WritePostPage::PhotoInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'date' :
							$variable->properties['format'] = "m.d.y";
							RCCWP_WritePostPage::DateInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'audio' :
							RCCWP_WritePostPage::AudioInterface($variable, $inputName, 0, 0, $variableValue);
							break;
						case 'Color Picker' :
							RCCWP_WritePostPage::ColorPickerInterface($customField, $inputName, 0, 0,$variableValue);
							break;
						case 'Slider' :
							RCCWP_WritePostPage::SliderInterface($customField, $inputName, 0, 0,$variableValue);
							break;
					}
				?>
				</td>
			</tr>
		<?php
		}
		?>
		
		</table>
		
		<p class="submit" ><input name="_savesetttings" type="submit" value="<?php _e('Save Settings', $flutter_domain); ?>" /></p>
		</form>
		</div>
		<?php
	}

    /**
     * This function  is for add new element at the Flutter i
     * layout settings
     *
     * @author David Valdez <david@freshout.us>
     */
     function  show_layout_settings(){
         global $flutter_domain,$wpdb;

         $template = get_option('template');

         $registered_layout = $wpdb->get_var("SELECT COUNT(*) FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'");

         if($registered_layout == 0){

             $wpdb->query("INSERT INTO ".FLUTTER_TABLE_LAYOUT_MODULES." (module_id,theme,page,duplicate_id) VALUES (-1,'{$template}','-',0)");

         }else{
            $template_module_id =  $wpdb->get_var("SELECT module_id  FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'"); 
         }



        if(!empty($_GET['flutter_action']) && $_GET['flutter_action'] == "create-layout-setting"){
           RCCWP_ThemeSettingsPage::create_layout_element();
           exit();
        }        ?>

        
        <script type="text/javascript">
            jQuery('document').ready(function(){
                jQuery('.del_element').click(function(){ 
                    if(!confirm("<?php echo _e("are you sure?",$flutter_domain);?>")){
                        return false;
                    }
                });
            });
        </script>
             

        <?php 

         $modules = FlutterLayoutBlock::GetModuleSettings($template_module_id);

         $table_header = "<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>";
         $table_header .= "<thead><tr><th scope='col' width='70%'>".__('Name',$flutter_domain)."</th><th scope='col' colspan='2'>".__('Actions',$flutter_domain)."</th></tr></thead><tbody>";
         $table_body   = "";
         $table_footer = "</tbody></table>";

         if(empty($modules->variables)){
         echo "<h2>".__("You don't have any setting created,  start creating one",$flutter_domain)."</h2>";
         echo "<br /><a href='?page=RCCWP_ThemeSettingsPage&flutter_action=create-layout-setting'>Add new layout setting</a>";
         exit();

         }

         foreach($modules->variables  as $varkey => $variable){
             if(empty($class)){
                 $class = "";
             }

             $class = $class == '' ? 'alternate' : '';
             $table_body .= "<tr class='{$class}'>";
             $table_body .= "<td>{$variable->variable_name}</td>";
             $table_body .= "<td></td>";
             $table_body .= "<td><a class='del_element' id='del_{$variable->variable_id}'  href='?page=RCCWP_ThemeSettingsPage&element_id={$variable->variable_id}&flutter_action=delete-theme-settings'>".__("Delete",$flutter_domain)."</a></td>";
             $table_body .= "</tr>";
         }
         echo $table_header.$table_body.$table_footer;
         echo "<br /><br /><a href='?page=RCCWP_ThemeSettingsPage&flutter_action=create-layout-setting'>".  __('add new template option',$flutter_domain)."</a>";
         echo "<br /><br />";
         echo '<a href="http://flutter.freshout.us"><img src="'. FLUTTER_URI.'/images/flutter_logo.jpg" /></a>';
     }


     /**
      * Remove  a setting  layout option
      */
      function remove_layout_setting(){
          global $wpdb;

          if (!is_user_logged_in()){
				wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php'));
          }

          if(empty($_GET['element_id']) || !is_int($_GET['element_id'])){
              wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php'));
          }

          $wpdb->query("DELETE FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE variable_id = ".$_GET['element_id']);

          wp_redirect('?page=RCCWP_ThemeSettingsPage');
      }

      function finish_create_layout_element(){
          global $flutter_domain,$wpdb;
          
          $template = get_option('template');
          $layout_id = $wpdb->get_var("SELECT block_id FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'");

          if(!empty($_POST['variable_name'])){
                $types = array(
    
                                  1  => 'textbox',
                                  2  => 'multiline Textbox',
                                  3  => 'checkbox', 
                                  4  => 'checkbox List',
                                  5  => 'radiobutton List',
                                  6  => 'dropdown List',
                                  7  => 'listbox',
                                  8  => 'file',
                                  9  => 'image', 
                                 10  => 'date',
                                 11  => 'audio' ,
				 12  => 'Color Picker',
				 13  => 'Slider' 
                              );

                
            
                $_POST['variable_name'] = trim($_POST['variable_name']);
                $_POST['variable_name'] = str_replace(" ","_",$_POST['variable_name']);

                

                $wpdb->query("INSERT INTO ".FLUTTER_TABLE_LAYOUT_VARIABLES." (variable_name,type,description,parent) VALUES ('{$_POST['variable_name']}','{$types[$_POST['custom-field-type']]}','{$_POST['description']}',{$layout_id});"); 
                wp_redirect('?page=RCCWP_ThemeSettingsPage');
          }
     }

      function create_layout_element(){
          global $flutter_domain;
          ?>
          <div class="wrap">
            <h2><?php _e('Create a new layout Setting',$flutter_domain);?></h2>
            <br/>
            <form method="post" action="?page=RCCWP_ThemeSettingsPage&flutter_action=create-layout-setting&save=true">
            <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
                <tbody>
                    <tr valing="top">
                        <th><?php _e('Name',$flutter_domain);?></th>
                        <td>
                            <input type="text" name="variable_name"/>
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><?php _e('Description',$flutter_domain);?></th>
                        <td>
                            <textarea name="description"></textarea>
                        </td>
                    </tr> 
                    <tr valing="top">
                        <th><?php _e('Type',$flutter_domain);?></th>
                        <td>
                             <?php
                                $field_types = RCCWP_CustomField::GetCustomFieldTypes();
                                foreach ($field_types as $field) :
                                    $checked =
                                    $field->name == RCCWP_CustomField::GetDefaultCustomFieldType() ?
                                    'checked="checked"' : '';
                             ?> 
                             <label><input name="custom-field-type" value="<?php echo $field->id?>" type="radio" <?php echo $checked;?> >
                             <?php echo $field->name?></label><br />
                             <?php
                                endforeach;
                             ?>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" id="continue-create-custom-field" value="<?php _e('Create',$flutter_domain);?>"  onclick="submitForm=true;"/>
                        </td>
                    </tr> 
               </tbody> 
            </form>        
             <p class="submit" >
             </p>
          </div>
<?php
      }
}
?>
