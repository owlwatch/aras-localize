<?php
function aras_custom_japanese_excerpt_length($length) {
  // Check if the current language is Japanese
  if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE == 'ja-jp') {

      return 3; // Set the number of words you want for Japanese excerpts
  }
  return $length; // Default excerpt length for other languages
}
add_filter('excerpt_length', 'aras_custom_japanese_excerpt_length', 999999999);

if( get_current_user_id() == 46 ){
  wp_die('before header');
}

get_header(); ?>

<?php $default_post_archive_url = get_permalink(get_option('page_for_posts')); ?>

<section id="short-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('glossary_archive_headline', 'option')) : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>"><?php echo get_field('glossary_archive_headline', 'option'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline <?php echo $h1color; ?>">Glossary</h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<main class="glossary-archive mediumtoppadding largebottompadding bg-white" role="main">
  <div class="grid-container">
    <section class="grid-x grid-padding-x blog-post-loop">
      <?php if (have_posts()) : $postCount = 0; ?>
        <div class="glossary-filter-bar cell" data-glossary-filters></div>
        <dl class="glossary cell small-12" data-glossary-terms>
          <?php while (have_posts()) :
            $postCount++; ?>
            <?php the_post(); ?>
            <?php
            $title = get_the_title();
            // get the first letter
            $letter = strtolower(substr($title, 0, 1));
            $letters[$letter] = true;
            ?>
            <div class="glossary-row">
              <dt>
                <a aria-label="<?php the_title_attribute() ?>" class="glossary-link" title="<?php the_title_attribute() ?>" href="<?php the_permalink() ?>">
                  <?php /*  
            <?php if (get_field('glossary_term_headline')) : ?>
                <h4 class="hero-headline"><?php echo get_field('glossary_term_headline'); ?></h4>
              <?php else : ?>
                <h4 class="hero-headline"><?php the_title(''); ?></h4>
              <?php endif; ?>
              */ ?>
                  <h4 class="hero-headline"><?php the_title(''); ?></h4>
                </a>
              </dt>
              <?php if (get_the_excerpt()) : ?>
                <?php /* <p class="content"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p> */ ?>
                <?php the_excerpt() ?>
              <?php else : ?>
                <?php if (get_field('hero_content')) : ?>
                  <p class="content"><?php echo wp_trim_words(get_field('hero_content'), 30); ?></p>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </dl>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <?php get_template_part('parts/content', 'missing'); ?>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php get_template_part('parts/_template_parts/footer_cta_glossary'); ?>
<?php get_footer(); ?>