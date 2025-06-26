<?php global $post;
if ($post->post_parent == 20987) { ?>

    <style type="text/css">
        .language-pack-description h3 {
            margin: 2rem 0 1rem 0;
        }

        .language-pack-description p {
            margin: 0 0 .5rem 0;
        }

        .language-packs .accordion-title {
            font-family: "azo-sans-web", sans-serif;
            font-size: 1.25rem;
            font-weight: 300;
            line-height: 1;
            margin-bottom: 1.5rem;
            color: #4f4f4f;
            border: none;
            background: white;
            border-bottom: 1px solid #d6d6d6;
            margin-bottom: 0;
        }

        .language-packs .accordion-title:before {
            margin-top: 0;
            transform: translateY(-50%);
        }

        .language-packs .accordion-item.is-active .accordion-title {
            border-bottom: 0;
        }

        .language-packs .accordion-content {
            border-top: 0;
            border-right: 0;
            border-bottom: 1px solid #d6d6d6;
            border-left: 0;
        }

        .language-packs .accordion-content p {
            font-size: .9375rem;
            margin: 0 0 .5rem 0;
            color: #4f4f4f;
        }

        .language-packs .accordion-content a {
            font-weight: 500;
        }
    </style>

    <?php
    $description = get_field('description');
    $owner = get_field('owner');
    $license = get_field('license');
    $versions = get_field('versions');
    $created = get_field('created');
    $last_modified_date = get_field('last_modified_date');
    $last_modified_time = get_field('last_modified_time');
    ?>

    <section class="content-section language-pack-container">
        <div class="grid-container mediumtoppadding mediumbottompadding">
            <div class="grid-x grid-margin-x align-top">
                <div class="large-5 medium-6 small-12 cell language-pack-description">
                    <?php echo $description; ?>
                    <h3>Project Info</h3>
                    <?php if ($owner) {
                        echo '<p><strong>Owner:</strong> ' . $owner . '</p>';
                    } ?>
                    <?php if ($license) {
                        echo '<p><strong>License:</strong> ' . $license . '</p>';
                    } ?>
                    <?php if ($versions) {
                        echo '<p><strong>Supported Versions:</strong> ' . $versions . '</p>';
                    } ?>
                    <?php if ($last_modified_date) {
                        echo '<p><strong>Last Modified:</strong> ' . $last_modified_date . ' ' . $last_modified_time . '</p>';
                    } ?>
                </div>
                <div class="large-6 medium-6 small-12 large-offset-1 cell">
                    <?php if (have_rows('packs')) : ?>
                        <ul class="accordion language-packs" data-accordion data-allow-all-closed="true" data-multi-expand="true">
                            <?php while (have_rows('packs')) : the_row();
                                $title = get_sub_field('title');
                                $build = get_sub_field('build');
                                $created = get_sub_field('created');
                                if ($created) {
                                    $created = wp_date('F j, Y', strtotime($created));
                                }
                                $download = get_sub_field('download');
                                $download_short = basename($download);
                                $file_size = get_sub_field('file_size');
                            ?>
                                <li class="accordion-item<?php if (get_row_index() == 1) {
                                                                echo ' is-active';
                                                            } ?>" data-accordion-item>
                                    <a href="#" class="accordion-title"><?php echo $title; ?></a>
                                    <div class="accordion-content" data-tab-content>
                                        <?php if ($build) {
                                            echo '<p><strong>Build:</strong> ' . $build . '</p>';
                                        } ?>
                                        <?php if ($created) {
                                            echo '<p><strong>Created On:</strong> ' . $created . '</p>';
                                        } ?>
                                        <?php if ($download) { ?><p><a aria-label="<?php echo $download_short; ?>" href="<?php echo $download; ?>" target="_blank"><?php echo $download_short; ?></a></p><?php } ?>
                                        <?php if ($file_size) {
                                            echo '<p><strong>File Size:</strong> ' . $file_size . '</p>';
                                        } ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>

    <?php } ?>