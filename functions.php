<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if(!class_exists('MMPLM_Functions')){
	class MMPLM_Functions {
		
		private static function mmplm_filter_count($number){
			
			$precision = 0;
			if ( $number >= 1000 && $number < 1000000 ) {
				$formatted = number_format( $number/1000, $precision ).'K';
			} else if ( $number >= 1000000 && $number < 1000000000 ) {
				$formatted = number_format( $number/1000000, $precision ).'M';
			} else if ( $number >= 1000000000 ) {
				$formatted = number_format( $number/1000000000, $precision ).'B';
			} else {
				$formatted = $number; // Number is less than 1000
			}
			return $formatted;
		
		}
		
		//Get Client IP address	
		public static function mmplm_get_ip_address() {
			if (getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_X_FORWARDED')) {
				$ip = getenv('HTTP_X_FORWARDED');
			} elseif (getenv('HTTP_FORWARDED_FOR')) {
				$ip = getenv('HTTP_FORWARDED_FOR');
			} elseif (getenv('HTTP_FORWARDED')) {
				$ip = getenv('HTTP_FORWARDED');
			} elseif(getenv('REMOTE_ADDR')){
				$ip = $_SERVER['REMOTE_ADDR'];
			}else{
					$ip="0.0.0.0";
				}
			
			$ip = filter_var( $ip, FILTER_VALIDATE_IP );
			$ip = ( $ip === false ) ? '0.0.0.0' : $ip;
			return $ip;
		}
	
	
		public static function mmplm_is_user_already_liked($post_id){
		
			global $wpdb,$is_like_found;
			
			if(is_user_logged_in ())
			{
			
				 $user_ID = get_current_user_id();
				 $is_like_found = $wpdb->get_var(
											$wpdb->prepare(  
												 "SELECT COUNT(*) FROM ".MMPLM_TABLE_NAME." WHERE `user_id`=%d AND `post_id`=%d",
												 $user_ID,$post_id
												 )
											);                  
			}else{
			
				$is_like_found = $wpdb->get_var(
										$wpdb->prepare(
												"SELECT COUNT(*) FROM ".MMPLM_TABLE_NAME." WHERE `ip`=%s AND `post_id`=%d",
												MMPLM_CLIENT_IP_ADDRESS,$post_id
												)
										);
				
			}
			
			if($is_like_found>=1){
				return true;
			}else{
				return false;
			}
			
		}
	
	
		public static function mmplm_get_last_like_date($post_id){
				
			global $wpdb;
			$last_liked_date = $wpdb->get_var(
												$wpdb->prepare(
														"SELECT `date_time` FROM ".MMPLM_TABLE_NAME." WHERE `post_id`=%d AND `ip`=%s",
														$post_id,MMPLM_CLIENT_IP_ADDRESS
															   )
												);
				
			return $last_liked_date;
		
		}
	
	
		public static function  mmplm_get_can_like_again_date($last_like_date,$like_period){
			
			$day = $month = $year = 0;
			
			switch($like_period)
			{
				case "1d":$day = 1;break;
				case "1w": $day = 7;break;
				case "1m":$month = 1;break;
				case "3m": $month = 3;break;
				case "6m":$month = 6;break;
				case "1y": $year = 1;break;
				
			}
			
			$last_like_date_timstamp = strtotime($last_like_date);
			$next_like_date = mktime(date('H',$last_like_date_timstamp),
									 date('i',$last_like_date_timstamp),
									 date('s',$last_like_date_timstamp),
									 date('m',$last_like_date_timstamp) + $month,
									 date('d',$last_like_date_timstamp) + $day,
									 date('Y',$last_like_date_timstamp) + $year
									 );
			
			$next_like_date = date('Y-m-d H:i:s',$next_like_date);
			
			return $next_like_date;
		}
	
		public static function mmplm_get_last_action($post_id,$user_id){
			
			global $wpdb;
				$last_action = $wpdb->get_var(
												$wpdb->prepare(
															   "SELECT `last_action` FROM ".MMPLM_TABLE_NAME."
									   WHERE `post_id`=%d AND `ip`=%s",
									   $post_id,MMPLM_CLIENT_IP_ADDRESS
															   )
												);
				
			return $last_action;
			
		}
	
	
		public static function mmplm_get_like_count($post_id){
			
			global $wpdb;
			$like_count = $wpdb->get_var(
										$wpdb->prepare(
								"SELECT SUM(value) FROM ".MMPLM_TABLE_NAME."
								WHERE post_id = %d AND value >= 0",$post_id
								)
										);
			
			$like_count = ($like_count)? self::mmplm_filter_count($like_count):0;
				
			$like_count = (get_option('plm-show-plus-minus'))?'+'.$like_count:$like_count;
			
			return $like_count;
		}
	
	
		public static function mmplm_get_unlike_count($post_id){
			
			global $wpdb;
			$unlike_count = $wpdb->get_var(
										$wpdb->prepare(
								"SELECT SUM(value) FROM ".MMPLM_TABLE_NAME."
								WHERE post_id = %d AND value <=0",
								$post_id
								)
						 
					  );
			
			$unlike_count = ($unlike_count)?self::mmplm_filter_count($unlike_count):0;
			
			$unlike_count = (get_option('plm-show-plus-minus'))?'-'.str_replace('-','',$unlike_count):str_replace('-','',$unlike_count);
				
			return $unlike_count;
		}
		
		public static function get_all_post_list($limit){
			
			global $wpdb;
			$result = [];
			$pagenum = filter_input(INPUT_GET, 'pagenum') ? absint(filter_input(INPUT_GET, 'pagenum')) : 1;
			$offset = ( $pagenum - 1 ) * $limit;
			$total = $wpdb->query("SELECT * FROM ".MMPLM_TABLE_NAME." WHERE 1 GROUP BY `post_id`");
			$num_of_pages = ceil( $total / $limit );
			$entries = $wpdb->get_results( "SELECT `post_id` , sum( `value` ) AS `values`
									 FROM {$wpdb->prefix}pl_management
									 WHERE `value` > 0
									 GROUP BY `post_id`
									 ORDER BY `values` DESC
									 LIMIT {$offset},{$limit}
									 "
							  );
			
			$result['entries'] = $entries;
			$result['pagenum'] = $pagenum;
			$result['num_of_pages'] = $num_of_pages;
			$result['total'] = $num_of_pages;

			return (!empty($result['entries']))?$result:false;
			
		}
		
		public static function get_like_pagination($num_of_pages,$pagenum){
			$page_links = paginate_links( array(
			  'base' => add_query_arg( 'pagenum', '%#%' ),
			  'format' => '',
			  'prev_text' => __( '&laquo;', MMPLM_PLUGIN_NAME ),
			  'next_text' => __( '&raquo;', MMPLM_PLUGIN_NAME ),
			  'total' => $num_of_pages,
			  'current' => $pagenum
			  ) );
			  
			  if ( $page_links ) {
			  echo '<div class="navigation">' . $page_links . '</div>';
			  }
		}
		
		
	}	
}