<?php
use function Aras\Marketplace\get_first_term;

$contributor = get_first_term('mp-contributor');
?>
<div class="mp-card mp-card--solution">
	<div class="mp-card__header">
		
	<?php if( has_post_thumbnail() ){ ?>
		<?php the_post_thumbnail('medium', ['class' => 'mp-card__header-image']); ?>
		<?php } ?>

		<div class="mp-card__header-title">
			<a href="<?php echo get_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</div>
		
	</div>
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
		<div class="mp-card__excerpt">
			<?php
			echo get_field('mp_brief_description');
			?>
		</div>
		<div class="mp-card__meta">
			<?php
			// show the "type"
			$type = get_first_term('mp-type');
			if( $type ){
				$logo = get_field('logo', $type);
				if( $logo ){
					echo wp_get_attachment_image( $logo->ID, 'medium');
				}
			}
			?>
		</div>
	</div>
</div>