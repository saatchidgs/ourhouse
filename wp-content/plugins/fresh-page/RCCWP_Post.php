<?php
class RCCWP_Post {	
	
	function SaveCustomFields($postId){
		if(!wp_verify_nonce($_REQUEST['rc-custom-write-panel-verify-key'], 'rc-custom-write-panel'))
			return $postId;
        		
		if (!current_user_can('edit_post', $postId))
			return $postId;
		RCCWP_Post::SetCustomWritePanel($postId);
		RCCWP_Post::PrepareFieldsValues($postId);
		RCCWP_Post::SetMetaValues($postId);
		

		return $postId;
	}
	
		
	/*
	 * Attach a custom write panel to the current post by saving the custom write panel id
	 * as a meta value for the post
	 */
	function SetCustomWritePanel($postId) {
		$customWritePanelId = $_POST['rc-cwp-custom-write-panel-id'];
		if (isset($customWritePanelId))
		{
			if (!empty($customWritePanelId))
			{
				
				if (!update_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, $customWritePanelId))
				{

					add_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, $customWritePanelId);
				}
			}
			else
			{
				delete_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY);
			}
		}
	}
	
	/**
	 * Save all custom field values meta values for the post, this function assumes that 
	 * $_POST['rc_cwp_meta_keys'] contains the names of the fields, while $_POST[{FIELD_NAME}]
	 * contains the value of the field named {FIELD_NAME}
	 *
	 * @param unknown_type $postId
	 * @return unknown
	 */
	function SetMetaValues($postId){
		global $wpdb;
	
		$customWritePanelId = $_POST['rc-cwp-custom-write-panel-id'];
		$customFieldKeys = $_POST['rc_cwp_meta_keys'];
		
		if (!empty($customWritePanelId) && !empty($customFieldKeys) )
		{
				
			// --- Delete old values
			foreach ($customFieldKeys as $key)
			{
				if (!empty($key))
				{
					list($customFieldId, $groupCounter, $fieldCounter, $groupId, $rawCustomFieldName) = split("_", $key, 5);
					$customFieldName = $wpdb->escape(stripslashes(trim(RC_Format::GetFieldName($rawCustomFieldName))));
					delete_post_meta($postId, $customFieldName);	
				}
			}

            if ( $the_post = wp_is_post_revision($postId) )
			    $postId = $the_post;

			$wpdb->query("DELETE FROM ". RC_CWP_TABLE_POST_META .
				" WHERE post_id=$postId");

			// --- Make sure all groups/fields duplicates are in sequence, 
			//		i.e. there is no gap due to removing items
			
			$arr = ARRAY();
			foreach($customFieldKeys as $key=>$value)
			{
				list($customFieldId, $groupCounter, $fieldCounter, $groupId,$rawCustomFieldName) = split("_", $value, 5);
				$arr[$key]->id = $customFieldId ;
				$arr[$key]->gc = $groupCounter ;
				$arr[$key]->fc = $fieldCounter ;
                $arr[$key]->gi = $groupId;
				$arr[$key]->fn = $rawCustomFieldName ;
				$arr[$key]->ov = $value ;
			}

            /**
			for($i=0;$i<$key;$i++){
				for($j=0;$j<$key;$j++){
					if( $arr[$i]->id == $arr[$j]->id )
					{
						if( $arr[$i]->gc == $arr[$j]->gc )
						{
							if( $arr[$i]->fc < $arr[$j]->fc )
							{
								$t = $arr[$i] ;
								$arr[$i] = $arr[$j] ;
								$arr[$j] = $t ;
							}
						}
						else if( $arr[$i]->gc < $arr[$j]->gc )
						{
							$t = $arr[$i] ;
							$arr[$i] = $arr[$j] ;
							$arr[$j] = $t ;
						}
					}
					else if( $arr[$i]->id < $arr[$j]->id )
					{
						$t = $arr[$i] ;
						$arr[$i] = $arr[$j] ;
						$arr[$j] = $t ;
					}
				}
			}
			
			for($i=0;$i<$key;$i++)
			{
				if( $arr[$i]->id != $currentFieldID )
				{
					$currentFieldID = $arr[$i]->id ;
					$currentG = $arr[$i]->gc ;
					$GC = 1 ;
					$FC = 1 ;
				}
				else if( $arr[$i]->gc != $currentG )
				{
					$GC ++ ;
					$FC = 1 ;
					$currentG = $arr[$i]->gc ;
				}
				else $FC ++ ;
				$arr[$i]->fc = $FC ;
				$arr[$i]->gc = $GC ;
			}*/


			// --- Add new meta data
			foreach ($arr as $key)
			{
				if (!empty($key))
				{
                    //order
                    if($key->gi == 1){
                        $order = 1;
                    }else if (!empty($_POST['order_'.$key->gi.'_'.$key->gc])){
                        $order = $_POST['order_'.$key->gi.'_'.$key->gc];
                    }else{
                        $order = 1;
                    }
                    
					$customFieldValue = $_POST[$key->ov];

					$customFieldName = $wpdb->escape(stripslashes(trim(RC_Format::GetFieldName($key->fn))));
					
					// Prepare field value
                        if (is_array($customFieldValue))
					{
						$finalValue = array();
						foreach ($customFieldValue as $value)
						{
							$value = stripslashes(trim($value));
							array_push($finalValue, $value);
							//add_post_meta($postId, $customFieldName, $value);
						}
					}
					else
					{
						$finalValue = stripslashes(trim($customFieldValue));
					}
            
                   
    				// Add field value meta data
					add_post_meta($postId, $customFieldName, $finalValue);
					
					// make sure meta is added to the post, not a revision
					if ( $the_post = wp_is_post_revision($postId) )
						$postId = $the_post;
					
					$fieldMetaID = $wpdb->insert_id;

					// Add field extended properties
        			$wpdb->query("INSERT INTO ". RC_CWP_TABLE_POST_META .
								" (id, field_name, group_count, field_count, post_id,order_id) ".
								" VALUES ($fieldMetaID, '$customFieldName', ".$key->gc.", ".$key->fc.", $postId,$order)");
				}
			}
	 	}	
	}
	
	/**
	 * This function prepares some custom fields before saving it. It reads $_REQUEST and:
	 * 1. Adds params to photos uploaded (Image field)
	 * 2. Formats dates (Date Field) 
	 *
	 */
	function PrepareFieldsValues($postId)
	{
			
		// Add params to photos
		if( isset( $_REQUEST['rc_cwp_meta_photos'] ) ) 
		{
			foreach( $_REQUEST['rc_cwp_meta_photos'] as $meta_name )
			{		
				$slashPos = strrpos($_POST[$meta_name], "/");
				if (!($slashPos === FALSE))
					$_POST[$meta_name] = substr($_POST[$meta_name], $slashPos+1);

				// if photo is new, add params
				/*if( isset( $_REQUEST[ $meta_name . '_params' ] ) && $_REQUEST[ $meta_name . '_params' ] )
				{
					if( ! strpos( $_POST[$meta_name], $_REQUEST[ $meta_name . '_params' ] ) )
					{
						$_POST[$meta_name] .= $_REQUEST[$meta_name . '_params'];
					}
				}*/
				
				//Rename photo if it is edited using editnplace to avoid phpthumb cache
				if ($_POST[$meta_name.'_dorename'] == 1){
					$oldFilename = $_POST[$meta_name]; 
					$newFilename = time().substr($oldFilename, 10);
					rename(FLUTTER_UPLOAD_FILES_DIR.$oldFilename, FLUTTER_UPLOAD_FILES_DIR.$newFilename);
					$_POST[$meta_name] = $newFilename;
				}
				
			}
		}

		// Format Dates
		if( isset( $_REQUEST['rc_cwp_meta_date'] ) )
		{
			foreach( $_REQUEST['rc_cwp_meta_date'] as $meta_name )
			{
				$metaDate = strtotime($_POST[$meta_name]);
				$formatted_date = strftime("%Y-%m-%d", $metaDate);
				$_POST[$meta_name] = $formatted_date;
			}
		}
    


        
	}
	
	/**
	 * Get a custom write panel by reading $_REQUEST['custom-write-panel-id'] or the
	 * To see whether $_GET['post'] has a custom write panel associated to it.
	 *
	 * @return Custom Write Panel as an object, returns null if there is no write panels.
	 */
	function GetCustomWritePanel()
	{
		
		if (isset($_GET['post']))
		{

			$customWritePanelId = get_post_meta((int)$_GET['post'], RC_CWP_POST_WRITE_PANEL_ID_META_KEY, true);
		
		
			if (empty($customWritePanelId))
			{
				$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
			}
		}
		else if (isset($_REQUEST['custom-write-panel-id']))
		{
			$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
		}
		
		if (isset($customWritePanelId))
		{
			include_once('RCCWP_Application.php');
			$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		}
		
		return $customWritePanel;
	}


	/**
	 *
	 *
	*/
	function DeletePostMetaData($postId)
	{
		global $wpdb;
		$wpdb->query("DELETE FROM " . RC_CWP_TABLE_POST_META . " WHERE post_id =" . $postId) ;
	}

	
	
}
?>
