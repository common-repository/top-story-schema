<?php
/*
Plugin Name: Top Story Schema
Description: Add Google article schema to all Posts
Version: 1.0.0
Author: Panutat Latplee
Author URI: p_latplee@hotmail.com
License: GPLv2 or later
Text Domain: tss
*/

class TSS_Plugin {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'head' ] );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
	}

	function head() {
		global $wp, $post;

		if($post->post_type != 'post') return;

		// Site Logo
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$site_logo = wp_get_attachment_url( $custom_logo_id );

		$featured_images_1 = get_the_post_thumbnail_url( $post, [300, 300]);
		$featured_images_2 = get_the_post_thumbnail_url( $post, [800, 600]);
		$featured_images_3 = get_the_post_thumbnail_url( $post, [1024, 576]);

		$data = [
			'@context' => 'https://schema.org',	
			'@type' => 'NewsArticle',
			'mainEntityOfPage' => [
				'@type' => 'WebPage',
				'@id' => home_url( $wp->request ),
			],
			'headline' => $post->post_title,
			'image' => [
				$featured_images_1, // 1x1
				$featured_images_2, // 4x3
				$featured_images_3, // 16x9
			],
			'datePublished' => $post->post_date,
			'dateModified' => $post->post_modified,
			'author' => [
				'@type' => 'Person',
				'name' => get_the_author_meta('display_name', $post->post_author),
			],
			'publisher' => [
				'@type' => 'Organization',
				'name' => get_bloginfo('name'),
			],
		];

		if(!empty($site_logo)) {
			$data['publisher']['logo']['@type'] = 'ImageObject';
			$data['publisher']['logo']['url'] = $site_logo;
		}

		echo "<!-- Top Story Schema -->\n";
		echo "<script type='application/ld+json'>". wp_json_encode( $data ). "</script>\n";
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if($plugin_file == plugin_basename(__FILE__)) {
			$row_meta = [
				'donate' => '<a href="https://www.paypal.me/panutatlatplee" aria-label="Donate" target="_blank"  style="background: #F92C8B; border-radius: 3px; padding: 1px 3px; color: #fff;">Donate Me</a>',
			];
			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}
		return $plugin_meta;
	}

}

$tss_plugin = new TSS_Plugin();
