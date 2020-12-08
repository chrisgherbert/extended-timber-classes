<?php

namespace chrisgherbert\ExtendedTimberClasses\TwitterCard;

class Base {

	protected $type = 'summary_large_image';
	protected $site_handle;
	protected $author_handle;
	protected $player_url;
	protected $player_width;
	protected $player_height;
	protected $image_url;
	protected $fallback_image_url;

	/////////////
	// Setters //
	/////////////

	/**
	 * Change the card type
	 * Default: 'summary_large_image'
	 * Options: 'summary', 'player'
	 * @param string $type Card type
	 */
	public function set_type($type){
		$this->type = is_string($type) ? $type : null;
	}

	/**
	 * Required for ALL Twitter cards
	 * Handle associated with the site
	 * @param string $handle Twitter handle
	 */
	public function set_site_handle($handle){
		$this->site_handle = is_string($handle) ? $handle : null;
	}

	/**
	 * Optional. Handle of author
	 * @param string $handle Author's Twitter handle
	 */
	public function set_author_handle($handle){
		$this->author_handle = is_string($handle) ? $handle : null;
	}

	/**
	 * Must be the HTTPS full screen embed URL, e.g.
	 * https://www.youtube.com/embed/Es5hQczPLys
	 * @param string $embed_url
	 */
	public function set_player_url($embed_url){
		if (filter_var($embed_url, FILTER_VALIDATE_URL) !== false){
			$this->player_url = $embed_url;
		}
	}

	/**
	 * Required for player card. Should be the same dimensions
	 * as the image passed along too
	 * @param int $height Height of player/image
	 * @param int $width  Width of player/image
	 */
	public function set_player_dimensions($height, $width){
		$this->player_height = is_int($height) ? $height : null;
		$this->player_width = is_int($width) ? $width : null;
	}

	/**
	 * Required for player card
	 * @param string $url
	 */
	public function set_image_url($url){
		if (filter_var($url, FILTER_VALIDATE_URL) !== false){
			$this->image_url = $url;
		}
	}

	/**
	 * Set a fallback image URL
	 * @param string $url
	 */
	public function set_fallback_image_url($url){
		if (filter_var($url, FILTER_VALIDATE_URL) !== false){
			$this->fallback_image_url = $url;
		}
	}

	/////////////
	// Getters //
	/////////////

	public function get_type(){
		if ($this->type) {
			return array(
				'key' => 'twitter:card',
				'value' => $this->type
			);
		}
	}

	public function get_site_attribution(){
		if ($this->site_handle) {
			return array(
				'key' => 'twitter:site',
				'value' => $this->site_handle
			);
		}
	}

	public function get_author_attribution(){
		if ($this->author_handle){
			return array(
				'key' => 'twitter:creator',
				'value' => $this->author_handle
			);
		}
	}

	public function get_image_url(){
		if ($this->image_url){
			return array(
				'key' => 'twitter:image',
				'value' => $this->image_url
			);
		}
		else if ($this->fallback_image_url){
			return array(
				'key' => 'twitter:image',
				'value' => $this->fallback_image_url
			);
		}
	}

	public function get_player_url(){
		if ($this->player_url){
			return array(
				'key' => 'twitter:player',
				'value' => $this->player_url
			);
		}
	}

	public function get_player_width(){
		if ($this->player_width){
			return array(
				'key' => 'twitter:player:width',
				'value' => $this->player_width
			);
		}
	}

	public function get_player_height(){
		if ($this->player_height){
			return array(
				'key' => 'twitter:player:height',
				'value' => $this->player_height
			);
		}
	}

	/**
	 * Aggregates all the required player tags
	 * Only returns if all present
	 * @return array
	 */
	public function get_player_tags(){

		$tags = array();

		if ($this->get_player_url()){
			$tags[] = $this->get_player_url();
		}
		if ($this->get_player_width()){
			$tags[] = $this->get_player_width();
		}
		if ($this->get_player_height()){
			$tags[] = $this->get_player_height();
		}
		if ($this->get_image_url()){
			$tags[] = $this->get_image_url();
		}

		// Only return if everything is there
		// all tags are required for player to work
		if (count($tags) == 4){
			return $tags;
		}

	}

	/**
	 * Returns array of the tag data
	 * @return array
	 */
	public function get_data(){

		$parts = array();

		$parts[] = $this->get_type();

		if ($this->get_site_attribution()){
			$parts[] = $this->get_site_attribution();
		}
		if ($this->get_author_attribution()){
			$parts[] = $this->get_author_attribution();
		}
		if ($this->get_player_tags()){
			foreach ($this->get_player_tags() as $tag) {
				$parts[] = $tag;
			}
		}
		else if ($this->get_image_url()){
			$parts[] = $this->get_image_url();
		}

		return $parts;

	}

}