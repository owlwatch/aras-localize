<section id="short-hero" class="short-hero hero-banner bg-dblue">
  <div class="grid-container">
    <div class="grid-x grid-padding-x align-top">
      <div class="cell small-12 medium-10 hero-content">
        <div class="hero-content-inner">
          <?php if (get_field('glossary_term_headline')) : ?>
            <h1 class="hero-headline"><?php echo get_field('glossary_term_headline'); ?></h1>
          <?php else : ?>
            <h1 class="hero-headline"><?php the_title(''); ?></h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>