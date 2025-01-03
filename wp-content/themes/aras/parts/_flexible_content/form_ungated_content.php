<section class="post-submission-content mediumtoppadding largebottompadding box-bg-white">
    <div class="grid-container">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <?php if (get_sub_field('post_submission_content')) : ?>
                    <div class="wysiwyg-content" style="margin-bottom: 2rem">
                    <?php echo get_sub_field('post_submission_content'); ?>
                    </div>
                <?php endif; ?>
                <?php $link = get_sub_field('post_submission_button');
                if ($link) : $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                ?>
                    <a aria-label="<?php echo esc_html($link_title); ?>" class="aras-button" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                        <?php echo esc_html($link_title); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>