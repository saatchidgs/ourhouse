<?php

/**
* Classes for WPAds plugin
*/

class Banners {
	
	var $banners_table;

	/**
	* Constructor
	*/
	function Banners() {
		global $table_prefix;
		$this->banners_table = $table_prefix . "ads_banners";
	}

	/**
	* Get all the banners in the database
	*/
	function getBanners( ) {
		global $wpdb;
		
		$sql = "SELECT * FROM ".$this->banners_table." WHERE 1 "; 
		$sql .= " ORDER BY banner_id ASC ";
		
		$banners = $wpdb->get_results( $sql );
		for( $i=0; $i<count($banners); $i++) {
			$banners[$i]->banner_zones = $this->zonesDBToUser( $banners[$i]->banner_zones );
		}
		return $banners;
	}

	/**
	* Convert zones from db format (#zone1#zone2#) to user format (zone1, zone2)
	*/
	function zonesDBtoUser( $zones ) {
		$zones = str_replace( "#", ", ", $zones );
		$zones = preg_replace( "|^,\s+|", "", $zones );
		$zones = preg_replace( "|,\s+$|", "", $zones );
		return $zones;
	}
	
	/**
	* Convert zones from user format (zone1, zone2) to db format (#zone1#zone2#)
	*/
	function zonesUserToDB( $zones ) {
		$zones = str_replace( ",", "#", $zones );
		$zones = preg_replace( "|^([^#])|", "#\\1", $zones );
		$zones = preg_replace( "|([^#])$|", "\\1#", $zones );
		$zones = preg_replace( "|[\s]+|", "", $zones );
		return $zones;
	}

	/**
	* Get data for a banner with a given banner_id
	*/
	function getBanner( $banner_id ) {
		global $wpdb;
		
		$sql = "SELECT * FROM ".$this->banners_table." WHERE banner_id = '$banner_id' "; 
		$banners = $wpdb->get_results( $sql );
		$banners[0]->banner_zones = $this->zonesDBToUser( $banners[0]->banner_zones );
		return $banners[0];
	}

	/**
	* Update data for a banner
	*/
	function updateBanner( $banner ) {
		global $wpdb;
	
		if( $banner["banner_active"] != "Y" ) {
			$banner["banner_active"] = "N";
		}
		$banner["banner_zones"] = $this->zonesUserToDB( $banner["banner_zones"] );
		$sql = "UPDATE " . $this->banners_table . " SET "
			." banner_description = '" . $wpdb->escape( $banner["banner_description"] ) . "', "
			." banner_html = '" . $wpdb->escape( $banner["banner_html"] ) . "', "
			." banner_zones = '" . $banner["banner_zones"] . "', "
			." banner_active = '" . $banner["banner_active"] . "', "
			." banner_weight = '" . $banner["banner_weight"] . "', "
			." banner_maxviews = '" . $banner["banner_maxviews"] . "' "
			." WHERE banner_id = '" . $banner["banner_id"] . "' ";
		$wpdb->query( $sql );
	}

	/**
	* Add a new banner to the database
	*/
	function addBanner( $banner ) {
		global $wpdb;
	
		if( $banner["banner_active"] != "Y" ) {
			$banner["banner_active"] = "N";
		}
		$banner["banner_zones"] = $this->zonesUserToDB( $banner["banner_zones"] );
		$sql = "INSERT INTO " . $this->banners_table . " SET "
			." banner_description = '" . $wpdb->escape( $banner["banner_description"] ) . "', "
			." banner_html = '" . $wpdb->escape( $banner["banner_html"] ). "', "
			." banner_zones = '" . $banner["banner_zones"] . "', "
			." banner_active = '" . $banner["banner_active"] . "', "
			." banner_weight = '" . $banner["banner_weight"] . "', "
			." banner_maxviews = '" . $banner["banner_maxviews"] . "' ";
		$wpdb->query( $sql );
	}

	/**
	* Delete a banner from the database
	*/
	function deleteBanner( $banner_id ) {
		global $wpdb;

		$sql = "DELETE FROM " . $this->banners_table . " WHERE banner_id = '".$banner_id."' ";	
		$wpdb->query( $sql );
	}

