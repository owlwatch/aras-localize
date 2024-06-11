<?php
$modnum = get_row_index();
if (get_sub_field('anchor_link')) {
    $anchor = ('id="' . get_sub_field('anchor_link') . '"');
} else {
    $anchor = ('id="comparisontable-' . $modnum . '"');
};
$header = get_sub_field('table_header');

$subscriptions = get_sub_field('subscriptions');

$column_num = $subscriptions['column_count'];

$column_1 = $subscriptions['columns']['subscription_1'];
$column_2 = $subscriptions['columns']['subscription_2'];
$column_3 = $subscriptions['columns']['subscription_3'];

$check = "<svg class=\"check\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\" xml:space=\"preserve\">
<path d=\"M17.8 43.8 0 26l5.3-5.3 12.5 12.4L44.7 6.2l5.3 5.4z\" />
</svg>";
$ex = "<svg class=\"x\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\" xml:space=\"preserve\">
<path d=\"m45.9 9.4-5.3-5.3L25 19.7 9.4 4.1 4.1 9.4 19.7 25 4.1 40.6l5.3 5.3L25 30.3l15.6 15.6 5.3-5.3L30.3 25z\" />
</svg>";

$background_color = get_sub_field('background_color');
$top_padding = get_sub_field('top_padding_amount');
$bottom_padding = get_sub_field('bottom_padding_amount');
$bg_color = '';
$toppadding = '';
$bottompadding = '';
switch ($background_color) {
    case 'transparent':
        $bg_color = 'bg-transparent';
        break;
    case 'white':
        $bg_color = 'bg-white';
        break;
    case 'grey':
        $bg_color = 'bg-grey';
        break;
    case 'dblue':
        $bg_color = 'bg-dblue';
        break;
    case 'whitetogrey':
        $bg_color = 'bg-whitetogrey';
        break;
    case 'greytowarm':
        $bg_color = 'bg-greytowarm';
        break;
    default:
        $bg_color = 'bg-transparent';
}
switch ($top_padding) {
    case 'large':
        $toppadding = 'largetoppadding';
        break;
    case 'medium':
        $toppadding = 'mediumtoppadding';
        break;
    case 'small':
        $toppadding = 'smalltoppadding';
        break;
    case 'none':
        $toppadding = 'notoppadding';
        break;
    default:
        $toppadding = 'mediumtoppadding';
}
switch ($bottom_padding) {
    case 'large':
        $bottompadding = 'largebottompadding';
        break;
    case 'medium':
        $bottompadding = 'mediumbottompadding';
        break;
    case 'small':
        $bottompadding = 'smallbottompadding';
        break;
    case 'none':
        $bottompadding = 'nobottompadding';
        break;
    default:
        $bottompadding = 'mediumbottompadding';
}

