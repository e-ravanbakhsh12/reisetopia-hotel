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
        <?= $this->generateHotelList($hotelList) ?>
    </div>
    <div class="error-box <?= $hotelList ? 'tw-hidden' : '' ?> ?> tw-min-h-40 tw-mt-6" data-anim="up" data-y="40" data-delay="0.3">
        <div class="tw-flex tw-items-center tw-gap-4 tw-p-6  tw-rounded-md tw-bg-red-100 tw-text-red-700 tw-m-auto">
            <i class="reisetopiaicon-info-circle tw-text-4xl"></i>
            <div class="message-content tw-font-bold">Hotel not found</div>
        </div>
    </div>

    <div class="hotel-pagination-container" data-anim="up" data-y="40" data-delay="0.3">
    <?= $this->generatePagination($totalPages,$page) ?>
    </div>
</div>