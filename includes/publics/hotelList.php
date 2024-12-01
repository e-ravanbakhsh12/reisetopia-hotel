<?php if ($hotelList) foreach ($hotelList as $hotel): ?>
    <a href="<?= get_permalink($hotel['id']) ?>" class="hotel-item tw-rounded-md tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden hover:tw-shadow-lg tw-transition-all" data-anim="up" data-y="40" data-delay="0.3">
        <?php if (has_post_thumbnail($hotel['id'])): ?>
            <?= get_the_post_thumbnail($hotel['id'], 'post-thumbnail', ['class' => 'tw-h-full tw-w-24 md:tw-w-36 tw-object-cover tw-bg-gray-300']) ?>
        <?php else: ?>
            <div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300"><i class="reisetopiaicon-gallery tw-text-4xl tw-text-white"></i></div>
        <?php endif ?>
        <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">
            <h3 class="tw-font-bold tw-text-lg tw-line-clamp-1 tw-text-black !tw-pb-0" title="<?= $hotel['name'] ?>"><?= $hotel['name'] ?></h3>
            <div class="tw-font-bold tw-line-clamp-1" title="<?= $hotel['country'] ?>,<?= $hotel['city'] ?>">Location: <span class="tw-font-normal"><?= $hotel['country'] ?>,<?= $hotel['city'] ?></span></div>
            <div class="tw-font-bold">Price: <span class="tw-font-normal"><?= $hotel['priceRange']['min'] ?> - <?= $hotel['priceRange']['max'] ?></span></div>
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