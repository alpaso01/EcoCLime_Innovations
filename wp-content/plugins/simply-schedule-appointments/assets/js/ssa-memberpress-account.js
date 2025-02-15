;(function($, undefined) {
  const {ssa, user_id, included_products, past_appointments, translations} = ssaMeprAccountView;

  const $iframesContainer        = $('#ssa-mepr-booking-app-container');
  const $iframes                 = $('.ssa-mepr-booking-app-iframe-container');
  const $appointmentTableBody    = $('#ssa-mepr-appointments-table-body');
  const $appointmentsTable       = $('#mepr-account-appointment-table');
  const $pastAppointmentsSection = $('#ssa-mepr__past-appointments-section');
  const $backBtn                 = $('#ssa-mepr-back-appointments')

  // Add eventListener on Book Now buttons
  $appointmentTableBody.on('click', '.ssa-mepr-book-now', function($event, el) {
    $event.preventDefault();
    bookNow(this);
  });

  $backBtn.on('click', function() {
    $iframes.fadeOut(300);
    $iframesContainer.fadeOut(300, function(){
      $appointmentsTable.show()
      $pastAppointmentsSection.show();
    });
    render();
  })

  function bookNow(el) {
    var appointment_type_id = $(el).data('appointment_type_id');
    var membership_id = $(el).data('membership_id');
    let $iframe = $(`.ssa-mepr-booking-app-iframe-container[data-type-id="${appointment_type_id}"][data-product-id="${membership_id}"]`);

    $pastAppointmentsSection.fadeOut(300);
    $appointmentsTable.fadeOut(300, function(){
      $iframesContainer.fadeIn();
      $iframe.show();
      $('html, body').animate({
        scrollTop: $iframe.offset().top
      }, 500);
    });
  }

  function fetchCurrentCycleAppointmentsPerProduct(appointment_type_id, product_id ) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: ssa.api.root + '/memberpress/user/curr_cycle_appointments_per_product',
        method: 'GET',
        data: {
          user_id,
          appointment_type_id,
          product_id
        },
        cache: false,
        dataType: 'json',
        contentType: 'application/json',
        beforeSend: (xhr) => {
          xhr.setRequestHeader('X-WP-Nonce', ssa.api.nonce)
        }
      })
        .done((data) => {
          resolve(data.data)
        })
        .fail((xhr, status, error) => {
          reject(error)
        })
    })
  }

  function renderUpcomingAppointments(){
    var $tbody = $('#ssa-mepr-appointments-table-body');
    $tbody.empty()

    // Create an array to store all the promises
    var promises = [];
    var rows = [];

    $.each(included_products, function(index, product) {
      $.each(product.appointment_types, function(appointment_type_id, appointment_type) {
        let allowTrialBooking = appointment_type.allow_booking_during_trials
        let is_on_trial = product.is_on_trial

        if (!allowTrialBooking && is_on_trial) {
          return; // Skip this iteration
        }

        let product_id = product.product_id
        let limit_booking_per_cycle = appointment_type.limit_booking_per_cycle;
        let max_num_of_appointments_per_cycle = appointment_type.max_num_of_appointments_per_cycle;
        let is_renewable = product.is_renewable
        let is_active = appointment_type.active

        var tr = $('<tr>').addClass('mepr-appointment-row');
        let membershipCell = $('<td>').addClass('ssa-loading-indicator').text(product.membership).appendTo(tr);
        let appointmentTypesCell = $('<td>').addClass('ssa-loading-indicator').text(translations.loading + '...').appendTo(tr);
        let bookedCell = $('<td>').addClass('ssa-loading-indicator').text(translations.loading + '...').appendTo(tr)
        $tbody.append(tr);

        promises.push(fetchCurrentCycleAppointmentsPerProduct(appointment_type_id, product.product_id));
        rows.push({
          // Info
          appointment_type,
          limit_booking_per_cycle,
          max_num_of_appointments_per_cycle,
          appointment_type_id,
          is_renewable,
          is_on_trial,
          product_id,
          is_active,
          // Row & Cells
          tr,
          membershipCell,
          appointmentTypesCell,
          bookedCell,
        });
      });
    });

    // Wait for all the promises to resolve
    Promise.all(promises)
      .then(function(dataArray) {
        // Loop through the data and update the corresponding cells
        dataArray.forEach(function(appointments, i) {
          let row = rows[i];

          // If appointment type is inactive and there are no upcoming appointments, remove the row
          if (!row.is_active && getUpcoming(appointments).length == 0) {
            row.tr.remove();
            return;
          }

          row.membershipCell.removeClass('ssa-loading-indicator');
          row.appointmentTypesCell.removeClass('ssa-loading-indicator').html(parseApptTypeInfo(row));
          row.bookedCell.removeClass('ssa-loading-indicator').html(parseAppointmentsDate(appointments, row));
        });

        // Check if the tbody is empty
        if ($tbody.children().length === 0) {
          var $tr = $('<tr>');
          $('<td>').attr('colspan', '3')
                   .css({'text-align': 'center', 'padding': '20px'})
                   .text(translations.empty_table_msg)
                   .appendTo($tr);
          $tbody.append($tr);
        }

      })
  }

  function getUpcoming(appointments){
    let currentDate = new Date();

    let upcomingAppointments = appointments.filter(function(appointment){
        let appointmentEndDate = new Date(appointment.end_date + "Z");

        // Return true if the appointment's end date is in the future, false otherwise
        return appointmentEndDate > currentDate;
    });

    return upcomingAppointments;
  }

  function parseAppointmentsDate(appointments, info){
    let $output = $('<div>').addClass('ssa-mepr_cell__upcoming-appts')

    let upcomingAppts = getUpcoming(appointments);
    // If no upcoming appointments
    if(upcomingAppts.length === 0){
      $('<span>').text(translations.none).appendTo($output);

    } else {
      let $ul = $('<ul>');
      $.each(upcomingAppts, function(key, value){

        let $date = $('<span>').text(value.formatted_start_date);
  
        let $link = $('<a>', {
          target: '_blank',
          href: value.public_edit_url,
          text: translations.view_details,
          class: 'ssa-mepr_cell__upcoming-appts_link'
        });
  
        $('<li>').append(
          $date,
          $link
        ).appendTo($ul)
      })
      $ul.appendTo($output)
    }

    // Book now btn & remaining
    if(info.is_active){
      let $cellFooter = parseRemaining(appointments, info);
      $cellFooter.appendTo($output);
    }


    return $output;
  }

  function parseRemaining(appointments, info){
    let canBook = true;
    let $output = $('<div>');
    let count   = appointments.length;
    let remaining;

    if (info.is_on_trial){
      remaining = info.appointment_type.max_num_of_appointments_during_trials - count;
      if (remaining <= 0){
        $('<span>').addClass('ssa-mepr_grayed-out').text(translations.no_remaining_appointments).appendTo($output);
        canBook = false;
      }
      else if (remaining === 1) {
        $('<span>').addClass('ssa-mepr_grayed-out').text(translations.x_remaining_appointment).appendTo($output);
      }
      else {
        let remainingText = translations.x_remaining_appointments.replace('%d', remaining)
        $('<span>').addClass('ssa-mepr_grayed-out').text(remainingText).appendTo($output);
      }
    } 

    // Check for booking limit per cycle
    else if (info.limit_booking_per_cycle && !info.is_renewable){
      remaining = info.max_num_of_appointments_per_cycle - count
      if(remaining <= 0){
        $('<span>').addClass('ssa-mepr_grayed-out').text(translations.no_remaining_appointments).appendTo($output);
        canBook = false;
      } 
      else if (remaining === 1) {
        $('<span>').addClass('ssa-mepr_grayed-out').text(translations.x_remaining_appointment).appendTo($output);
      }
      else {
        let remainingText = translations.x_remaining_appointments.replace('%d', remaining)
        $('<span>').addClass('ssa-mepr_grayed-out').text(remainingText).appendTo($output);
      }
    }

    if(canBook) {
      $('<br>').prependTo($output)

      $('<button>')
        .data('appointment_type_id', info.appointment_type_id)
        .data('membership_id', info.product_id)
        .addClass(['ssa-btn-raised', 'ssa-mepr-book-now'])
        .text(translations.book_now)
        .prependTo($output)
    }
      
    return $output;
  }

  function parseApptTypeInfo(info){

    let appointment_type = info.appointment_type;

    let rowContent = $('<div>').addClass('ssa-mepr_cell__appttype');
    $('<span>').addClass('ssa-mepr_cell__appttype-title').text(appointment_type.title).appendTo(rowContent);
    $('<br>').appendTo(rowContent);

    // If the appointment type is longer active display a message instead of its settings
    if (!info.is_active) {
      $('<span>').addClass(['ssa-mepr_grayed-out','ssa-mepr_cell__appttype-info']).text(translations.typeNoLongerActive).appendTo(rowContent);
      $('<br>').appendTo(rowContent);
      $('<span>').addClass(['ssa-mepr_grayed-out','ssa-mepr_cell__appttype-info']).text(translations.accessExistingAppts).appendTo(rowContent);
    
    } else {
      $('<span>').addClass(['ssa-mepr_grayed-out','ssa-mepr_cell__appttype-info']).text(localizePerIntervalStr(appointment_type)).appendTo(rowContent);
      if (appointment_type.limit_booking_per_cycle) {
        $('<br>').appendTo(rowContent);
        $('<span>').addClass(['ssa-mepr_grayed-out','ssa-mepr_cell__appttype-info']).text(localizePerCycleStr(appointment_type)).appendTo(rowContent);
      }
    }
    // If on trial period
    if (info.is_on_trial) {
      $('<br>').appendTo(rowContent);
      $('<span>').addClass(['ssa-mepr_grayed-out','ssa-mepr_cell__appttype-info']).text(localizeTrialPeriodStr(appointment_type)).appendTo(rowContent);
    }

    return rowContent;
  }

  function localizePerIntervalStr(appointment_type){
    let per_interval = parseInt(appointment_type.per_interval);
    let interval_type = appointment_type.interval_type
    // singular - singular
    if(per_interval === 1) {
      return translations.singular_appointment_per_singular_interval.replace('%s', translations[interval_type]);
    } 
    // plural - singular
    else if (per_interval > 1){
      return translations.plural_appointment_per_singular_interval.replace('%d', per_interval).replace('%s', translations[interval_type]);
    }
  }

  function localizeTrialPeriodStr(appointment_type){
    let per_trial = parseInt(appointment_type.max_num_of_appointments_during_trials);
    if(per_trial === 1) {
      return translations.singular_appointment_during_trial;
    } 
    else if (per_trial > 1){
      return translations.plural_appointments_during_trial.replace('%d', per_trial);
    }
  }

  function localizePerCycleStr(appointment_type){
    let per_cycle = parseInt(appointment_type.max_num_of_appointments_per_cycle);
    // singular
    if(per_cycle === 1) {
      return translations.appointment_per_cycle
    }
    // plural
    else {
      return translations.appointments_per_cycle.replace('%d', per_cycle)
    }
  }

  /* ------- Past appointments section ------- */

  const $membershipIdFilter      = $('#ssa_mepr_select_membership');
  const $appointmentTypeIdFilter = $('#ssa_mepr_select_appointment_types');
  const $dateFilterFilter        = $('#ssa_mepr_select_date');
  const $statusFilter            = $('#ssa_mepr_select_status'); 

  const filters = [
    $membershipIdFilter, 
    $appointmentTypeIdFilter, 
    $dateFilterFilter, 
    $statusFilter
  ];

  filters.forEach($filter => {
    $filter.change(renderPastAppointments);
  });

  function renderPastAppointments() {
    const membershipId      = $membershipIdFilter.val();
    const appointmentTypeId = $appointmentTypeIdFilter.val();
    const dateFilter        = $dateFilterFilter.val();
    const statusFilter      = $statusFilter.val(); 

    // Filter pastAppointments based on the selected membership and appointmentType
    const filteredAppointments = past_appointments.filter(appointment => {
      let membershipMatch      = membershipId === 'any' || appointment.membership_id == membershipId;
      let appointmentTypeMatch = appointmentTypeId === 'any' || appointment.appointment_type_id == appointmentTypeId;
      let statusMatch          = statusFilter === 'any' || appointment.status == statusFilter;

      // Check if date filter matches
      const appointmentStartDate = new Date(appointment.start_date);
      const now = new Date();
      let dateMatch;

      switch (dateFilter) {
        case 'last_30_days':
          const oneMonthAgo = new Date();
          oneMonthAgo.setMonth(now.getMonth() - 1);
          dateMatch = appointmentStartDate >= oneMonthAgo;
          break;
        case 'last_90_days':
          const threeMonthsAgo = new Date();
          threeMonthsAgo.setMonth(now.getMonth() - 3);
          dateMatch = appointmentStartDate >= threeMonthsAgo;
          break;
        default:
          dateMatch = true; // default is true
      }

      return membershipMatch && appointmentTypeMatch && dateMatch && statusMatch;
    });

    if (filteredAppointments.length > 1) {
      // Sort the filtered appointments by date
      filteredAppointments.sort(function(a, b) {
        const dateA = new Date(a.start_date)
        const dateB = new Date(b.start_date);
        return dateB - dateA; // For descending order
      });
    }

    // Populate the table with the filtered appointments
    populatePastAppointmentsTable(filteredAppointments);
  }

  function populatePastAppointmentsTable(appointments) {
    // Select the table body where the rows should be inserted
    const $tableBody = $('#ssa-mepr-past-appointments-table-body');

    // Clear the table body
    $tableBody.empty();

    // If there are no past appointments, show a message
    if (appointments.length === 0) {
      const noAppointmentsRow = $(`<tr><td class="ssa-past-table-empty" colspan="3">${translations.noPastAppointments}</td></tr>`);
      $tableBody.append(noAppointmentsRow);
      return;
    }

    // Iterate over the filtered appointments and create a table row for each one
    $.each(appointments, function(index, appointment) {
        const row = $('<tr></tr>');

        const membershipCell = $('<td></td>').text(appointment.membership_title);
        row.append(membershipCell);

        const appointmentTypeCell = $('<td></td>').text(appointment.type_title);
        row.append(appointmentTypeCell);

        const appointmentCell = $('<td></td>').addClass('ssa-mepr_pastAppointmentsCell');
        const formattedDate = $('<span></span>').text(appointment.formatted_start_date);

        // Don't append the view details link for past appointments
        appointmentCell.append(formattedDate);
        row.append(appointmentCell);

        // Append the row to the table body
        $tableBody.append(row);
    });
  }

  /* ------- End Past appointments section ------- */

  function render(){
    renderUpcomingAppointments();
    renderPastAppointments();
  }

  render();

})(jQuery);
