<?php

class BasePostTest extends WP_UnitTestCase {

	public function test_get_php_class(){

		$post_id = $this->factory->post->create();
		$post = new BasePost($post_id);

		$this->assertEquals('BasePost', $post->get_php_class());

	}

	/**
	 * @dataProvider get_rss_title_provider
	 */
	public function test_get_rss_title($title){

		$post_id = $this->factory->post->create(array(
			'post_title' => $title
		));
		$post = new BasePost($post_id);

		$this->assertEquals($title, $post->get_rss_title());

	}

	public function get_rss_title_provider(){

		return array(
			array("We’d all be better off without apostropes"),
			array("In the ‘60s, rock ‘n’ roll"),
			array("“That’s a ‘magic’ sock.”"),
			array('')
		);

	}

	/**
	 * @dataProvider get_facebook_share_url_provider
	 */
	public function test_get_facebook_share_url($expected_value, $url){

		$post_id = $this->factory->post->create();
		$object = new ShareTestClass($post_id);
		$object->link_value = $url;

		$this->assertEquals($expected_value, $object->get_facebook_share_url());

	}

	public function get_facebook_share_url_provider(){

		return array(
			array("https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.alec.org%2Fpublication%2F2016opioids%2F","https://www.alec.org/publication/2016opioids/"),
			array("https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.alec.org%2Farticle%2Fthreat-of-the-zika-virus%2F", "https://www.alec.org/article/threat-of-the-zika-virus/")
		);

	}

	/**
	 * @dataProvider get_twitter_tweet_url_provider
	 */
	public function test_get_twitter_tweet_url($expected_value, $url, $title, $handle){

		$post_id = $this->factory->post->create();
		$object = new ShareTestClass($post_id);
		$object->link_value = $url;
		$object->title_value = $title;

		$this->assertEquals($expected_value, $object->get_twitter_tweet_url($handle));

	}

	public function get_twitter_tweet_url_provider(){

		return array(
			array("https://twitter.com/intent/tweet?text=custom%20share%20text&url=https%3A%2F%2Fdev.twitter.com%2Fweb%2Ftweet-button&via=twitterdev", "https://dev.twitter.com/web/tweet-button", "custom share text", "twitterdev")
		);

	}

	/**
	 * @dataProvider get_mailto_url_provider
	 */
	public function test_get_mailto_url($expected_value, $url, $title){

		$post_id = $this->factory->post->create();
		$object = new ShareTestClass($post_id);
		$object->link_value = $url;
		$object->title_value = $title;

		$this->assertEquals($expected_value, $object->get_mailto_url());

	}

	public function get_mailto_url_provider(){

		return array(
			array("mailto:?subject=West%20Virginia%20Becomes%20the%2026th%20Right-to-Work%20State&body=Check%20it%20out%3A%20https%3A%2F%2Fwww.alec.org%2Farticle%2Fwest-virginia-becomes-the-26th-right-to-work-state%2F", "https://www.alec.org/article/west-virginia-becomes-the-26th-right-to-work-state/", "West Virginia Becomes the 26th Right-to-Work State")
		);

	}

	/**
	 * @dataProvider get_first_content_image_provider
	 */
	public function test_get_first_content_image($expected_value, $post_content){

		$post_id = $this->factory->post->create(array('post_content' => $post_content));
		$post = new BasePost($post_id);

		$this->assertEquals($expected_value, $post->get_first_content_image());

	}

	public function get_first_content_image_provider(){

		return array(
			array('http://test.com/image1.jpg', '<p><img src="http://test.com/image1.jpg" alt="" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec odio. Quisque volutpat mattis eros. Nullam malesuada erat ut turpis. Suspendisse urna nibh, viverra non, semper suscipit, posuere a, pede.</p>'),
			array(null, ' Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec odio. Quisque volutpat mattis eros. Nullam malesuada erat ut turpis. Suspendisse urna nibh, viverra non, semper suscipit, posuere a, pede.'),
			array('http://test.com/image1.jpg', '<p><img src="http://test.com/image1.jpg" alt="" />Lorem ipsum <img src="http://test.com/image2.jpg" alt="" />dolor sit amet, consectetuer adipiscing elit. Donec odio. Quisque volutpat mattis eros. Nullam malesuada erat ut turpis. Suspendisse urna nibh, viverra non, semper suscipit, <img src="http://test.com/image3.jpg" alt="" />posuere a, pede.</p>')
		);

	}

	/**
	 * @dataProvider create_posts_titles_string_provider
	 */
	public function test_create_posts_titles_string($expected_value, $post_array){

		$post_id = $this->factory->post->create();
		$post = new BasePost($post_id);

		$reflection_class = new ReflectionClass("BasePost");
		$reflection_method = $reflection_class->getMethod("create_posts_titles_string");
		$reflection_method->setAccessible(true);
		$result = $reflection_method->invoke($post, $post_array);

		$this->assertEquals($expected_value, $result);

	}

	public function create_posts_titles_string_provider(){

		$post_one = new BasePost($this->factory->post->create(array('post_title' => 'Title One')));
		$post_two = new BasePost($this->factory->post->create(array('post_title' => 'Title Two')));
		$post_three = new BasePost($this->factory->post->create(array('post_title' => 'Title Three')));

		return array(
			array('Title One, Title Two, Title Three', array($post_one, $post_two, $post_three)),
			array('Title One, Title Two', array($post_one, $post_two)),
			array('Title One', array($post_one)),
			array(null, array()),
		);

	}

