<?php

use bermanco\TimberClasses\Tools;

class BasePost extends TimberPost {

	public $PostClass = 'BasePost';
	public $disable_robots = false;
	public $custom_field_prefix = '';

	/**
	 * Gets the label of a post type, if it is defined.
	 * @param  string $label Post type slug
	 * @return string        The desired post type label
	 */
	public function get_post_type_label($label){

		$post_type_obj = get_post_type_object($this->post_type);

		if ($post_type_obj){

			if (isset($post_type_obj->labels->{$label})){
				return $post_type_obj->labels->{$label};
			}

		}

	}

	/**
	 * Check if the image is large enough to be expanded to fill a content
	 * container (at least 500px wide)
	 * @return boolean True if the image is at least 500px wide
	 */
	public function has_large_thumbnail(){

		$thumbnail = $this->get_thumbnail();

		if ($thumbnail && $thumbnail->width() > 499){
			return true;
		}
		else {
			return false;
		}

	}

	/**
	 * Get the first image embedded in the post's content
	 * @return string|null URL of the first content image
	 */
	public function get_first_content_image(){

		$first_img = '';

		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/U', $this->get_content(), $matches);

		if (isset($matches[1][0])){
			$first_img = $matches[1][0];
		}

		if ($first_img){
			return $first_img;
		}

	}

	/**
	 * Get a string with comma-separated list of the post's tag names
	 * @return string Tag names list
	 */
	public function get_tags_links_string(){

		$tags = $this->get_terms('tag');

		if ($tags){
			return $this->create_terms_links_string($tags);
		}

	}

	/**
	 * Get a string with comma-separated list of the post's category names
	 * @return string Category names list
	 */
	public function get_categories_links_string(){

		$categories = $this->get_terms('category');

		if ($categories){
			return $this->create_terms_links_string($categories);
		}

	}

	/**
	 * Get the PHP class of the current object
	 * @return string PHP class name
	 */
	public function get_php_class(){
		return get_class($this);
	}

	/**
	 * Get Facebook Share URL
	 * @return string Facebook Share URL
	 */
	public function get_facebook_share_url(){
		$base = 'https://www.facebook.com/sharer/sharer.php?u=';
		$url = rawurlencode($this->get_link());
		return $base . $url;
	}

	/**
	 * Get Twitter Tweet URL
	 * @return string Twitter Tweet URL
	 */
	public function get_twitter_tweet_url($handle = ''){

		$format = 'https://twitter.com/intent/tweet?text=%s&url=%s&via=%s';

		$url_params = array();

		$url_params['text'] = $this->get_title();
		$url_params['url'] = $this->get_link();

		if ($handle){
			$url_params['via'] = $handle;
		}

		$query_string = http_build_query($url_params, false, '&', PHP_QUERY_RFC3986);

		return "https://twitter.com/intent/tweet?$query_string";

	}

	/**
	 * Get mailto link
	 * @return string The mailto link
	 */
	public function get_mailto_url(){
		$format = 'mailto:?subject=%s&body=%s';
		$subject = rawurlencode($this->get_title());
		$body = rawurlencode('Check it out: ' . $this->get_link());
		return sprintf($format, $subject, $body);
	}

	////////////////
	// Open Graph //
	////////////////

	public function get_open_graph_data(){
		$open_graph = new \bermanco\opengraph\Base($this);
		return $open_graph->get_data();
	}

	/////////
	// RSS //
	/////////

	/**
	 * Get post title, formatted for an RSS feed
	 * @return string|null Formatted post title
	 */
	public function get_rss_title(){
		$title = $this->get_title();
		if ($title){
			return html_entity_decode( $title, ENT_COMPAT, 'UTF-8' );
		}
	}

