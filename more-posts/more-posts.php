<?php
/**
 * Plugin Name: More Posts
 * Plugin URI: https://wordpress.org/plugins/more-posts/
 * Description: Appends links to other posts under a particular category.
 * Author: Bimal Poudel
 * Author URI: http://bimal.org.np/
 * Development URI: https://github.com/bimalpoudel/
 * License: GPLv2 or later
 * Version: 3.0.1
 */

class more_posts
{
	public function init()
	{
		add_shortcode('otherposts', array($this, '_shortcode_otherposts'));
		add_filter('the_content', array($this, '_category_page_name'));
	}
	
	function _category_page_name($content)
	{
		#if(!is_single()) return $content;
		if(!is_singular('post')) return $content;
		
		$categories = get_the_category();
		$cat_ids = array(0);
		foreach($categories as $category)
		{
			$cat_ids[] = $category->cat_ID;
		}
		
		$args = array(
			'posts_per_page' => '50',
			'category__in' => $cat_ids,
		);
		$queries = new WP_Query( $args );

		$li = array();
		while ( $queries->have_posts() )
		{
			$queries->the_post();
			$permalink = get_the_permalink($queries->post->ID);
			$li[] = '<li><a href="'.$permalink.'">' . get_the_title( $queries->post->ID ) . '</a></li>';
		}
		if(!count($li)) return $content;

		$links = implode('', $li);
		
		
	$content .= "
	<h2 class='more-posts' style='margin-top: 30px;'>More from: {$categories[0]->name}</h2>
	<div><ol>{$links}</ol></div>
	";
		return $content;
	}
}

$more_posts = new more_posts();
$more_posts->init();
