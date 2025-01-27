<?php
namespace Aras\Marketplace;
use Aras\Marketplace\Service\Template;

get_header();
?>
<section>
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell">
				<h1><?php echo get_the_title(); ?></h1>
			</div>
		</div>
		<div class="grid-x grid-padding-x">
			<?php
			if (have_posts()) {
				while (have_posts()) {
					the_post();
					?>
					<div class="cell small-12 medium-6 large-4">
						<?php
						Template::get_template_part('marketplace/card', 'solution');
						?>
					</div>
					<?php
				}
				the_posts_navigation();
			} else {
				?>
				<div class="cell">
					<p><?php _e('No solutions found.', 'aras-marketplace'); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer();