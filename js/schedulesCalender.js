document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      start: 'prev,next today',
      center: 'title',
      end: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    defaultDate: new Date(),
    navLinks: true, // can click day/week names to navigate views
    editable: true,
    eventLimit: true, // allow "more" link when too many events
    events: [
      {
        title: 'All Day Event',
        start: '2024-07-01'
      },
      {
        id: 888,
        title: 'First Dose',
        start: '2024-07-10T06:00:00'
      },
      {
        id: 999,
        title: 'Second Dose',
        start: '2024-07-09T16:00:00'
      },
      {
        title: 'Child Demo Medication',
        start: '2024-07-11',
        end: '2024-07-13'
      },
      {
        title: 'Paracetamol 100mg',
        start: '2024-07-11T10:30:00',
        end: '2024-07-11T12:30:00'
      },
      {
        title: 'Meeting',
        start: '2024-07-12T10:30:00',
        end: '2024-07-12T12:30:00'
      },
    ]
  });
  calendar.render();
});
