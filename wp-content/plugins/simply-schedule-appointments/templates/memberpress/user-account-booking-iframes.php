<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} // phpcs:ignore ?>

<div id="ssa-mepr-booking-app-container" style="display:none;">
    <button id="ssa-mepr-back-appointments" class="ssa-btn-raised-bordered">
      &larr; &nbsp;<?= __('Back To Appointments', 'simply-schedule-appointments'); ?>
    </button>
    <?php foreach ($bookable_memberships as $membership): 
        $membership_id = $membership->get_product_id();
        $bookable_types = $user->get_bookable_types_for_membership($membership_id);
        foreach ($bookable_types as $appointment_type_id): ?>
            <div 
                class="ssa-mepr-booking-app-iframe-container" 
                data-type-id="<?= $appointment_type_id ?>" 
                data-product-id="<?= $membership_id ?>"
                style="display:none"
            >
                <!-- The iframe goes here  -->
                <?= $this->plugin->shortcodes->ssa_booking([
                    'mepr_membership_id' => $membership_id,
                    'types' => $appointment_type_id
                ]); ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>