<?php
    
    require( dirname(__FILE__) . "/../../../wp-config.php");

    //check if the user  is logged in
    global $flutter_domain;
    if(!(is_user_logged_in() && current_user_can('edit_posts'))){
        die(__('Authentication failed!',$flutter_domain));   
    };

    
    if(empty($_GET['action'])){
        exit();
    }

    switch($_GET['action']){
        case  "delete":
        $file = addslashes($_GET['file']);
        $exists = $wpdb->get_row("select * from wp_postmeta where meta_value =  '{$file}'");
        
        if(!empty($exists->meta_id)){
            $wpdb->query("DELETE FROM  wp_postmeta where meta_id = {$exists->meta_id}");
        }
        
        //deleting  file
        unlink(FLUTTER_FILES_PATH.$file);
        echo "true";
        exit();
    }

?>
