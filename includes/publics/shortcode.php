<?php

namespace RHC\includes\publics;

use WP_Query;

wp_enqueue_script('rhc-public', RHC_URL . 'assets/js/public.js', array('jquery'), RHC_VERSION, true);
wp_enqueue_style('rhc-public', RHC_URL . 'assets/css/public-style.css', array(), RHC_VERSION, 'all');
wp_enqueue_style('rhc-icon', RHC_URL . 'assets/reisetopiaicon/style.css', array(), RHC_VERSION, 'all');
wp_localize_script('rhc-public', 'rhcArr', $this->localizeArr());

$args = [
    'post_type'  => 'reisetopia_hotel',
    'posts_per_page' => -1,
];

$hotelList = new WP_Query($args);
?>
<div class="shortcode-container tw-my-10">
    <div class="hotel-filter-container tw-flex tw-flex-col tw-gap-4 ">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 ">
            <input type="text" name="name" id="hotel-name" class="tw-h-10 tw-rounded-md !tw-border-none tw-shadow-input tw-outline-none !tw-px-2 tw-flex-center tw-font-bold tw-placeholder-gray-300 tw-w-full" placeholder="name">
            <input type="text" name="location" id="hotel-location" class="tw-h-10 tw-rounded-md !tw-border-none tw-shadow-input tw-outline-none !tw-px-2 tw-flex-center tw-font-bold tw-placeholder-gray-300 tw-w-full" placeholder="location">

            <select name="data-source" id="hotel-data-source" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="ajax">Ajax</option>
                <option value="rest-api">Rest Api</option>
            </select>
        </div>
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 ">
            <select name="sorting" id="hotel-sorting" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="date">Date</option>
                <option value="name">Name</option>
                <option value="price_range_min">Min Price</option>
                <option value="price_range_max">Max Price</option>
            </select>
            <select name="order" id="hotel-order" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="DESC">Descending </option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
    </div>
    <div class="hotel-list-container tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-6">
        <?php if ($hotelList->have_posts()): ?>
            <?php foreach ($hotelList->posts as $hotel):
                $country = get_post_meta($hotel->ID, 'country', true);
                $city = get_post_meta($hotel->ID, 'city', true);
                $priceMax = get_post_meta($hotel->ID, 'price_range_max', true);
                $priceMin = get_post_meta($hotel->ID, 'price_range_min', true);
                $rate = get_post_meta($hotel->ID, 'rating', true);
            ?>
                <a href="<?= get_permalink($hotel->ID) ?>" class="hotel-item tw-rounded-md tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden hover:tw-shadow-lg tw-transition-all">
                    <?php if (has_post_thumbnail($hotel->ID)): ?>
                        <?= get_the_post_thumbnail($hotel->ID, 'post-thumbnail', ['class' => 'tw-h-full tw-w-24 md:tw-w-36 tw-object-cover tw-bg-gray-300']) ?>
                    <?php else: ?>
                        <div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300"><i class="reisetopiaicon-gallery tw-text-4xl tw-text-white"></i></div>
                    <?php endif ?>
                    <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">
                        <h3 class="tw-font-bold tw-text-lg tw-line-clamp-1 tw-text-black !tw-pb-0" title="<?= $hotel->post_title ?>"><?= $hotel->post_title ?></h3>
                        <div class="tw-font-bold tw-line-clamp-1" title="<?= "$country, $city" ?>">Location: <span class="tw-font-normal"><?= "$country, $city" ?></span></div>
                        <div class="tw-font-bold">Price: <span class="tw-font-normal"><?= "$priceMin - $priceMax" ?></span></div>
                        <div class="tw-font-bold">
                            Rating:
                            <?php for ($i = 1; $i < 6; $i++) : ?>
                                <i class="reisetopiaicon-star<?= $i <= intval($rate) ? '-fill' : '' ?> tw-text-yellow-300"></i>
                            <?php endfor ?>
                            <span class="">( <?= $rate ?> )</span>
                        </div>
                    </div>
                </a>
            <?php endforeach ?>
        <?php
            wp_reset_postdata();
        endif ?>
    </div>
    <div class="error-box tw-hidden tw-min-h-40 tw-mt-6">
        <div class="tw-flex tw-items-center tw-gap-4 tw-p-6  tw-rounded-md tw-bg-red-100 tw-text-red-700 tw-m-auto">
            <i class="reisetopiaicon-info-circle tw-text-4xl"></i>
            <div class="message-content tw-font-bold"></div>
        </div>
    </div>
    <div class="hotel-list-pagination ">

    </div>
</div>