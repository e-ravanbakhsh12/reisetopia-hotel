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