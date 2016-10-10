<?php

namespace bermanco\ExtendedTimberClasses\Tools;
use bermanco\ExtendedTimberClasses\Post;
use Timber\Timber;

class FacebookPopularPosts {

	public $post_types = array('post'); // array of post types to use
	public $limit = 20; // total number of posts to check count for 
	public $time = 1209600; // Time from the present to check for new posts (default is two weeks back)

	/////////////
	// Setters //
	/////////////

	public function set_post_types(array $post_types){
		$this->post_types = $post_types;
	}

	public function set_limit($limit){
		$this->limit = $limit;
	}

	public function set_time($time_in_seconds){
		$this->time = $time_in_seconds;
	}

	public function get_facebook_view_counts(){

		$posts = $this->get_posts();

		$results = array();

		if ($posts){

			foreach ($posts as $post){

				$results[] = array(
					'shares' => $post->get_facebook_share_count(),
					'post' => $post
				);

			}

		}

		// Sort results by share count
		usort($results, function($a, $b){
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});

		// Reverse the array
		$reversed = array_reverse($results);

		return $reversed;

	}

	///////////////
	// Protected //
	///////////////

	protected function get_posts(){

		// Create start date, in seconds
		$time = time('now') - $this->time;
		$date = date('Y-m-d', $time);

		$query_args = array(
			'post_type' => $this->post_types,
			'posts_per_page' => $this->limit,
			'date_query' => array(
				array(
					'after' => $date
				)
			)
		);

		$posts = Timber::get_posts($query_args, 'bermanco\ExtendedTimberClasses\Post');

		return $posts;

	}

}