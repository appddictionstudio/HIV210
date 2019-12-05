<!-- NOTE: If you need to make changes to this file, copy it to your current theme's main directory so your changes won't be overwritten when the plugin is upgraded. -->

<!-- Start of Post Wrap -->
<?php if (has_post_thumbnail()) : ?>
  <div class="card">
    <img class="card-img-top img-fluid" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title(); ?>">
    <div class="card-block">
<?php else : ?>
  <div class="card card-block">
<?php endif; ?>
    <h4 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
    <p class="card-text"><?php the_excerpt(); ?></p>
    <p class="card-text"><small class="text-muted"></small><?= get_the_date();  ?></small></p>
  </div>
</div>
<!-- // End of Post Wrap -->
