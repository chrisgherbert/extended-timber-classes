<?php 

namespace bermanco\TimberClasses;

class Base {

	protected $post;
	protected $type = 'website';
	protected $logo_url;

	public function __construct(\TimberPost $post){
		$this->post = $post;
	}

	public function set_logo_url($logo_url){
		$this->logo_url = $logo_url;
	}

	public function get_data(){

		$parts = array();

		$parts[] = $this->get_title();
		$parts[] = $this->get_url();
		$parts[] = $this->get_type();
		$parts[] = $this->get_description();

		// Add images
		$images = $this->get_images();

		if ($images){
			foreach ($images as $image){
				$parts[] = array(
					'key' => 'og:image',
					'value' => $image
				);
			}
		}

		return $parts;

	}

	public function get_url(){

		return array(
			'key' => 'og:url',
			'value' => $this->post->get_link()
		);

	}

	public function get_title(){

		return array(
			'key' => 'og:title',
			'value' => html_entity_decode($this->post->get_title(), ENT_QUOTES, 'UTF-8')
		);

	}

	public function get_type(){

		return array(
			'key' => 'og:type',
			'value' => $this->type
		);

	}

	public function get_description(){

		$description = '';

		if ($this->post->get_content()){
			$description = $this->post->get_preview(40, '', false);
		}
		else {
			$description = get_bloginfo('description');
		}

		if ($description){
			return array(
				'key' => 'og:description',
				'value' => $description
			);
		}

	}

	public function get_images(){

		$images = array();

		// Featured image
		if ($this->post->thumbnail()){
			$images[] = $this->post->get_thumbnail('full');
		}

		// First content image
		$first_content_image = $this->post->get_first_content_image();
		if ($first_content_image){
			$images[] = $first_content_image;
		}

		// Site logo - include only when no other images exist
		if (!$images){
			$images[] = $this->logo_url;
		}

		return $images;

	}

}

