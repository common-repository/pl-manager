<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('wp_ajax_plm_process_vote_count',array('MMPLM_Ajax_Process','plm_process_vote_func'));
add_action('wp_ajax_nopriv_plm_process_vote_count',array('MMPLM_Ajax_Process','plm_process_vote_func'));

add_action('wp_ajax_plm_delete_liked_post',array('MMPLM_Ajax_Process','delete_liked_post_deails'));
add_action('wp_ajax_nopriv_plm_delete_liked_post',array('MMPLM_Ajax_Process','delete_liked_post_deails'));

add_action('wp_ajax_plm_restore_defaults',array('MMPLM_Ajax_Process','restore_defaults'));
add_action('wp_ajax_nopriv_plm_restore_defaults',array('MMPLM_Ajax_Process','restore_defaults'));

if(!class_exists('MMPLM_Ajax_Process')){
   class MMPLM_Ajax_Process {
   
         function plm_process_vote_func(){
              
              //date_default_timezone_set("Asia/Kolkata");
              
              global $wpdb,$message;
              $post_id=$_REQUEST['post_id'];
              $user_id=get_current_user_id();
              $type = $_REQUEST['type'];
              $nonce=$_REQUEST['nonce'];
              $date=date( 'Y-m-d H:i:s' );
              $message = '';
              
              
              if(!wp_verify_nonce($nonce,'plm_like_post_nonce'))
              {
                 $error=1;
                 $message=__("Invalid Access",'plm-text-domain');
              }else{
                 $is_user_logged_in = is_user_logged_in();
                 $loggin_required = get_option('plm-login-required');
                 $able_to_like = false;
                 
                 if($loggin_required && !$is_user_logged_in)
                 {
                      $error=1;
                      $message=get_option('plm-login-required-message');
                      
                 }else{
                     
                     $already_liked = MMPLM_Functions::mmplm_is_user_already_liked($post_id);
                     $like_period = get_option('plm-can-vote');
                     $current_date = date( 'Y-m-d H:i:s' );
                     $last_action = MMPLM_Functions::mmplm_get_last_action($post_id,$user_id);
                     
                     if(!$already_liked || $like_period=="always"){
                        
                        $able_to_like = true;
                        
                     }elseif($already_liked && $like_period=="once"){
                         
                         if($type!=$last_action){
                              $able_to_like = true;
                         }else{
                             $error = 1;
                             $message=__(get_option('plm-already-voted-message'),'plm-text-domain');
                         } 
                         
                     }else{
                         
                         
                         $last_like_date = MMPLM_Functions::mmplm_get_last_like_date($post_id);
                         $can_like_again_date = MMPLM_Functions::mmplm_get_can_like_again_date($last_like_date,$like_period);
                        
                         if($can_like_again_date > $current_date){
                             
                             $calculate_can_vote_day = (strtotime($can_like_again_date) - strtotime($current_date))/(60*60*24);
                             
                             $error=1;
                             $message=__('You can vote after ',MMPLM_PLUGIN_NAME).ceil($calculate_can_vote_day).' day(s)';
                         }else{
                             
                             $able_to_like = true;
                         }
                     }
                     
                     
                 }
                 
                 
              }
              
              if($able_to_like){
                 
                 global $is_like_found;
                 $user_ID = get_current_user_id();
                 $count = $wpdb->get_var(
                                         $wpdb->prepare(
                                                 "SELECT `value` FROM ".MMPLM_TABLE_NAME." WHERE `post_id`=%d AND (`user_id`=%d OR `ip`=%s)",$post_id,$user_ID,MMPLM_CLIENT_IP_ADDRESS
                                                 )
                                         );
                 
                 
                 if($type=='like'){
                     if($already_liked){
                        
                        if($last_action == 'unlike'){
                           $count=$count+2;
                        }else{
                           $count=$count+1;
                        }
                        
                        
                        $execute = $wpdb->update( MMPLM_TABLE_NAME, 
                                                                     array( 
                                                                         'value' => $count ,
                                                                         'date_time' => date( 'Y-m-d H:i:s' ) ,
                                                                         'last_action' => $type
                                                                     ),
                                                                     array(
                                                                           'post_id' => $post_id ,
                                                                           'ip' => MMPLM_CLIENT_IP_ADDRESS
                                                                           ),
                                                                     array('%d','%s','%s'),
                                                                     array('%d','%s')
                                                                     
                                             );
                         
                     }else{
                          $execute = $wpdb->insert( MMPLM_TABLE_NAME, 
                                                                     array( 
                                                                         'id' => '', 
                                                                         'post_id' => $post_id ,
                                                                         'value' => '1' ,
                                                                         'date_time' => $date ,
                                                                         'ip' => MMPLM_CLIENT_IP_ADDRESS ,
                                                                         'user_id' => $user_id ,
                                                                         'last_action' => $type
                                                                         
                                                                     ), 
                                                                     array('%d','%d','%d','%s','%s','%d','%s') 
                                             );
                     }
                 }else{
                     
                   if($already_liked){

                        if($last_action == 'like'){
                           $count=$count-2;
                        }else{
                           $count=$count-1;
                        }
                        
                        
                        $execute = $wpdb->update( MMPLM_TABLE_NAME, 
                                                                     array( 
                                                                         'value' => $count ,
                                                                         'date_time' => $date ,
                                                                         'last_action' => $type
                                                                     ),
                                                                     array(
                                                                           'post_id' => $post_id ,
                                                                           'ip' => MMPLM_CLIENT_IP_ADDRESS
                                                                           ),
                                                                     array('%d','%s','%s'),
                                                                     array('%d','%s')
                                                                     
                                             );
                         
                     }else{
                          $execute = $wpdb->insert( MMPLM_TABLE_NAME, 
                                                                     array( 
                                                                         'id' => '', 
                                                                         'post_id' => $post_id ,
                                                                         'value' => '-1' ,
                                                                         'date_time' => $date ,
                                                                         'ip' => MMPLM_CLIENT_IP_ADDRESS ,
                                                                         'user_id' => $user_id ,
                                                                         'last_action' => $type
                                                                         
                                                                     ), 
                                                                     array('%d','%d','%d','%s','%s','%d','%s') 
                                             );
                     }
                 }
                 
                 if($execute){
                   $message = get_option('plm-thank-you-message');
                 }
              }
         
              
              $like_count = MMPLM_Functions::mmplm_get_like_count($post_id);
              $unlike_count = MMPLM_Functions::mmplm_get_unlike_count($post_id);
              
              
              $result=array('like_count'=>$like_count,'unlike_count'=>$unlike_count,'message'=>$message);
              
              if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
                        echo json_encode($result);
               }else{
                  exit();
               }
               
              die();
         }
   
   
         function delete_liked_post_deails(){
               
               global $wpdb;
               $post_id = $_REQUEST['deleteID'];
                if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
                {
                  if('all'==$post_id){
                   
                   $status = $query = $wpdb->prepare("DELETE FROM `".MMPLM_TABLE_NAME."`");
                   $status = $wpdb->query($query);
                  }else{
                   $query =$wpdb->prepare("DELETE FROM `".MMPLM_TABLE_NAME."` WHERE `post_id`=%d",$post_id);
                   $status = $wpdb->query($query);
                  }
                }
                exit();
         }
   
         function restore_defaults(){
         
            foreach(MMPLM_Options::$settings_group as $key=>$value)
                      {
                         update_option($key, $value,'yes');
                      }
            exit();
         }
   }
}