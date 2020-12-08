<?php

namespace chrisgherbert\ExtendedTimberClasses;

class Term extends \Timber\Term {

	public $PostClass = '\chrisgherbert\ExtendedTimberClasses\Post';
	public $TermClass = '\chrisgherbert\ExtendedTimberClasses\Term';
	public $custom_field_prefix;

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
		return get_term_meta($this->ID, $key, true);
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