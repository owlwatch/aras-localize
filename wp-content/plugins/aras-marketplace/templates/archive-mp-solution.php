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

		<?php
		// output landing page content if we are on the initial page
		// of the archive
		if( is_post_type_archive() && !is_paged() ){
			Template::get_template_part('marketplace/page-content', null, [
				'field_name' => 'marketplace_landing_content'
			]);
		}	
		?>

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
			// output pagination
			Template::get_template_part('marketplace/pagination');

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