<?php
/**
 * Template Name: New Member
 */

my_force_login(); 
?>
 
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?> 
<?php endwhile; ?>
 