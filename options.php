<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('MMPLM_Options')){
class MMPLM_Options{
    
    public static $settings_group,$instance;
    
    
    public static function get_instance(){
        if(NULL == self::$instance){
            self::$instance = new self;
        }else{
            self::$instance;
        }
    }
    
    public function __construct(){
        
    self::$settings_group = array(
                                'plm-activate-plugin'=>1,
                                'plm-use-shortcode' => 0,
                                'plm-filter' => 1,
                                'plm-login-required-message' => __("Please login to vote.",MMPLM_PLUGIN_NAME),
                                'plm-thank-you-message' => __("Thanks for your vote.",MMPLM_PLUGIN_NAME),
                                'plm-already-voted-message' => __("You have already voted.",MMPLM_PLUGIN_NAME),
                                'plm-text-like-dislike'=> __("Like/Unlike",MMPLM_PLUGIN_NAME),
                                'plm-show-dislike-option' =>__("Like/Unlike",MMPLM_PLUGIN_NAME),
                                'plm-remove-plugin-settings'=>0,
                                'plm-can-vote'=>"always",
                                'plm-voting-style'=>"style1",
                                'plm-like-icon-color'=>"#2FED1A",
                                'plm-like-icon-color-hover'=>"#000",
                                'plm-dislike-icon-color'=>"#dd0b0b",
                                'plm-dislike-icon-color-hover'=>"#000",
                                'plm-icon-size'=>15,
                                'plm-position' => "top",
                                'plm-login-required' => 0,
                                'plm-alignment' => "left",
                                'plm-show-dislike' => 1,
                                'plm-show-plus-minus' => 0,
                                'plm-exclude-post-types'=>array('page','attachment'),
                                'plm-exclude-categories' => '',
                                'plm-exclude-posts'=>'',
                                'plm-remove-from-single-page' => '1'
                                );
    }
    
    
}
}
MMPLM_Options::get_instance();