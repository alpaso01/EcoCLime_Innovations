
<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} // phpcs:ignore ?>

<?php
$post_id = $product->ID;
// Default values
$included_appointment_types = array();
$included_appointment_types_ids = array();
$enabled = false;
		
// Get product settings for this post/membership
$product_settings = get_post_meta( $post_id, SSA_Mepr_Membership::$ssa_product_post_meta_key_str );

if ( ! empty( $product_settings[0] ) ) {
  $product_settings = $product_settings[0];
  $enabled = $product_settings[ SSA_Mepr_Membership::$ssa_product_is_enabled_str ];
  $included_appointment_types = $product_settings[ SSA_Mepr_Membership::$ssa_product_product_settings_str ];

  if( ! empty( $included_appointment_types ) ) {
    foreach ($included_appointment_types as $id => $settings) {
      if( empty( $settings['active'] ) ) {
        continue;
      }
      $included_appointment_types_ids[] = $id;
    }
  }
}

$appointment_types = $this->plugin->appointment_type_model->query( array( 'status' => 'publish' ) );

?> 
<div class="product_options_page appointments"> 

<!-- In case there are no appointment types created -->
<?php if(empty($appointment_types)): ?>

  <div class="ssa-memberpress-memberships-empty-container">
    <p>
      <?= __('Looks like Simply Schedule Appointments isn\'t fully set up yet.', 'simply-schedule-appointments'); ?>
    </p>
    <span>
      <?= sprintf( __( 'Head to %1$s the SSA dashboard  %2$s to get that squared away.', 'simply-schedule-appointments' ),
                          '<a href="' . $this->plugin->wp_admin->url( 'ssa/appointment-types/all' ) . '" target="_blank">',
                          '</a>'
                        );
      ?>
    </span>
  </div>

<?php else: ?>

  <div 
    class="ssa-memberpress-memberships-container" 
    style="width: fit-content; display:block;"
  >					
    <div>
      <input 
        aria-describedby="ssa-memberpress-helperText" 
        type="checkbox" 
        name="ssa-mepr-include-appointments" 
        id="ssa-mepr-include-appointments" <?php checked($enabled); ?> 
      />
      <label for="ssa-mepr-include-appointments">
        <?= __('Do you want to include appointments with this membership?', 'simply-schedule-appointments' ); ?>
      </label>
      <span 
        id="ssa-memberpress-helperText" 
        class="ssa-memberpress-helperText" 
        style="display: <?php  echo $enabled ? 'none' : 'block'; ?>"
        aria-hidden="<?php  echo $enabled ? 'true' : 'false'; ?>"
      >
        <?= __('Enable this feature to allow members to book appointments as part of their membership.', 'simply-schedule-appointments' ); ?>
        
        <!-- TODO Set the guide url  -->
        <a href="https://simplyscheduleappointments.com/guides/memberpress-booking-subscriptions" target="_blank">
          <?= __('Learn more', 'simply-schedule-appointments'); ?>
        </a>

      </span>
      
    </div>

    <div
      id="ssa-mepr-appointments-block" 
      style="display: <?= $enabled ? 'block' : 'none'; ?>"
      aria-hidden="<?= $enabled ? 'false' : 'true'; ?>"
    >
      <div>
        <p 
          id="ssa-mepr-note-not-setup" 
          class="ssa-mepr-note"
          style="display: <?php  echo empty( $included_appointment_types ) ? 'block' : 'none'; ?>"
          aria-hidden="<?php  echo empty( $included_appointment_types ) ? 'false' : 'true'; ?>"

          >
          <?= __('No appointment types are set up for this membership yet.', 'simply-schedule-appointments'); ?>
        </p>
        <p 
          id="ssa-mepr-note-offers"
          class="ssa-mepr-note"
          style="display: <?= ! empty( $included_appointment_types ) ? 'block' : 'none'; ?>"
          aria-hidden="<?= ! empty( $included_appointment_types ) ? 'false' : 'true'; ?>"
          >
          <?= __('This Membership offers the following appointments', 'simply-schedule-appointments'); ?>:
        </p>
      </div>
      
      <!-- Appointment types table -->
      <div 
        id="ssa-mepr-appointment-type-table"
        style="display:none;"	
      >
        <div 
          class="ssa-mepr-flex-column" 
          style="margin: 5px 0 15px 0;">

          <!-- foxy logo -->
          <div class="ssa-mepr-image-wrapper"> 
            <img 
              src= <?= $this->plugin->url('assets/images/foxes/logo-ssa.svg') ?> 
              alt="Simply Schedule Appointments Foxy Icon" 
              class="ssa-mepr-foxy-icon"
              >
          </div>

          <!-- Here the render method in javascript will populate the table content  -->
          <div id="ssa-mepr-appointment-type-table-content"></div>

        </div>
      </div> <!-- End Appointment types table -->

      <!-- Appointment types Dropdown -->
      <div>
        <label for="ssa_apptTypesDropdown">
          <?= __('Add a new appointment type', 'simply-schedule-appointments'); ?>:
        </label>
        <select 
          name="appointment-types" 
          id="ssa_apptTypesDropdown"
          aria-description="<?= __('Click the add button to choose the selected option', 'simply-schedule-appointments'); ?>"
        >
          <?php foreach ($appointment_types as $appointment_type):
              $is_included = in_array( $appointment_type['id'], $included_appointment_types_ids );
              ?>
              <option 
                value="<?= $appointment_type['id']; ?>"
                data-title="<?= $appointment_type['title']; ?>"
                <?= $is_included ? 'disabled' : '' ; ?>
                aria-disabled="<?= $is_included ? 'true' : 'false'; ?>"
                >
                <?= $appointment_type['title']; ?>
              </option>
            <?php endforeach; ?>
        </select>

        <button 
          id="ssa-mepr-action-btn" 
          class="button"
        >
          <span class="ssa-screen-reader-text">
            <?= __('Add the selected appointment type', 'simply-schedule-appointments'); ?>
          </span>
          <span aria-hidden="true">
            <?= __('Add', 'simply-schedule-appointments'); ?>
          </span>
        </button>
      </div> <!-- End Appointment types Dropdown -->
    </div>
<div>

<?php endif; ?>

</div>

