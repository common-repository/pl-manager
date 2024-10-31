<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('MMPLM_Admin')){
	  class MMPLM_Admin {
		 
		 public $settings_options;
	  
		 public static $instance = NULL;
			  public static function get_instance(){
				  if(NULL == self::$instance){
					  self::$instance == new self;
				  }
				  return self::$instance;
			  }
		   
		   
		 public function __construct(){
		  
			  add_action('admin_menu',array($this,'mmplm_add_to_menu'));
			  add_action( 'admin_init',array($this,'mmplm_register_setting'));
			  add_action('admin_enqueue_scripts',array($this,'mmplm_include_admin_js_css'));
		 
		 }
		 
		  /*Add Menu Page Function*/
		 public function mmplm_add_to_menu()
		  {
		  add_menu_page(__('PL Manager',MMPLM_PLUGIN_NAME),__('PL Manager',MMPLM_PLUGIN_NAME),'edit_theme_options','post-like-manager',array($this,'mmplm_settings_page'),MMPLM_PLUGIN_URL.'images/dashboard-icon.png');
		  }
		 
		 
		 public function mmplm_register_setting() {
		  
				  $settings_group = 'mmplm-options';
				  foreach (MMPLM_Options::$settings_group as $key=>$value){
				   register_setting( $settings_group, $key);
				  }
	  } 
	  
	  public function mmplm_settings_page(){
		   
		  $categories=  get_categories();
		  $excluded_sections = (empty(get_option('plm-exclude-sections')))?array():get_option('plm-exclude-sections');
		  $excluded_categories = (empty(get_option('plm-exclude-categories')))?array():get_option('plm-exclude-categories');
		  
		  $post_types = get_post_types(array('public'=>true),'names');
		  $include_post_types = (empty(get_option('plm-exclude-post-types')))?array():get_option('plm-exclude-post-types');
		  
		  
	  ?>    
	  <div class="wrap plm-wrap">
			  <div class="plm-header clear">
				 <h2><?php _e('Post Like Manager',MMPLM_PLUGIN_NAME); ?></h2>
			  </div>
			  <div class="plm-body">
				  <div class="plm-left-content">
					  <div class="plm-tabs" id="plm-tabs">
						  <ul class="resp-tabs-list">
							  <li><?php _e('Settings',MMPLM_PLUGIN_NAME); ?></li>
							  <li><?php _e('Likes',MMPLM_PLUGIN_NAME); ?></li>
						  </ul>
						  <div class="resp-tabs-container">
						  <div>
						   <div class="overlay" style="display: none;"><i aria-hidden="true" class="fa fa-spinner fa-5x fa-spin"></i>
	  </div>
						  <form method="post" action="options.php" id="plm-settings-form">
						  <?php
						  settings_fields('mmplm-options');
						  ?>
				  <div class="saveResult"></div>
						<div class="plm-form-sections clearfix">
						   <div class="pure-button submit-holder clearfix">
							<button type="submit" class="submit_button" value="Submit"><i class="fa fa-floppy-o" aria-hidden="true"></i>
	  Save Options</button>
						   <button type="reset" class="reset" value="Reset"><i class="fa fa-undo" aria-hidden="true"></i>
	  Reset</button>
						   </div>
						 </div>
	  
				<div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-activate-plugin"><?php _e('Enable Plugin',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--checkbox">
						   <input  type="checkbox" id="plm-activate-plugin" name="plm-activate-plugin" value="1" <?php echo (get_option('plm-activate-plugin')=='1')?'checked="checked"':''; ?>>
						   <div class="control__indicator"></div>
						</label>
						  
				  <div class="plm-note"><?php _e('Check to enable this plugin.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				   <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-use-shortcode"><?php _e('Use shortcode',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--checkbox">
						   <input  type="checkbox" id="plm-use-shortcode" name="plm-use-shortcode" value="1" <?php echo (get_option('plm-use-shortcode')=='1')?'checked="checked"':''; ?>>
						   <div class="control__indicator"></div>
						</label>
				  <div class="plm-note"><?php _e('Select this if you want to use shortcode, Note; When shortcode is checked filter will not work.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				   <div class="plm-form-sections clearfix">
					  <label class="plm-label"><?php _e('Filter',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio">
						   <?php _e('Content',MMPLM_PLUGIN_NAME); ?><input  type="radio" name="plm-filter" value="1" <?php echo (1==get_option('plm-filter')) ? 'checked="checked"' : ''?>>
						   <div class="control__indicator"></div>
						</label>
						<label class="control control--radio">
						   <?php _e('Excerpt',MMPLM_PLUGIN_NAME); ?><input  type="radio" name="plm-filter" value="2" <?php echo (2==get_option('plm-filter')) ? 'checked="checked"' : ''?>>
						   <div class="control__indicator"></div>
						   </label>
	  
						
				  <div class="plm-note"><?php _e('Select which filter you want.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
	  
					  <div class="plm-form-sections clearfix">
					  <label class="plm-label"><?php _e('Remove plugin settings on uninstall',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Yes',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-remove-plugin-settings" value="1" <?php echo (get_option('plm-remove-plugin-settings')=='1')?'checked="checked"':''; ?> >
						   <div class="control__indicator"></div>
						</label>
						<label class="control control--radio"><?php _e('No',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-remove-plugin-settings" value="0" <?php echo (get_option('plm-remove-plugin-settings')=='0')?'checked="checked"':''; ?>>
						<div class="control__indicator"></div>
						</label>
				  <div class="plm-note"><?php _e('Select whether the plugin settings and table will be removed when you uninstall the plugin. Setting this to NO is helpful if you are planning to reuse this in future with old data.',MMPLM_PLUGIN_NAME);?></div>
					  </div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-can-vote"><?php _e('Can vote',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<div class="select">
						  <select name="plm-can-vote" id="plm-can-vote">
							  <option value="once" <?php echo (get_option('plm-can-vote')=='once')?'selected=true"':''; ?>><?php _e('Once',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="always" <?php echo (get_option('plm-can-vote')=='always')?'selected=true"':''; ?>><?php _e('Always',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="1d" <?php echo (get_option('plm-can-vote')=='1d')?'selected=true"':''; ?>><?php _e('One Day',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="1w" <?php echo (get_option('plm-can-vote')=='1w')?'selected=true"':''; ?>><?php _e('One Week',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="1m" <?php echo (get_option('plm-can-vote')=='1m')?'selected=true"':''; ?>><?php _e('One Month',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="3m"<?php echo (get_option('plm-can-vote')=='3m')?'selected=true"':''; ?>><?php _e('Three Months',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="6m" <?php echo (get_option('plm-can-vote')=='6m')?'selected=true"':''; ?>><?php _e('Six Months',MMPLM_PLUGIN_NAME); ?></option>
							  <option value="1y" <?php echo (get_option('plm-can-vote')=='1y')?'selected=true"':''; ?>><?php _e('One Year',MMPLM_PLUGIN_NAME); ?></option>
						  </select>
						  <div class="select__arrow"></div>
						</div>
				  <div class="plm-note"><?php _e('Select whether a user can vote multiple times on only once',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>		 
			  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-exclude-posts"><?php _e('Exclude posts',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						  <input type="text" name="plm-exclude-posts" id="plm-exclude-posts" placeholder="Type post ids to exclude" value="<?php echo get_option('plm-exclude-posts');?>"/>
						  <div class="plm-note"><?php _e("Type post ids separated by ','.",MMPLM_PLUGIN_NAME); ?></div>
					  </div>
			   </div>
				 <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-exclude-post-types"><?php _e('Exclude post types',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">				  
				   <?php
				   foreach ($post_types as $key=>$value) {
				   $selected = (in_array($value, $include_post_types)) ? 'checked' : '';
				   $option  = '<label class="control control--checkbox">'.__(ucfirst($key),MMPLM_PLUGIN_NAME).'<input type="checkbox" name="plm-exclude-post-types[]" value="' . $value . '" ' . $selected . '/>';
				   $option .= '<div class="control__indicator"></div></label>';
				   echo $option;
				   }
				   ?>
					 </select>
				  <div class="plm-note"><?php _e('Check to remove from particular post types',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				 </div>
				  <div class="plm-form-sections clearfix">
						<label class="plm-label" for="plm-remove-from-single-page"><?php _e('Exclude From Single Page',MMPLM_PLUGIN_NAME); ?></label>
						<div class="plm-input-field">
							  <label class="control control--checkbox">
									<input  type="checkbox" id="plm-remove-from-single-page" name="plm-remove-from-single-page" value="1" <?php echo (get_option('plm-remove-from-single-page')=='1')?'checked="checked"':''; ?>>
									<div class="control__indicator"></div>
							  </label>
						
							  <div class="plm-note"><?php _e('Check to exclude from single page.',MMPLM_PLUGIN_NAME); ?></div>
						</div>
				  </div>

	  
				 <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-exclude-categories"><?php _e('Exclude categories',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">				  
				   <?php
				   foreach ($categories as $category) {
				   $selected = (in_array($category->cat_ID, $excluded_categories)) ? 'checked' : '';
				   $option  = '<label class="control control--checkbox">'.$category->cat_name.'<input type="checkbox" name="plm-exclude-categories[]" value="' . $category->cat_ID . '" ' . $selected . '/>';
				   $option .= '<div class="control__indicator"></div></label>';
				   echo $option;
				   }
				   ?>
					 </select>
				  <div class="plm-note"><?php _e('Check to exclude from particular categories.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
			
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label"><?php _e('Login required to vote',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Yes',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-login-required" value="1" <?php echo (get_option('plm-login-required')=='1')?'checked="checked"':''; ?> >
						  <div class="control__indicator"></div>
						</label>
						   <label class="control control--radio"><?php _e('No',MMPLM_PLUGIN_NAME); ?>
							  <input  type="radio" name="plm-login-required" value="0" <?php echo (get_option('plm-login-required')=='0')?'checked="checked"':''; ?>>
						   <div class="control__indicator"></div>
						</label>
				  <div class="plm-note"><?php _e('Select whether only logged in users can vote or not.',MMPLM_PLUGIN_NAME); ?></div> 
					  </div>
				  </div>
	  
				  <div class="plm-form-sections clearfix">
				  <label class="plm-label" for="plm-login-required-message"><?php _e('Login required message',MMPLM_PLUGIN_NAME); ?></label>
				  <div class="plm-input-field">
				  <input type="text" name="plm-login-required-message" id="plm-login-required-message" placeholder="Login required Message" value="<?php echo get_option('plm-login-required-message');?>"/>
			  <div class="plm-note"><?php _e('Message to show in case login required to vote.',MMPLM_PLUGIN_NAME); ?></div>
				  </div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-thank-you-message"><?php _e('Thank you message',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						  <input type="text" name="plm-thank-you-message" id="plm-thank-you-message" placeholder="Thank you message" value="<?php echo get_option('plm-thank-you-message');?>"/>
				  <div class="plm-note"><?php _e('Message to show after successful voting.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
	  
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-already-voted-message"><?php _e('Already voted message',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						  <input type="text" name="plm-already-voted-message"  id="plm-already-voted-message" placeholder="Already voted message" value="<?php echo get_option('plm-already-voted-message');?>"/>
				  <div class="plm-note"><?php _e('Message to show if user has already voted.',MMPLM_PLUGIN_NAME); ?></div>
				  
					  </div>
				  </div>
	  
				   <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-text-like-dislike"><?php _e('Title text for like/unlike',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						  <input type="text" name="plm-text-like-dislike"  id="plm-text-like-dislike" placeholder="Title text for like/unlike" value="<?php echo get_option('plm-text-like-dislike');?>"/>
				  <div class="plm-note"><?php _e('Enter both texts separated by "/" to show when user puts mouse over like/unlike images.',MMPLM_PLUGIN_NAME); ?></div>
				  
					  </div>
				  </div>
				   <div class="plm-form-sections clearfix">
					  <label class="plm-label"><?php _e('Show dislike option',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Yes',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-show-dislike" value="1" <?php echo (get_option('plm-show-dislike')=='1')?'checked="checked"':''; ?> >
						  <div class="control__indicator"></div>
						</label>
						<label class="control control--radio"><?php _e('No',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-show-dislike" value="0" <?php echo (get_option('plm-show-dislike')=='0')?'checked="checked"':''; ?>>
						  <div class="control__indicator"></div>
						</label>
					 <div class="plm-note"><?php _e('Select the option whether to show or hide the dislike option.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label"><?php _e('Show +/- symbols',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Yes',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-show-plus-minus" value="1" <?php echo (get_option('plm-show-plus-minus')=='1')?'checked="checked"':''; ?> >
						  <div class="control__indicator"></div>
						</label>
						<label class="control control--radio"><?php _e('No',MMPLM_PLUGIN_NAME); ?>
						  <input  type="radio" name="plm-show-plus-minus" value="0" <?php echo (get_option('plm-show-plus-minus')=='0')?'checked="checked"':''; ?>>
						  <div class="control__indicator"></div>
						</label>
						<div class="plm-note"><?php _e('Select the option whether to show or hide the plus or minus symbols before like/unlike count.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
	  
				   <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-voting-style"><?php _e('Voting style',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						  <select class="plm-select image" name="plm-voting-style" id="plm-voting-style">
							  <option data-img-src="<?php echo MMPLM_PLUGIN_URL.'images/like-samples/like-dislike-trans1.png'; ?>" value="style1" <?php echo (get_option('plm-voting-style')=='style1')?'selected=true':''; ?>><?php _e('Style 1',MMPLM_PLUGIN_NAME); ?></option>
							  <option data-img-src="<?php echo MMPLM_PLUGIN_URL.'images/like-samples/like-dislike-trans2.png'; ?>" value="style2" <?php echo (get_option('plm-voting-style')=='style2')?'selected=true':''; ?>><?php _e('Style 2',MMPLM_PLUGIN_NAME); ?></option>
							  <option data-img-src="<?php echo MMPLM_PLUGIN_URL.'images/like-samples/like-dislike-trans3.png'; ?>" value="style3" <?php echo (get_option('plm-voting-style')=='style3')?'selected=true':''; ?>><?php _e('Style 3',MMPLM_PLUGIN_NAME); ?></option>
							  <option data-img-src="<?php echo MMPLM_PLUGIN_URL.'images/like-samples/like-dislike-trans4.png'; ?>" value="style4" <?php echo (get_option('plm-voting-style')=='style4')?'selected=true':''; ?>><?php _e('Style 4',MMPLM_PLUGIN_NAME); ?></option>
	  
						  </select>
				  <div class="plm-note"><?php _e('Select the voting style',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-voting-style"><?php _e('Select Color',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field color">
						<fieldset>
						   <legend><?php _e('Like',MMPLM_PLUGIN_NAME); ?></legend>
						<p>
						<label for="plm-like-icon-color"><?php _e('Color',MMPLM_PLUGIN_NAME); ?></label>
						<input  type="text" class="plm-color-field" id="plm-like-icon-color" name="plm-like-icon-color" value="<?php echo (get_option('plm-like-icon-color'))?get_option('plm-like-icon-color'):'#2FED1A'; ?>">
						</p>
						<p>
						<label for="plm-like-icon-color-hover"><?php _e('Hover Color',MMPLM_PLUGIN_NAME); ?></label>
						<input  type="text" class="plm-color-field" id="plm-like-icon-color-hover" name="plm-like-icon-color-hover" value="<?php echo (get_option('plm-like-icon-color-hover'))?get_option('plm-like-icon-color-hover'):'#1ac600'; ?>">
						</p>
						</fieldset>
						<fieldset>
						   <legend><?php _e('Dislike',MMPLM_PLUGIN_NAME); ?></legend>
						<p>
						<label for="plm-dislike-icon-color"><?php _e('Color',MMPLM_PLUGIN_NAME); ?></label>
						<input  type="text" class="plm-color-field" id=="plm-dislike-icon-color" name="plm-dislike-icon-color" value="<?php echo (get_option('plm-dislike-icon-color'))?get_option('plm-dislike-icon-color'):'#c4294a'; ?>">
						</p>
						<p>
						<label for="plm-like-icon-color-hover"><?php _e('Hover Color',MMPLM_PLUGIN_NAME); ?></label>
						<input  type="text" class="plm-color-field" id="plm-dislike-icon-color-hover" name="plm-dislike-icon-color-hover" value="<?php echo (get_option('plm-dislike-icon-color-hover'))?get_option('plm-dislike-icon-color-hover'):'#c4002a'; ?>">
						</p>
						</fieldset>
						</div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-icon-size"><?php _e('Size',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						
						<input  type="number" id="plm-icon-size" name="plm-icon-size" value="<?php echo (get_option('plm-icon-size'))?get_option('plm-icon-size'):15; ?>">px
	  
				  <div class="plm-note"><?php _e('Select icon size',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>		 
	  
				  <div class="plm-form-sections clearfix">
					 <label class="plm-label" for="plm-position"><?php _e('Position Setting',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Top of Content',MMPLM_PLUGIN_NAME); ?>
						<input  type="radio" name="plm-position" value="top" <?php echo (get_option('plm-position')=='top')?'checked="checked"':''; ?> >
						<div class="control__indicator"></div>
					  </label>
					  <label class="control control--radio"><?php _e('Bottom of Content',MMPLM_PLUGIN_NAME); ?>
						<input  type="radio" name="plm-position" value="bottom" <?php echo (get_option('plm-position')=='bottom')?'checked="checked"':''; ?> >
						<div class="control__indicator"></div>
					  </label>
					 <div class="plm-note"><?php _e('Select the position where you want to show the like options. Applicable when shortcode option set to no.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				  <div class="plm-form-sections clearfix">
					  <label class="plm-label" for="plm-alignment"><?php _e('Alignment',MMPLM_PLUGIN_NAME); ?></label>
					  <div class="plm-input-field">
						<label class="control control--radio"><?php _e('Left',MMPLM_PLUGIN_NAME); ?>
						   <input  type="radio" name="plm-alignment" value="left" <?php echo (get_option('plm-alignment')=='left')?'checked="checked"':''; ?> >
						   <div class="control__indicator"></div>
					  </label>
						 <label class="control control--radio"><?php _e('Right',MMPLM_PLUGIN_NAME); ?>
						   <input  type="radio" name="plm-alignment" value="right" <?php echo (get_option('plm-alignment')=='right')?'checked="checked"':''; ?> >
						   <div class="control__indicator"></div>
					  </label>
				  <div class="plm-note"><?php _e('Select the alignment whether to show on left or on right.Applicable when shortcode option set to no.',MMPLM_PLUGIN_NAME); ?></div>
					  </div>
				  </div>
				  
						<div class="plm-form-sections clearfix">
						   <div class="pure-button submit-holder clearfix">
							<button type="submit" class="submit_button" value="Submit"><i class="fa fa-floppy-o" aria-hidden="true"></i>
	  <?php _e('Save Options',MMPLM_PLUGIN_NAME); ?></button>
						   <button type="reset" class="reset" value="Reset"><i class="fa fa-undo" aria-hidden="true"></i>
	  <?php _e('Reset',MMPLM_PLUGIN_NAME); ?></button>
						   </div>
						 </div>
				  </form>
						  </div>
						  <div>
			  <?php 
			  $liked_posts_result = MMPLM_Functions::get_all_post_list(5);
			  extract($liked_posts_result);
			  ?>
					 
				  <?php if($total>=1){?>
				   <div class="plm-post-like-table" >
					 <div class="overlay"><i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i>
	  </div>
					 <div class="pure-button submit-holder clearfix">
						<button type="button" class="deleteall cd-popup-trigger" data-delete-id ="all">
						   <i class="fa fa-trash-o" aria-hidden="true"></i>
	  <?php _e('Delete All',MMPLM_PLUGIN_NAME); ?></button>
					 </div>
					  <table class="pure-table pure-table-bordered">
						<thead>
						  <tr>
							  <th><?php _e('Post Title',MMPLM_PLUGIN_NAME); ?></th>
							  <th><?php _e('Likes',MMPLM_PLUGIN_NAME); ?></th>
							  <th><?php _e('Action',MMPLM_PLUGIN_NAME); ?></th>
						  </tr>
						</thead>
						<tbody>
				  <?php foreach ($entries as $entry){?>
						  <tr id="liked-<?php echo $entry->post_id; ?>">
							  <td><?php echo get_the_title($entry->post_id); ?></td>
							  <td> <?php echo $entry->values; ?></td>
							  <td>
							  <a class="btn btn-danger cd-popup-trigger" href="#" data-delete-id ="<?php echo $entry->post_id;?>">
							  <i class="fa fa-trash-o" title="Delete" aria-hidden="true"></i>
							  <span class="sr-only "><?php _e('Delete',MMPLM_PLUGIN_NAME); ?></span>
							  </td>
							  </a>
						  </tr>
						 <?php } ?>
						</tbody>
					 </table>
	  <div class="cd-popup" role="alert">
		  <div class="cd-popup-container">
			  <p><?php _e('Action',MMPLM_PLUGIN_NAME); ?><?php _e('Are you sure you want to delete this element?',MMPLM_PLUGIN_NAME); ?></p>
			  <ul class="cd-buttons">
				  <li><a href="#" id="delete_confirm" ><i class="fa fa-check fa-4x"  aria-hidden="true"></i></a></li>
			  </ul>
			  <a href="#0" class="cd-popup-close img-replace"><?php _e('Close',MMPLM_PLUGIN_NAME); ?></a>
		  </div> <!-- cd-popup-container -->
	  </div> <!-- cd-popup -->
			<?php MMPLM_Functions::get_like_pagination($num_of_pages,$pagenum);?>
			  </div>
			  <?php } else {?>
			  <p><?php _e('No Post Liked..',MMPLM_PLUGIN_NAME); ?></p>
			  <?php } ?>
						  </div>
				  </div>
					  </div>
				  </div>
				  <div class="plm-right-content" >
					 <div class="paypal-donations" >
						<h3><?php _e('Donate',MMPLM_PLUGIN_NAME); ?></h3>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" >
						   <input type="hidden" name="cmd" value="_donations">
						   <input type="hidden" name="business" value="manidip143@gmail.com">
						   <input type="hidden" name="return" value="<?php echo $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>">
						   <input type="text" name="amount" value="5">
						   <input type="hidden" name="currency_code" value="USD">
						   <button value="Submit" class="submit_button" type="submit"><?php _e('Donate',MMPLM_PLUGIN_NAME); ?></button>
						</form>
					 </div>
					 <div class="shortcode">
						<h3><?php _e('Shortcode',MMPLM_PLUGIN_NAME); ?></h3>
						<p><input type="text" class="view-shortcode" value="[mm-plm]"></p>
						<p><?php _e('This shortcode takes an id parameter for which you want the like buttons.',MMPLM_PLUGIN_NAME)?> <br/><?php _e('If you using it within a loop, then this option is optional.',MMPLM_PLUGIN_NAME); ?><br/> <?php _e('If you use this outside a loop and not passing id then it will display like button for that particular page.',MMPLM_PLUGIN_NAME); ?></p>
					 </div>
				  </div>
			  </div>
			  <div class="plm-footer clear">
				  
			  </div>
		  </div>
	  <?php  
	  }
	  function mmplm_include_admin_js_css(){
			  if ( isset($_GET['page']) && $_GET['page'] == 'post-like-manager' ) {
				  wp_enqueue_script('jquery');
				  wp_enqueue_script( 'jquery-form' );
			  
				  wp_register_style('plm-admin-css',MMPLM_CSS_DIR_URL.'backend/style.css');
				  wp_register_style('plm-font-awesome-css',MMPLM_CSS_DIR_URL.'backend/font-awesome.min.css');
				  wp_register_style('plm-easy-responsive-tabs-css',MMPLM_CSS_DIR_URL.'backend/easy-responsive-tabs.css');
				  wp_register_style('image-picker-css',MMPLM_CSS_DIR_URL.'backend/image-picker.css');
				  wp_register_style('notify-css',MMPLM_CSS_DIR_URL.'backend/notify.css');
				  wp_register_style('tables-min',MMPLM_CSS_DIR_URL.'backend/tables-min.css');
				  
				  
				  wp_enqueue_style('plm-admin-css');
				  wp_enqueue_style('plm-font-awesome-css');
				  wp_enqueue_style('plm-easy-responsive-tabs-css');
				  wp_enqueue_style('image-picker-css');
				  wp_enqueue_style('notify-css');
				  wp_enqueue_style('tables-min');
				  wp_enqueue_style( 'wp-color-picker' ); 
				  
				  
				  wp_register_script('plm-easyResponsiveTabs-js',MMPLM_JS_DIR_URL.'backend/easyResponsiveTabs.js');
				  wp_register_script('image-picker.min-js',MMPLM_JS_DIR_URL.'backend/image-picker.min.js');
				  wp_register_script('notify-js',MMPLM_JS_DIR_URL.'backend/notify.js');
				  wp_register_script('jquery.easy-confirm-dialog',MMPLM_JS_DIR_URL.'backend/jquery.easy-confirm-dialog.js');
				  wp_register_script('plm-admin-js',MMPLM_JS_DIR_URL.'backend/script.js',array( 'wp-color-picker' ),false,true);
				  
				  wp_enqueue_script('plm-easyResponsiveTabs-js');
				  wp_enqueue_script('image-picker.min-js');
				  wp_enqueue_script('notify-js');
				  wp_enqueue_script('jquery.easy-confirm-dialog');
				  wp_enqueue_script('plm-admin-js');
				  
				  wp_localize_script( 'plm-admin-js', 'plm_admin_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
			 
			 
			  }
	  }  
   }
}