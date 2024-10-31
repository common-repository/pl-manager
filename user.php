<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('MMPLM_User')){
class MMPLM_User {
   
   
     public static $instance = NULL;

     public static function get_instance()
     {
	    if(NULL == self::$instance)
	    {
		 self::$instance = new self;
	    }
	  return self::$instance;
       }
     
     
      public function __construct(){
    
	    add_action('wp_head',array($this,'register_style'));
		if(get_option('plm-use-shortcode')!=1 && 1==get_option('plm-activate-plugin')){
		  
		if(1==get_option('plm-filter')){
		  add_filter('the_content',array($this,'mmplm_output_content'));
		  add_filter('get_the_excerpt', array($this,'mmplm_remove_content_filter'), 5);
		}elseif(2==get_option('plm-filter')){
		   add_filter('the_excerpt',array($this,'mmplm_output_content'));
		}
		
		}
	    add_action( 'wp_enqueue_scripts',array($this,'mmplm_include_user_js_css'));
		
		if(get_option('plm-use-shortcode')==1 && 1==get_option('plm-activate-plugin')){
		  add_shortcode( 'mm-plm', array($this,'mmplm_shortcode'));
		}
   
   }

   function mmplm_remove_content_filter( $content ) {
		  remove_filter('the_content', array($this,'mmplm_output_content'));
		  return $content;
   }
   
   public function mmplm_output_content($content){
     global $post;
	 
	 if(empty($post)){return;}
	 if(is_single() && '1' == get_option('plm-remove-from-single-page')){return $content;}
	 
	 $post_id = $post->ID;
	 $nonce = wp_create_nonce("plm_like_post_nonce");
	 $post_class = 'post-'.$post_id;
	 $likes = MMPLM_Functions::mmplm_get_like_count($post_id);
	 $dislikes = MMPLM_Functions::mmplm_get_unlike_count($post_id);
	 $style = get_option('plm-voting-style');
	 $alignment = (get_option('plm-alignment')=='right')?'align-right':'align-left';
	 $like_dislike_text = (get_option('plm-text-like-dislike'))?explode('/',get_option('plm-text-like-dislike')):array(__('Like',MMPLM_PLUGIN_NAME),__('Dislike',MMPLM_PLUGIN_NAME));
	 $like_class = '';
	 $dislike_class = '';
	 $post_class .= ' '.$alignment;
	 $is_already_liked = '';
	  
	 $exclude_posts = (!empty(get_option('plm-exclude-posts')))?explode(',',get_option('plm-exclude-posts')):'';
	 $exclude_post_types = (!empty(get_option('plm-exclude-post-types')))?get_option('plm-exclude-post-types'):'';
	 $exclude_this_cats = (!empty(get_option('plm-exclude-categories')))?get_option('plm-exclude-categories'):'';
	 $categories = get_the_terms( $post_id, 'category' );
	 
	 $exclude_this_cats = (empty($exclude_this_cats)) ? array():$exclude_this_cats;
	 $exclude_posts = (empty($exclude_posts)) ? array():$exclude_posts;
	 $exclude_post_types = (empty($exclude_post_types)) ? array():$exclude_post_types;
	 $categories = (empty($categories)) ? array():$categories;
	 
	 
	 
	 
	 foreach($categories as $category){
	  if(in_array($category->term_id,$exclude_this_cats)){
		  return $content;
	  }
	 }
	 
	 if( in_array($post->ID,$exclude_posts) || in_array($post->post_type,$exclude_post_types) ){
		  return $content;
	 }
	 
	 if('style1'==$style){
		  $like_class = 'fa-thumbs-up';
		  $dislike_class = 'fa-thumbs-down';
	 }elseif('style2'==$style){
		  $like_class = 'fa-thumbs-o-up';
		  $dislike_class = 'fa-thumbs-o-down';
	 }elseif('style3'==$style){
		  $like_class = 'fa-heart';
	 }elseif('style4'==$style){
		  $like_class = 'fa-heart-o';
	 }
	 
    	
	$output = '<div class="plm-btn-holder '.$post_class.' clearfix "><span class="plm-btn-inr-holder">';
	$output .= '<a href="javascript:void()" title="'.__($like_dislike_text[0],MMPLM_PLUGIN_NAME).'" data-post_id="'.$post_id.'" data-type="like" data-nonce="'.$nonce.'" class="plm-like plm-click '.$style.'">';
	$output .= '<i class="fa '.$like_class.'" aria-hidden="true"></i>';
    $output .= '<span class="plm-count">'.__($likes).'</span></a>';
	
	if(get_option('plm-show-dislike')==1 ){
		  if('style3'!=$style){
			   if('style4'!=$style){
		$output .='<a href="javascript:void()" title="'.__($like_dislike_text[1],MMPLM_PLUGIN_NAME).'" data-post_id="'.$post_id.'" data-type="unlike" data-nonce="'.$nonce.'" class="plm-unlike plm-click '.$style.'">';
		
		$output .= '<i class="fa '.$dislike_class.'" aria-hidden="true"></i>';
	    $output .= '<span class="plm-count">'.__($dislikes).'</span></a></span>';
			   }
			   }
	}
	
	 if('style3'==$style || 'style4'==$style){ $output .='</span>';}

	if(MMPLM_Functions::mmplm_is_user_already_liked($post_id)){
	 $is_already_liked = __("Already voted",MMPLM_PLUGIN_NAME);
	}
	
     $output.='<span class="status">'.$is_already_liked.'</span></div>';
	 
	   if('top'==get_option('plm-position'))
	       {
		    return $output.$content;
	       }else{
		    return $content.$output;
	       }
   }

public function mmplm_shortcode($atts){
     
	 if(0==get_option('plm-activate-plugin')){
		  _e('Please activate plugin first',MMPLM_PLUGIN_NAME);
		  return;
     }
	 if(is_single() && '1' == get_option('plm-remove-from-single-page')){return;}
	 
	$attrs = shortcode_atts( array(
        'id' => get_the_ID(),
    ), $atts );
	 
     $post_id = $attrs['id'];
	 $post_obj = get_post($post_id);
	 $nonce = wp_create_nonce("plm_like_post_nonce");
	 $post_class = 'post-'.$post_id;
	 $likes = MMPLM_Functions::mmplm_get_like_count($post_id);
	 $dislikes = MMPLM_Functions::mmplm_get_unlike_count($post_id);
	 $style = get_option('plm-voting-style');
	 $alignment = (get_option('plm-alignment')=='right')?'align-right':'align-left';
	 $like_dislike_text = (get_option('plm-text-like-dislike'))?explode('/',get_option('plm-text-like-dislike')):array(__('Like',MMPLM_PLUGIN_NAME),__('Dislike',MMPLM_PLUGIN_NAME));
	 $like_class = '';
	 $dislike_class = '';
	 $post_class .= ' '.$alignment;
	 $is_already_liked = '';
	  
	 $exclude_posts = (!empty(get_option('plm-exclude-posts')))?explode(',',get_option('plm-exclude-posts')):'';
	 $exclude_post_types = (!empty(get_option('plm-exclude-post-types')))?get_option('plm-exclude-post-types'):'';
	 $exclude_this_cats = (!empty(get_option('plm-exclude-categories')))?get_option('plm-exclude-categories'):'';
	 $categories = get_the_terms( $post_id, 'category' );
	 
	 $exclude_this_cats = (empty($exclude_this_cats)) ? array():$exclude_this_cats;
	 $exclude_posts = (empty($exclude_posts)) ? array():$exclude_posts;
	 $exclude_post_types = (empty($exclude_post_types)) ? array():$exclude_post_types;
	 $categories = (empty($categories)) ? array():$categories;

	 
	 foreach($categories as $category){
	  if(in_array($category->term_id,$exclude_this_cats)){
		  return;
	  }
	 }
	 
	 if( in_array($post_obj->ID,$exclude_posts) || in_array($post_obj->post_type,$exclude_post_types) ){
		  return;
	 }
	 
	 if('style1'==$style){
		  $like_class = 'fa-thumbs-up';
		  $dislike_class = 'fa-thumbs-down';
	 }elseif('style2'==$style){
		  $like_class = 'fa-thumbs-o-up';
		  $dislike_class = 'fa-thumbs-o-down';
	 }elseif('style3'==$style){
		  $like_class = 'fa-heart';
	 }elseif('style4'==$style){
		  $like_class = 'fa-heart-o';
	 }
	 
    	
	$output = '<div class="plm-btn-holder '.$post_class.' clearfix "><span class="plm-btn-inr-holder">';
	$output .= '<a href="javascript:void()" title="'.__($like_dislike_text[0],MMPLM_PLUGIN_NAME).'" data-post_id="'.$post_id.'" data-type="like" data-nonce="'.$nonce.'" class="plm-like plm-click '.$style.'">';
	$output .= '<i class="fa '.$like_class.'" aria-hidden="true"></i>';
    $output .= '<span class="plm-count">'.__($likes).'</span></a>';
	
	if(get_option('plm-show-dislike')==1){
		  if('style3'!=$style){
			   if('style4'!=$style){
		$output .='<a href="javascript:void()" title="'.__($like_dislike_text[1],MMPLM_PLUGIN_NAME).'" data-post_id="'.$post_id.'" data-type="unlike" data-nonce="'.$nonce.'" class="plm-unlike plm-click '.$style.'">';
		
		$output .= '<i class="fa '.$dislike_class.'" aria-hidden="true"></i>';
	    $output .= '<span class="plm-count">'.__($dislikes).'</span></a></span>';
			   }
			   }
	}
	if('style3'==$style || 'style4'==$style){ $output .='</span>';}
	
	
	if(MMPLM_Functions::mmplm_is_user_already_liked($post_id)){
	 $is_already_liked = __("Already voted",MMPLM_PLUGIN_NAME);
	}
     $output.='<span class="status">'.$is_already_liked.'</span></div>';
     
	 echo $output;
     }

	 
    public function mmplm_include_user_js_css(){
     
	  wp_enqueue_style( 'plm-user-css', MMPLM_CSS_DIR_URL.'frontend/style.css' );
	  wp_enqueue_style( 'plm-font-awesome-css', MMPLM_CSS_DIR_URL.'backend/font-awesome.min.css' );
	 
	  wp_enqueue_script( 'jquery' );
	  
	  wp_register_script( 'plm-user-script-js', MMPLM_JS_DIR_URL.'frontend/script.js', array('jquery') );
	  wp_enqueue_script( 'plm-user-script-js' );
	  wp_localize_script( 'plm-user-script-js', 'plm_admin_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
     
    }

	function register_style(){
	 ?>
	 <style>
		  a.plm-click.plm-like{color: <?php echo get_option('plm-like-icon-color'); ?>}
		  a.plm-click.plm-unlike{color: <?php echo get_option('plm-dislike-icon-color'); ?>}
		  a.plm-click.plm-like:hover{color: <?php echo get_option('plm-like-icon-color-hover'); ?>}
		  a.plm-click.plm-unlike:hover{color: <?php echo get_option('plm-dislike-icon-color-hover'); ?>}
		  .plm-btn-holder {font-size: <?php echo get_option('plm-icon-size'); ?>px;}
		  .plm-btn-holder .status{font-size: <?php echo get_option('plm-icon-size'); ?>px;}
	 </style>
	 <?php
	}
    
}
 }   
    