document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  const childDropdown = document.getElementById('childDropdown');

  let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: fetchEvents,
    eventClick: function(info) {
      showEventDetails(info.event);
    },
    eventDidMount: function(info) {
      info.el.style.cursor = 'pointer';
      info.el.title = info.event.title; // Show full title on hover
    },
    eventColor: '#378006',
  });

  calendar.render();

  $("#childDropdown").change(function (e){
    calendar.refetchEvents();
  })

  function fetchEvents(info, successCallback, failureCallback) {
    const childId = childDropdown.value;
    $.ajax({
      url: 'php/get_medications_for_calendar.php',
      method: 'GET',
      data: {
        child_id: childId
      },
      success: function(response) {
        console.log('AJAX Response:', response);  // Log the response

        let data;
        try {
          data = typeof response === 'string' ? JSON.parse(response) : response;  // Parse JSON response if it's a string
        } catch (error) {
          console.error('Error parsing JSON:', error);
          console.error('Response:', response);
          failureCallback();
          return;
        }

        if (Array.isArray(data)) {
          const events = data.map(medication => {
            return {
              title: medication.title.split(':')[1].trim(), // Short title for calendar
              start: medication.start,
              color: medication.color,
              extendedProps: medication.extendedProps
            };
          });
          successCallback(events);
        } else {
          console.error('Response is not an array:', data);
          failureCallback();
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        failureCallback();
      }
    });
  }

  function showEventDetails(event) {
    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    document.getElementById('modalTitle').innerText = event.title;
    document.getElementById('modalBody').innerHTML = `
            <p><strong>Child Name:</strong> ${event.extendedProps.child_name}</p>
            <p><strong>Medication Name:</strong> ${event.extendedProps.medication_name}</p>
            <p><strong>Time:</strong> ${formatTime(event.extendedProps.time)}</p>
            <p><strong>Start Date:</strong> ${event.extendedProps.start_date}</p>
            <p><strong>End Date:</strong> ${event.extendedProps.end_date}</p>
        `;
    modal.show();
  }

  function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const period = +hours >= 12 ? 'PM' : 'AM';
    const formattedHours = +hours % 12 || 12;
    return `${formattedHours}:${minutes} ${period}`;
  }
});
