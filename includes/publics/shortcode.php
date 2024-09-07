<?php

namespace RHC\includes\publics;

use RHC\includes\Query;
// css files
wp_enqueue_style('rhc-public', RHC_URL . 'assets/css/public-style.css', [], RHC_VERSION, 'all');
wp_enqueue_style('range-slide', RHC_URL . 'assets/css/range-slide.css', [], RHC_VERSION, 'all');
wp_enqueue_style('rhc-icon', RHC_URL . 'assets/reisetopiaicon/style.css', [], RHC_VERSION, 'all');

// js files
wp_enqueue_script('range-slide', RHC_URL . 'assets/js/range-slide.js', [], RHC_VERSION, true);
wp_enqueue_script('rhc-public', RHC_URL . 'assets/js/public.js', ['jquery', 'range-slide'], RHC_VERSION, true);
wp_localize_script('rhc-public', 'rhcArr', $this->localizeArr());
// js animation fiels
if (!isGoogleBot()) {
    wp_enqueue_script('gsap', RHC_URL . '/assets/js/gsap.min.js', '3.12.5', true);
    wp_enqueue_script('gsap-st', RHC_URL . '/assets/js/scrollTrigger.min.js', ['gsap'], '3.12.5', true);
    wp_enqueue_script('rhc-animation', RHC_URL . '/assets/js/animation.js', ['jquery', 'gsap', 'gsap-st'], RHC_VERSION, true);
}

$page = $_GET['pg'] ?? 1;
$name = $_GET['search-name'] ?? '';
$location = $_GET['location'] ?? '';
$sorting = $_GET['sorting'] ?? 'date';
$order = $_GET['order'] ?? 'DESC';
$maxPrice = $_GET['max-price'] ?? '';
$minPrice = $_GET['min-price'] ?? '';
$paginationOffset = 3;


$args = [
    'items' => 10,
    'page' => $page,
    'order' => $order,
    'sorting' => $sorting,
];
if (!empty($name)) $args['name'] = $name;
if (!empty($location)) $args['location'] = $location;
if (!empty($maxPrice)) $args['max_price'] = $maxPrice;
if (!empty($minPrice)) $args['min_price'] = $minPrice;


