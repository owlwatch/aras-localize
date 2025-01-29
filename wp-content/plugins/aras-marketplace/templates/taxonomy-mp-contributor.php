<?php
namespace Aras\Marketplace;
use Aras\Marketplace\Service\Template;
use function Aras\Marketplace\app;
app()->templateService->enqueue_style();
get_header();

Template::get_template_part('marketplace/banner');

?>
<section class="mediumtoppadding mediumbottompadding">
	<div class="grid-container">
		<div class="mp-contributor-page">

		</div>
	</div>
</section>
<?php
get_footer();