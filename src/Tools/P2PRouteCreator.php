<?php

namespace bermanco\ExtendedTimberClasses\Tools;

class P2PRouteCreator {

	protected $containing_post_type;
	protected $contained_post_type;
	protected $p2p_connection_type;

	protected $template = 'sub-archive.php';
	protected $title;

	public function __construct($containing_post_type, $contained_post_type, $p2p_connection_type){

		$this->containing_post_type = $containing_post_type;
		$this->contained_post_type = $contained_post_type;
		$this->p2p_connection_type = $p2p_connection_type;

	}

	public function set_template_file($template){
		$this->template = $template;
	}

	public function set_title($title){
		$this->title = $title;
	}

	public function create_route(){

		$route_string = $this->containing_post_type . "/:name/" . $this->contained_post_type;

		\Routes::map($route_string, function($params){

			$posts = get_posts(array(
				'name' => $params['name'],
				'post_type' => $this->containing_post_type
			));

			if ($posts){

				$wp_post = $posts[0];

				$query = array(
					'post_type' => $this->contained_post_type,
					'connected_type' => $this->p2p_connection_type,
					'connected_items' => $wp_post->ID,
				);

			}

			$params += array(
				'archive_title' => $this->get_archive_title_string($wp_post, $this->contained_post_type),
				'containing_post_type' => $this->containing_post_type,
				'contained_post_type' => $this->contained_post_type,
				'p2p_connection_type' => $this->p2p_connection_type
			);

			\Routes::load($this->template, $params, $query, 200);

		});

		$paginated_route_string = $this->containing_post_type . "/:name/" . $this->contained_post_type . "/page/:pg";

		\Routes::map($paginated_route_string, function($params){

			$posts = get_posts(array(
				'name' => $params['name'],
				'post_type' => $this->containing_post_type,
			));

			if ($posts){

				$wp_post = $posts[0];

				$query = array(
					'post_type' => $this->contained_post_type,
					'connected_type' => $this->p2p_connection_type,
					'connected_items' => $wp_post->ID,
					'paged' => $params['pg'],
				);

				$params += array(
					'archive_title' => $this->get_archive_title_string($wp_post, $this->contained_post_type),
					'containing_post_type' => $this->containing_post_type,
					'contained_post_type' => $this->contained_post_type,
					'p2p_connection_type' => $this->p2p_connection_type,
				);

			}

			\Routes::load($this->template, $params, $query, 200);

		});

	}

	public function get_archive_title_string($containing_post, $contained_post_type){
		
		if ($this->title){
			return $this->title;
		}

		$title_string = "Archives";

		if (isset($containing_post->post_title)){
			$title_string .= ": " . apply_filters('the_title', $containing_post->post_title);
		}

		$post_type_obj = get_post_type_object($contained_post_type);

		if (isset($post_type_obj->labels)){
			$title_string .= " / " . $post_type_obj->labels->name;
		}

		return $title_string;

	}

}