?>
<section class="comparison__table <?= "$toppadding $bottompadding $bg_color" ?>" <?= "$anchor" ?>>
    <div class="grid-container">
        <div class="grid-x grid-margin-x">
            <div class="cell small-12 fullwidthblock comparison__table--body">
                <div class="comparison__table--header <?= $bg_color ?>">

                    <h2><?= (!empty($header)) ? $header : ''; ?></h2>
                    <div>
                        <div class="comparison__table--header--subscription ">
                            <h3 class="comparison__table--header--subscription__title"><?= $column_1['title'] ?></h3>
                            <p><?= $column_1['description'] ?></p>
                            <?= $column_1['link'] ? "<a href=\"{$column_1["link"]["url"]}\" class=\"aras-button\">{$column_1["link"]["title"]}</a>" : "" ?>

                        </div>
                        <div class="comparison__table--header--subscription ">
                            <h3 class="comparison__table--header--subscription__title"><?= $column_2['title'] ?></h3>
                            <p><?= $column_2['description'] ?></p>
                            <?= $column_2['link'] ? "<a  href=\"{$column_2["link"]["url"]}\" class=\"aras-button\">{$column_2["link"]["title"]}</a>" : "" ?>
                        </div>
                        <?php if ($column_num == 3) : ?>
                            <div class="comparison__table--header--subscription">
                                <h3 class="comparison__table--header--subscription__title"><?= $column_3['title'] ?></h3>
                                <p><?= $column_3['description'] ?></p>
                                <?= $column_3['link'] ? "<a href=\"{$column_3["link"]["url"]}\" class=\"aras-button\">{$column_3["link"]["title"]}</a>" : "" ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (have_rows('sections')) :
                    while (have_rows('sections')) : the_row();
                        $heading = get_sub_field('heading'); ?>


                        <div class="comparison__table--body--section__header">
                            <h4><?= $heading ?></h4>
                        </div>
                        <?php if (have_rows('rows')) :
                            while (have_rows('rows')) : the_row();
                                $columns = get_sub_field('columns');
                                $column_1 = $columns['subscription_1'];
                                $column_2 = $columns['subscription_2'];
                                ($column_num == '3') ? $column_3 = $columns['subscription_3'] : False; ?>
                                <div class="comparison__table--body--section__row">
                                    <div class="comparison__table--body--section__row--description">
                                        <?php if (!empty(get_sub_field('line_item_link'))) :
                                            $link = get_sub_field('line_item_link') ?>

                                            <a aria-label="<?= get_sub_field('line_item'); ?>" href="<?= $link['url'] ?>" <?= (!empty($link['target'])) ? "target=\"{$link['target']}\"" : ""; ?>><?= get_sub_field('line_item'); ?> <span>→</span></a>
                                        <?php else : ?>
                                            <?= get_sub_field('line_item') ?>
                                        <?php endif; ?>
                                        <?php if (get_sub_field('hover_popup') == 'Yes') : ?>
                                            <span class="comparison__table--infoicon">
                                                <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 50 50">
                                                    <defs>
                                                        <style>
                                                            .cls-1 {
                                                                fill: #000;
                                                                stroke-width: 0px;
                                                            }
                                                        </style>
                                                    </defs>
                                                    <path class="cls-1" d="M25,50C11.2,50,0,38.8,0,25S11.2,0,25,0s25,11.2,25,25-11.2,25-25,25ZM25,3.4C13.1,3.4,3.4,13.1,3.4,25s9.7,21.6,21.6,21.6,21.6-9.7,21.6-21.6S36.9,3.4,25,3.4Z" />
                                                    <g>
                                                        <polygon class="cls-1" points="30.3 19.5 21.8 19.5 21.3 22.2 23.5 22.2 20.6 38 18.5 38 18 40.6 30.3 40.6 30.8 38 27.1 38 30.3 19.5" />
                                                        <path class="cls-1" d="M28.5,9.4c-2.2,0-3.9,1.7-3.9,3.9s1.5,3.5,3.5,3.5,3.9-1.7,3.9-3.9-1.5-3.5-3.5-3.5Z" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <div class="disclosure">
                                                <?= get_sub_field('popup_text') ?>
                                            </div>
                                        <?php endif; ?>
                                        <?= (get_sub_field('extra_text') == 'Yes') ? get_sub_field('line_item_extra') : "" ?>
                                    </div>
                                    <div class="comparison__table--body--section__row--marks">
                                        <div>
                                            <?php if ($column_1['in_subscription'] == 'true') : ?>
                                                <?= $check ?>
                                            <?php elseif ($column_1['in_subscription'] == 'text') : ?>
                                                <?= $column_1['custom_text'] ?>
                                            <?php else : ?>
                                                <?= $ex ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php if ($column_2['in_subscription'] == 'true') : ?>
                                                <?= $check ?>
                                            <?php elseif ($column_2['in_subscription'] == 'text') : ?>
                                                <?= $column_2['custom_text'] ?>
                                            <?php else : ?>
                                                <?= $ex ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($column_num == 3) : ?>
                                            <div>
                                                <?php if ($column_3['in_subscription'] == 'true') : ?>
                                                    <?= $check ?>
                                                <?php elseif ($column_3['in_subscription'] == 'text') : ?>
                                                    <?= $column_3['custom_text'] ?>
                                                <?php else : ?>
                                                    <?= $ex ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>