	/**
	 * @dataProvider create_terms_titles_string_provider
	 */
	public function test_create_terms_titles_string($expected_value, $terms){

		$post = new BasePost($this->factory->post->create());

		$reflection_class = new ReflectionClass("BasePost");
		$reflection_method = $reflection_class->getMethod("create_terms_titles_string");
		$reflection_method->setAccessible(true);
		$result = $reflection_method->invoke($post, $terms);

		$this->assertEquals($expected_value, $result);

	}

	public function create_terms_titles_string_provider(){

		$term_1 = new TimberTerm($this->factory->term->create(array('name' => 'Term 1')));
		$term_2 = new TimberTerm($this->factory->term->create(array('name' => 'Term 2')));
		$term_3 = new TimberTerm($this->factory->term->create(array('name' => 'Term 3')));

		return array(
			array('Term 1, Term 2, Term 3', array($term_1, $term_2, $term_3)),
			array('Term 1, Term 2', array($term_1, $term_2)),
			array('Term 1', array($term_1)),
			array(null, array())
		);

	}

	/**
	 * @dataProvider create_posts_links_string_provider
	 */
	public function test_create_posts_links_string($expected_value, $posts_array){

		$post = new BasePost($this->factory->post->create());

		$reflection_class = new ReflectionClass("BasePost");
		$reflection_method = $reflection_class->getMethod("create_posts_links_string");
		$reflection_method->setAccessible(true);
		$result = $reflection_method->invoke($post, $posts_array);

		$this->assertEquals($expected_value, $result);

	}

	public function create_posts_links_string_provider(){

		$post_1 = $this->get_BasePost_mock_for_posts_links_string_test('Post 1', '/post_1.php');
		$post_2 = $this->get_BasePost_mock_for_posts_links_string_test('Post 2', '/post_2.php');
		$post_3 = $this->get_BasePost_mock_for_posts_links_string_test('Post 3', '/post_3.php');

		return array(
			array("<a href='/post_1.php'>Post 1</a>, <a href='/post_2.php'>Post 2</a>, <a href='/post_3.php'>Post 3</a>", array($post_1, $post_2, $post_3)),
			array("<a href='/post_1.php'>Post 1</a>, <a href='/post_2.php'>Post 2</a>", array($post_1, $post_2)),
			array("<a href='/post_1.php'>Post 1</a>", array($post_1)),
			array(null, array())
		);

	}

	/**
	 * @dataProvider create_terms_links_string_provider
	 */
	public function test_create_terms_links_string($expected_value, $terms_array){

		$post = new BasePost($this->factory->post->create());

		$reflection_class = new ReflectionClass("BasePost");
		$reflection_method = $reflection_class->getMethod("create_terms_links_string");
		$reflection_method->setAccessible(true);
		$result = $reflection_method->invoke($post, $terms_array);

		$this->assertEquals($expected_value, $result);

	}

	public function create_terms_links_string_provider(){

		$term_1 = $this->get_TimberTerm_mock_for_terms_links_string_test('Term 1', '/term_1.php');
		$term_2 = $this->get_TimberTerm_mock_for_terms_links_string_test('Term 2', '/term_2.php');
		$term_3 = $this->get_TimberTerm_mock_for_terms_links_string_test('Term 3', '/term_3.php');

		return array(
			array("<a href='/term_1.php'>Term 1</a>, <a href='/term_2.php'>Term 2</a>, <a href='/term_3.php'>Term 3</a>", array($term_1, $term_2, $term_3)),
			array("<a href='/term_1.php'>Term 1</a>, <a href='/term_2.php'>Term 2</a>", array($term_1, $term_2)),
			array("<a href='/term_1.php'>Term 1</a>", array($term_1)),
			array(null, array()),
		);

	}

	/**
	 * @dataProvider get_post_type_label_provider
	 */
	public function test_get_post_type_label($expected_value, array $post_type_def, $label){

		register_post_type('test-post-type', $post_type_def);

		$post_id = $this->factory->post->create(array('post_type' => 'test-post-type'));

		$post_obj = new BasePost($post_id);

		$this->assertEquals($expected_value, $post_obj->get_post_type_label($label));

	}

	public function get_post_type_label_provider(){

		$video_def = array(
			'labels' => array(
				'name' => 'Videos',
				'singular_name' => 'Video',
				'menu_name' => 'Menu Name'
			)
		);

		return array(
			array('Video', $video_def, 'singular_name'),
			array('Menu Name', $video_def, 'menu_name')
		);

	}

	///////////////
	// Protected //
	///////////////

	protected function get_BasePost_mock_for_posts_links_string_test($title, $url){

		$mock = $this->get_BasePost_mock();
		$mock->method('get_title')->willReturn($title);
		$mock->method('get_link')->willReturn($url);

		return $mock;

	}

	protected function get_BasePost_mock(){

		$stub = $this->getMockBuilder('BasePost')->disableOriginalConstructor()->getMock();

		return $stub;

	}

	protected function get_TimberTerm_mock_for_terms_links_string_test($title, $url){

		$mock = $this->get_TimberTerm_mock();
		$mock->name = $title;
		$mock->method('get_link')->willReturn($url);

		return $mock;

	}

	protected function get_TimberTerm_mock(){

		$stub = $this->getMockBuilder('TimberTerm')->disableOriginalConstructor()->getMock();

		return $stub;

	}

}

class ShareTestClass extends BasePost {

	public $link_value;
	public $title_value;

	public function get_link(){
		return $this->link_value;
	}

	public function get_title(){
		return $this->title_value;
	}

}

