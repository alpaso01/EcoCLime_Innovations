<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} // phpcs:ignore ?>

<div id="ssa_mepr_accountview_table_container" class="mp_wrapper">
  <table id="mepr-account-appointment-table" class="mepr-account-table">
    <thead>
      <tr>
        <th><?php echo __('Membership', 'simply-schedule-appointments'); ?></th>
        <th><?php echo __('Appointment types', 'simply-schedule-appointments'); ?></th>
        <th><?php echo __('Upcoming appointments', 'simply-schedule-appointments'); ?></th>
      </tr>
    </thead>
    <tbody id="ssa-mepr-appointments-table-body" >
      <!-- The body will be populated with javascript  -->
    </tbody>
  </table>
</div>
		