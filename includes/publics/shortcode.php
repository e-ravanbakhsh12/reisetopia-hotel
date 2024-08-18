<?php

namespace RHC\includes\publics;

use WP_Query;

wp_enqueue_script('range-slide', RHC_URL . 'assets/js/range-slide.js', [], RHC_VERSION, true);
wp_enqueue_script('rhc-public', RHC_URL . 'assets/js/public.js', ['jquery', 'range-slide'], RHC_VERSION, true);
wp_localize_script('rhc-public', 'rhcArr', $this->localizeArr());
wp_enqueue_style('rhc-public', RHC_URL . 'assets/css/public-style.css', [], RHC_VERSION, 'all');
wp_enqueue_style('range-slide', RHC_URL . 'assets/css/range-slide.css', [], RHC_VERSION, 'all');
wp_enqueue_style('rhc-icon', RHC_URL . 'assets/reisetopiaicon/style.css', [], RHC_VERSION, 'all');

$page = 1;
$paginationOffset = 3;

$args = [
    'post_type'  => 'reisetopia_hotel',
    'posts_per_page' => 10,
    'paged' => $page,
    'sorting' => 'date',
    'order' => 'DESC',
];



$hotelList = new WP_Query($args);
$totalPages = $hotelList->max_num_pages;
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
            <div slider id="slider-distance" class="tw-w-full">
                <div>
                    <div inverse-left style="width:0%;"></div>
                    <div inverse-right style="width:0%;"></div>
                    <div class="range" style="left:0%;right:0%;"></div>
                    <span class="thumb" style="left:0%;"></span>
                    <span class="thumb" style="left:100%;"></span>
                    <div class="sign" style="left:0%;">
                        <span id="value">0</span>
                    </div>
                    <div class="sign" style="left:100%;">
                        <span id="value">1000</span>
                    </div>
                </div>
                <input name="min-price" id="hotel-min-price" type="range" tabindex="0" value="0" max="1000" min="0" step="1" />

                <input name="max-price" id="hotel-max-price" type="range" tabindex="0" value="1000" max="1000" min="0" step="1" />
            </div>
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

    <div class="hotel-pagination-container">
        <?php if ($hotelList->max_num_pages > 1): ?>
            <div class="hotel-pagination tw-flex tw-w-full tw-pt-4 md:tw-pt-10 tw-text-sm">
                <div class="pagination-inner tw-mx-auto tw-flex tw-items-start tw-gap-1 md:tw-gap-3 tw-bg-white tw-rounded-md tw-py-2 tw-px-6">
                    <!-- Previous Page Button -->
                    <?php if ($page > 1) : ?>
                        <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-sm tw-flex-center tw-cursor-pointer tw-flex hover:tw-bg-green-1 tw-transition-all hover:tw-text-white" data-page="<?= $page - 1 ?>">
                            <i class="reisetopiaicon-arrow-left"></i>
                        </div>
                    <?php endif ?>

                    <!-- Page Numbers -->
                    <div class="page-numbers tw-flex tw-flex-wrap tw-justify-center tw-gap-3 md:tw-gap-2">

                        <?php for ($i = 1; $i <= $totalPages; $i++) :
                            // Display a placeholder for the offset  
                            if ($totalPages > 6 && $page > ($paginationOffset + 2) && $i == $paginationOffset) : ?>
                                <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-flex hover:tw-bg-green-1 hover:tw-text-white tw-transition-all" data-page="<?= $i ?>">...</div>
                            <?php endif;

                            // Display the neighboring pages for pagination  
                            if ($totalPages > 6 && $page > ($paginationOffset + 2) && ($i == $paginationOffset - 1 || $i == $paginationOffset - 2)) : ?>
                                <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all <?php echo $i == $page ? 'tw-bg-green-1 tw-text-white selected' : 'hover:tw-bg-green-1 hover:tw-text-white'; ?>" data-page="<?= $i ?>"><?= $i ?></div>
                            <?php endif;

                            // Show the current page when within the offset range  
                            if ($i >= $page - $paginationOffset && $i <= $page + $paginationOffset) : ?>
                                <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all <?php echo $i == $page ? 'tw-bg-green-1 tw-text-white selected' : 'hover:tw-bg-green-1 hover:tw-text-white'; ?>" data-page="<?= $i ?>"><?= $i ?></div>
                            <?php endif;

                            // Add ellipsis at the beginning if required  
                            if ($totalPages > 6 && $page < ($totalPages - $paginationOffset - 1) && $i == $totalPages - $paginationOffset) : ?>
                                <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center hover:tw-bg-green-1 hover:tw-text-white tw-transition-all" data-page="<?= $i ?>">...</div>
                            <?php endif;

                            // Display the last two pages  
                            if ($totalPages > 6 && ($i == $totalPages - 1 || $i == $totalPages)) : ?>
                                <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all <?php echo $i == $page ? 'tw-bg-green-1 tw-text-white selected' : 'hover:tw-bg-green-1 hover:tw-text-white'; ?>" data-page="<?= $i ?>"><?= $i ?></div>
                            <?php endif; ?>

                        <?php endfor; ?>
                    </div>

                    <!-- Next Page Button -->
                    <?php if ($page < $totalPages) : ?>
                        <div class="page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer hover:tw-bg-green-1 hover:tw-text-white tw-transition-all" data-page="<?= $page + 1 ?>">
                            <i class="reisetopiaicon-arrow-left tw-rotate-180"></i>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>