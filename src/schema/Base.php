<?php

namespace bermanco\ExtendedTimberClasses\Schema;

class Base {

	protected $post;
	protected $context = 'http://schema.org';
	protected $type = 'Thing';
	protected $logo_url;

	public function __construct(\TimberPost $post){
		$this->post = $post;
	}

	public function set_logo_url($logo_url){
		$this->logo_url = $logo_url;
	}

	public function get_json_ld_code(){

		$schema_json = $this->write_schema();

		if ($schema_json){
			return "<script type='application/ld+json'>$schema_json</script>";
		}

	}

	public function write_schema(){

		$schema = $this->get_schema_object();

		$processed_schema = $this->remove_false_and_empty_items($schema);

		return json_encode($processed_schema);
	}

	public function get_schema_object(){

		$schema = array(
			'@context' => $this->context,
			'@type' => $this->type,
			'name' => $this->get_title(),
			'url' => $this->get_link()
		);

		if ($this->get_image()){

			$schema['image'] = array(
				'@type' => 'ImageObject',
				'url' => $this->get_image(),
				'width' => $this->get_image_width(),
				'height' => $this->get_image_height()
			);

		}

		if ($this->get_description()){
			$schema['description'] = $this->get_description();
		}

		return $schema;

	}

	///////////////
	// Protected //
	///////////////

	protected function get_logo_url(){
		return $this->logo_url;
	}

	protected function get_title(){
		return $this->post->get_title();
	}

	protected function get_link(){
		return $this->post->get_link();
	}

	protected function get_description(){

		$content = $this->post->get_preview(30, false, '', true);

		return strip_tags($content);

	}

	protected function get_image(){

		if ($this->post->get_thumbnail()){

			$id = get_post_thumbnail_id($this->post->ID);
			$url = wp_get_attachment_url($id);

			if ($url){
				return $url;
			}

		}

	}

	protected function get_image_height(){

		if ($this->get_image()){

			$id = get_post_thumbnail_id($this->post->ID);

			$meta = wp_get_attachment_metadata($id);

			if (isset($meta['height'])){
				return $meta['height'];
			}

		}

	}

	protected function get_image_width(){

		if ($this->get_image()){

			$id = get_post_thumbnail_id($this->post->ID);

			$meta = wp_get_attachment_metadata($id);

			if (isset($meta['width'])){
				return $meta['width'];
			}

		}

	}

	protected function get_publisher_logo(){

		if ($this->logo_url){

			$logo_obj = new \TimberImage($this->logo_url);

			$logo_schema_item = array();

			$logo_schema_item['@type'] = 'ImageObject';

			if ($logo_obj->get_src()){
				$logo_schema_item['contentUrl'] = $logo_obj->get_src();
				$logo_schema_item['url'] = $logo_obj->get_src();
			}

			if ($logo_obj->width){
				$logo_schema_item['width'] = $logo_obj->width;
			}

			if ($logo_obj->height){
				$logo_schema_item['height'] = $logo_obj->height;
			}

			return $logo_schema_item;

		}

	}

	/**
	 * Removes any falsey items - you probably don't want a bunch of "false"
	 * values in the schema for values that just don't exist.
	 * @param  array $schema Schema data
	 * @return array         Schema data with falsey values removed
	 */
	protected function remove_false_and_empty_items($schema){

		foreach ($schema as $key=>$value){

			if (!$value){
				unset($schema[$key]);
			}

		}

		return $schema;

	}

}