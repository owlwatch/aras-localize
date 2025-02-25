<?php
use function Aras\Marketplace\get_first_term;

$contributor = get_first_term('mp-contributor');
?>
<div class="mp-card mp-card--solution">
	<a class="mp-card__header" href="<?php echo get_permalink(); ?>">
		
		<?php if( has_post_thumbnail() ){ ?>
			<?php the_post_thumbnail('medium', ['class' => 'mp-card__header-image']); ?>
		<?php }else if( ($default_solution_icon = get_field('default_solution_icon', $contributor )) ){ ?>
			<?php echo wp_get_attachment_image( $default_solution_icon['id'], 'medium', ['class' => 'mp-card__header-image']); ?>
		<?php } ?>

		<span class="mp-card__title">
			<?php the_title(); ?>
		</span>
	</a>

	<div class="mp-card__content">
		<?php
		if( $contributor ){
			?>
		<div class="mp-card__contributor">
			By 
			<a href="<?php echo get_term_link( $contributor ); ?>">
				<?php echo $contributor->name; ?>
			</a>
		</div>
			<?php
		}
		?>
		<a class="mp-card__excerpt" href="<?php echo get_permalink(); ?>">
			<?php
			echo get_field('brief_description');
			?>
		</a>
		<div class="mp-card__meta">
			<?php
			// show the "type"
			$type = get_first_term('mp-solution-type');
			if( $type ){
				?>
				<div class="mp-type-label">
					<?php
					$icon = get_field('icon', $type);
					if( $icon ){
						echo wp_get_attachment_image( $icon['ID'], 'medium', false, [
							'class' => 'mp-type-label__icon'
						]);
					}
					?>
					<span class="mp-type-label__name">
						<?php echo $type->name; ?>
					</span>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>