	/**
	 * Get content formatted and sanitized for an RSS feed.  RSS only allows 
	 * specific tags, though I haven't been able to find a complete list of 
	 * them.
	 * @return string|null Post content prepared for RSS
	 */
	public function get_rss_content(){

		$content = $this->get_content();

		if ($content){

			$allowed_tags = array(
				'<p>',
				'<a>',
				'<strong>',
				'<em>',
				'<i>',
				'<b>',
				'<br>',
				'<div>',
				'<ul>',
				'<li>',
				'<blockquote>',
				'<table>',
				'<h1>',
				'<h2>',
				'<address>',
				'<article>',
				'<header>',
				'<img>',
				'<legend>',
				'<ol>',
				'<pre>',
				'<code>',
				'<small>',
				'<span>',
				'<table>',
				'<tr>',
				'<td>',
				'<th>',
				'<thead>',
				'<tbody>',
				'<sub>',
				'<u>'
			);

			$allowed_tags_string = implode('', $allowed_tags);

			$stripped_content = strip_tags($content, $allowed_tags_string);

			return $stripped_content;

		}

	}

	///////////////
	// Protected //
	///////////////

	/**
	 * Create a comma-separated list of post links
	 * @param  array  $posts Array of TimberPost objects
	 * @return string        List of comma separated links
	 */
	protected function create_posts_links_string(array $posts = null){

		if ($posts){

			$links = array();

			foreach ($posts as $post){
				$url = $post->get_link();
				$title = $post->get_title();
				$links[] = "<a href='$url'>$title</a>";
			}

			return implode(', ', $links);

		}

	}

	/**
	 * Create a comma-separated list of post titles from an array of post
	 * objects
	 * @param  array|null $posts AlecPost objects
	 * @return string            List of comma-separated post titles
	 */
	protected function create_posts_titles_string(array $posts = null){

		if ($posts){

			$items = array();

			foreach ($posts as $post){
				$items[] = $post->get_title();
			}

			return implode(', ', $items);

		}

	}

	/**
	 * Create a comma-separated list of term titles from an array of
	 * TimberTerm objects
	 * @param  array|null $terms Array of TimberTerm objects
	 * @return string            List of term titles
	 */
	protected function create_terms_titles_string(array $terms = null){

		if ($terms){

			$items = array();

			foreach ($terms as $term){
				$items[] = $term->name;
			}

			return implode(', ', $items);

		}

	}

	protected function create_terms_links_string(array $terms = null){

		if ($terms){

			$items = array();

			foreach ($terms as $term){
				$name = $term->name;
				$url = $term->get_link();
				$items[] = "<a href='$url'>$name</a>";
			}

			return implode(', ', $items);

		}

	}

	//////////
	// CMB2 //
	//////////

	/**
	 * Get the meta key for a CMB2 metadata field
	 * @param  string $id CMB2 field id (not including prefix)
	 * @return string     Complete WordPress meta key
	 */
	protected function get_cmb2_meta_key($id){
		$meta_key = $this->custom_field_prefix . $id;
		return $meta_key;
	}

	/**
	 * Get CMB2 meta data
	 * @param  string $id Meta key
	 * @return mixed      Meta value
	 */
	public function get_cmb2_meta($id){
		$key = $this->get_cmb2_meta_key($id);
		return $this->$key;
	}

	/**
	 * Get the TimberImage file attachment class from a CMB2 file attachment
	 * field
	 * @param  string $key      CMB2 field ID.  Should not include the prefix.
	 * @return TimberImage|null      TimberImage object for the file attachment
	 */
	protected function get_cmb2_image($id){

		$meta_data = $this->get_cmb2_meta($id);

		if ($meta_data){
			$file_obj = new TimberImage($meta_data);
			return $file_obj;
		}

	}

	/**
	 * Get an attachment URL from a CMB2 file attachment field
	 * @param  string $key     CMB2 field ID.  Should not include the prefix.
	 * @return string|null     Attachment URL
	 */
	protected function get_cmb2_file_attachment_url($id){

		$meta_data = $this->get_cmb2_meta($id);

		if ($meta_data){
			return wp_get_attachment_url($meta_data);
		}

	}

	protected function get_attachment_file_size($attachment_id){
		$file_path = get_attached_file($attachment_id);
		return \bermanco\TimberClasses\Tools::get_file_size($file_path);
	}

}