$query = new Query();
[$hotelList, $totalPages] = $query->getAllHotels2($args);
$inverseLeft = !empty($minPrice) ? ($minPrice / 1000 * 100) : 0;
$inverseRight = !empty($maxPrice) ? (($maxPrice - 1000) / 1000 * 100) : 0;
?>
<div class="shortcode-container tw-my-10">
    <div class="hotel-filter-container tw-flex tw-flex-col tw-gap-4 ">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 " data-anim="horizontal" data-x="-40" data-delay="0.2">
            <input type="text" name="name" id="hotel-name" class="tw-h-10 tw-rounded-md !tw-border-none tw-shadow-input tw-outline-none !tw-px-2 tw-flex-center tw-font-bold tw-placeholder-gray-300 tw-w-full" placeholder="name" value="<?= $name ?>">
            <input type="text" name="location" id="hotel-location" class="tw-h-10 tw-rounded-md !tw-border-none tw-shadow-input tw-outline-none !tw-px-2 tw-flex-center tw-font-bold tw-placeholder-gray-300 tw-w-full" placeholder="location" value="<?= $location ?>">

            <select name="data-source" id="hotel-data-source" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="ajax">Ajax</option>
                <option value="rest-api">Rest Api</option>
                <option value="rewrite-api">Rewrite Api</option>
            </select>
        </div>
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 " data-anim="horizontal" data-x="40" data-delay="0.2">
            <select name="sorting" id="hotel-sorting" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="date" <?= $sorting == 'date' ? 'selected' : '' ?>>Date</option>
                <option value="name" <?= $sorting == 'name' ? 'selected' : '' ?>>Name</option>
                <option value="price_range_min" <?= $sorting == 'price_range_min' ? 'selected' : '' ?>>Min Price</option>
                <option value="price_range_max" <?= $sorting == 'price_range_max' ? 'selected' : '' ?>>Max Price</option>
            </select>
            <select name="order" id="hotel-order" class="tw-h-10 tw-rounded-md tw-outline-none !tw-border-none tw-shadow-input !tw-px-2 tw-flex-center tw-font-bold tw-w-full">
                <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending </option>
                <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
            </select>
            <div slider id="slider-distance" class="tw-w-full">
                <div>
                    <div inverse-left style="width:<?= $inverseLeft ?>%;"></div>
                    <div inverse-right style="width:<?= $inverseRight ?>%;"></div>
                    <div class="range" style="left:<?= $inverseLeft ?>%;right:<?= $inverseRight ?>%;"></div>
                    <span class="thumb" style="left:<?= $inverseLeft ?>%;"></span>
                    <span class="thumb" style="left:<?= $inverseRight - 100 ?>%;"></span>
                    <div class="sign" style="left:<?= $inverseLeft ?>%;">
                        <span id="value"><?= $minPrice?: 0 ?></span>
                    </div>
                    <div class="sign" style="left:<?= $inverseRight - 100 ?>%;">
                        <span id="value"><?= $maxPrice ?: 1000 ?></span>
                    </div>
                </div>
                <input name="min-price" id="hotel-min-price" type="range" tabindex="0" value="<?= $minPrice?: 0 ?>" max="1000" min="0" step="1" />
                <input name="max-price" id="hotel-max-price" type="range" tabindex="0" value="<?= $maxPrice ?: 1000 ?>" max="1000" min="0" step="1" />
            </div>
        </div>
    </div>
    <div class="hotel-list-container tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-6">
        <?php if ($hotelList) foreach ($hotelList as $hotel): ?>
            <a href="<?= get_permalink($hotel['id']) ?>" class="hotel-item tw-rounded-md tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden hover:tw-shadow-lg tw-transition-all" data-anim="up" data-y="40" data-delay="0.3">
                <?php if (has_post_thumbnail($hotel['id'])): ?>
                    <?= get_the_post_thumbnail($hotel['id'], 'post-thumbnail', ['class' => 'tw-h-full tw-w-24 md:tw-w-36 tw-object-cover tw-bg-gray-300']) ?>
                <?php else: ?>
                    <div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300"><i class="reisetopiaicon-gallery tw-text-4xl tw-text-white"></i></div>
                <?php endif ?>
                <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">
                    <h3 class="tw-font-bold tw-text-lg tw-line-clamp-1 tw-text-black !tw-pb-0" title="<?= $hotel['name'] ?>"><?= $hotel['name'] ?></h3>
                    <div class="tw-font-bold tw-line-clamp-1" title="<?= $hotel['country']?>,<?= $hotel['city'] ?>">Location: <span class="tw-font-normal"><?= $hotel['country']?>,<?= $hotel['city'] ?></span></div>
                    <div class="tw-font-bold">Price: <span class="tw-font-normal"><?=$hotel['priceRange']['min']?> - <?=$hotel['priceRange']['max'] ?></span></div>
                    <div class="tw-font-bold">
                        Rating:
                        <?php for ($i = 1; $i < 6; $i++) : ?>
                            <i class="reisetopiaicon-star<?= $i <= intval($hotel['rate']) ? '-fill' : '' ?> tw-text-yellow-300"></i>
                        <?php endfor ?>
                        <span class="">( <?= $hotel['rate'] ?> )</span>
                    </div>
                </div>
            </a>
        <?php endforeach ?>
    </div>
    <div class="error-box <?= $hotelList ? 'tw-hidden' : '' ?> ?> tw-min-h-40 tw-mt-6" data-anim="up" data-y="40" data-delay="0.3">
        <div class="tw-flex tw-items-center tw-gap-4 tw-p-6  tw-rounded-md tw-bg-red-100 tw-text-red-700 tw-m-auto">
            <i class="reisetopiaicon-info-circle tw-text-4xl"></i>
            <div class="message-content tw-font-bold">Hotel not found</div>
        </div>
    </div>

    <div class="hotel-pagination-container" data-anim="up" data-y="40" data-delay="0.3">
        <?php if ($totalPages > 1): ?>
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