<?php
namespace Aras\Marketplace;
use function Aras\Marketplace\app;
use Aras\Marketplace\Service\Template;
app()->templateService->enqueue_style();
get_header();

Template::get_template_part('marketplace/banner');
?>
<section class="smalltoppadding mediumbottompadding">
	<div class="grid-container">

		<div class="mp-solution-filters">
			<?php
			Template::get_template_part('marketplace/solution', 'filters');
			?>
		</div>

		<?php
		if (have_posts()) {
			?>
			<div class="mp-solution-grid">
				<?php
				while (have_posts()) {
					the_post();
					Template::get_template_part('marketplace/card', 'solution');
				}
				?>
			</div>
			<?php
		} else {
			?>
			<div class="cell">
				<p><?php _e('No solutions found.', 'aras-marketplace'); ?></p>
			</div>
			<?php
		}
		?>
	</div>
</section>
<?php
get_footer();