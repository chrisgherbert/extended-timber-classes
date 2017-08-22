<?php

namespace bermanco\ExtendedTimberClasses;

class Post extends \Timber\Post {

	public $PostClass = '\bermanco\ExtendedTimberClasses\Post';
	public $disable_robots = false;
	public $custom_field_prefix = '';

	public function thumbnail_wider_than($pixel_width){

		$thumbnail = $this->get_thumbnail();

		if ($thumbnail && $thumbnail->width() > $pixel_width){
			return true;
		}
		else {
			return false;
		}

	}

	/**
	 * Check if the image is large enough to be expanded to fill a content
	 * container (at least 500px wide)
	 * @return boolean True if the image is at least 500px wide
	 */
	public function has_large_thumbnail(){
		return $this->thumbnail_wider_than(500);
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

		$url_params['text'] = $this->post_title;
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
		$subject = rawurlencode($this->post_title);
		$body = rawurlencode('Check it out: ' . $this->get_link());
		return sprintf($format, $subject, $body);
	}

	/**
	 * Get Reddit share URL
	 * @return string Reddit share URL
	 */
	public function get_reddit_share_url(){

		$base_url = 'https://reddit.com/submit';

		$params = array(
			'url' => $this->link(),
			'title' => $this->post_title
		);

		return $base_url . '?' . http_build_query($params);

	}

	/**
	 * Get Tumblr share URL
	 * @return string Tumblr share URL
	 */
	public function get_tumblr_share_url(){

		$base_url = 'http://www.tumblr.com/share/link';

		$params = array(
			'url' => $this->link(),
			'name' => $this->post_title,
			'posttype' => 'link'
		);

		if ($this->get_preview()){
			$params['description'] = $this->get_preview();
		}

		return $base_url . '?' . http_build_query($params);

	}

	/**
	 * Get Pinterest share link - requires a featured image
	 * @return string Pinterest share URL
	 */
	public function get_pinterest_share_url(){

		if (!$this->thumbnail()){
			return false;
		}

		return $this->create_pinterest_share_url($this->thumbnail()->src('large'), $this->link(), $this->post_title);

	}

	protected function create_pinterest_share_url($image_url, $source_url, $description){

		$image_url = $this->thumbnail()->src('full');

		$base_url = 'https://pinterest.com/pin/create/button';

		$params = array(
			'media' => $image_url,
			'url' => $source_url,
			'description' => $description
		);

		return $base_url . '?' . http_build_query($params);

	}

	////////////////
	// Open Graph //
	////////////////

	public function get_open_graph_data(){
		$open_graph = new OpenGraph\Base($this);
		return $open_graph->get_data();
	}

	////////////
	// Schema //
	////////////

	public function get_schema_data(){
		$schema = new Schema\Base($this);
		return $schema->get_json_ld_code();
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

	//////////////////////////////
	// Facebook Shares/Comments //
	//////////////////////////////

	/**
	 * Get Facebook share count for page's URL.
	 * @return int|null Number of shares
	 */
	public function get_facebook_share_count(){

		$data = $this->get_facebook_data();

		if ($data && isset($data->share->share_count)){
			return $data->share->share_count;
		}

	}

	/**
	 * Get Facebook comment count for page's URL.
	 * @return int|null Number of comments
	 */
	public function get_facebook_comment_count(){

		$data = $this->get_facebook_data();

		if ($data && isset($data->share->comment_count)){
			return $data->share->comment_count;
		}

	}

	///////////////
	// Protected //
	///////////////

	/**
	 * Get basic data (shares, comments, etc) on the page from Facebook. This
	 * is not an authenticated call, so the method may not work for long.
	 * @return stdClass  Object of Facebook data
	 */
	protected function get_facebook_data(){

		if (isset($this->facebook_data)){
			return $this->facebook_data;
		}

		$request_url = "http://graph.facebook.com/?id=" . $this->get_link();

		$json = file_get_contents($request_url);

		if ($json){
			$this->facebook_data = json_decode($json);
			return $this->facebook_data;
		}

	}

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
			$file_obj = new \Timber\Image($meta_data);
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
		return Tools\Tools::get_file_size($file_path);
	}

}