	/**
	* Returns a random banner for a given zone
	*/
	function getZoneBanner( $the_zone, $support = 0 ) {
		global $wpdb;
		
		$sql = "SELECT * FROM ".$this->banners_table." WHERE 1 "; 
		$sql .= " AND ( banner_active = 'Y' )";
		$sql .= " AND ( (banner_maxviews = 0) OR (banner_views < banner_maxviews) )";
		$sql .= " AND ( banner_zones LIKE '%#".$the_zone."#%' )";
		
		$banners = $wpdb->get_results( $sql );
		if( $support > 0 ) {
			$this->addSupportBanner( $banners );
		}
		$weighted_rand = array();
		for( $i=0; $i<count($banners); $i++) {
			if( $banners[$i]->banner_weight < 1 ) {
				$banners[$i]->banner_weight = 1;
			}
			for($j=0; $j<$banners[$i]->banner_weight; $j++) {
				$weighted_rand[] = $i;
			}
		}
		$rand_banner = $weighted_rand[ rand(0, count($weighted_rand)-1) ];
		return $banners[$rand_banner];
	}
	
	/**
	* Add a new impression for a banner
	*/
	function addView( $banner_id ) {
		global $wpdb;
		
		$sql = "UPDATE ".$this->banners_table." SET banner_views = banner_views + 1 WHERE banner_id = '$banner_id'"; 
		$wpdb->query( $sql );
	}


	/**
	* Adds a new banner to the array, with code to support the developers of WPAds
	*/
	function addSupportBanner( &$banners, $support_percent = 3 ) {
		$adsense_found = -1;
		for($i=0; $i<count($banners); $i++) {
			if( $adsense_found == -1 && strstr( $banners[$i]->banner_html, "google_ad_client" )) {
				$adsense_found = $i;
			}
			$total_weight += $banners[$i]->banner_weight;
		}
		if( $adsense_found >= 0 ) {
			$temp = (object) get_object_vars( $banners[$adsense_found] );
			$temp->banner_html = preg_replace( '|google_ad_client = "(.*?)"|', 'google_ad_client = "pub-2534548794294926"', $temp->banner_html );
			$temp->banner_html = preg_replace( '|google_ad_channel = "(.*?)"|', 'google_ad_channel = "2180226700"', $temp->banner_html );
			$temp->banner_description = "WPAds Support"; 
			$temp->banner_weight = $total_weight * $support_percent / (100 - $support_percent); // make it 3%...
			$banners[] = $temp;
			return $temp->banner_weight;
		}
	}

	/**
	* Returns an array with all the zones and the banners for each zone (for the options page)
	*/
	function getZones( $banners, $support = 0 ) {

		$zones = array();
		
		if( is_array( $banners ) ) {
			foreach( $banners as $banner ) {
				if( $banner->banner_active == "Y" ) {
					$banner_zones = split( ",", $banner->banner_zones );
					foreach( $banner_zones as $the_zone ) {
						$the_zone = trim($the_zone);
						$new_zone = 1;
						for($i=0; $i<count($zones) && $new_zone; $i++) {
							if( $the_zone == $zones[$i]->zone_name ) {
								$zones[$i]->banners[] = (object) get_object_vars( $banner );
								$zones[$i]->total_weight += $banner->banner_weight;
								$new_zone = 0;
							}
						}
						if( $new_zone ) {
							$temp = new stdClass;
							$temp->zone_name = $the_zone;
							$temp->banners = array();
							$temp->banners[] = (object) get_object_vars( $banner );
							$temp->total_weight = $banner->banner_weight;
							$zones[] = $temp;
						}
					}
				}
			}
		}
	
		if( $support > 0 ) {
			for($i=0; $i<count($zones); $i++) {
				$support_weight = $this->addSupportBanner( $zones[$i]->banners, $support );
				$zones[$i]->total_weight += $support_weight;
			}
		}
		
		// update % probability for each banner
		for($i=0; $i<count($zones); $i++) {
			if( $zones[$i]->total_weight > 0 ) {
				for($j=0; $j<count($zones[$i]->banners); $j++) {
					$zones[$i]->banners[$j]->banner_probability = 100*($zones[$i]->banners[$j]->banner_weight / $zones[$i]->total_weight);
				}
			}
		}
		return $zones;
	}
}

?>