$('#demoEvoCalendar').on('selectYear', function (event, activeYear) {
  $('#demoEvoCalendar').evoCalendar('toggleEventList', false);
 
  var monthIndex = parseInt($(".calendar-months li.active-month").attr("data-month-val"));
  monthIndex = monthIndex + 1;
  if (monthIndex < 10) {
    monthIndex = "0" + monthIndex;
  }
  var date = activeYear + "-" + monthIndex;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    url: $('meta[name="appurl"]').attr('content') + "/data/slots",
    method: "POST",
    data: { date: date, type: "getByMonthSlots" },
    success: function (data) {
      $('#demoEvoCalendar').evoCalendar('addCalendarEvent', JSON.parse($.trim(data)));
    }
  })
});
// selectMonth
$('#demoEvoCalendar').on('selectMonth', function (event, activeMonth, monthIndex) {
  $('#demoEvoCalendar').evoCalendar('toggleEventList', false);
  var year = $(".calendar-year p").text();
  monthIndex = monthIndex + 1;
  if (monthIndex < 10) {
    monthIndex = "0" + monthIndex;
  }
  var date = year + "-" + monthIndex;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url: $('meta[name="appurl"]').attr('content') + "/data/slots",
    method: "POST",
    data: { date: date, type: "getByMonthSlots" },
    success: function (data) {
      var data1 = JSON.parse($.trim(data));
      $('#demoEvoCalendar').evoCalendar('removeCalendarEvent', data1.ids);
      $('#demoEvoCalendar').evoCalendar('addCalendarEvent', data1.event);
    }
  })
});

$('#demoEvoCalendar').on('selectDate', function (event, newDate, oldDate) {
  $('#demoEvoCalendar').evoCalendar('toggleEventList', true);
  $('#demoEvoCalendar').evoCalendar('toggleSidebar', false);
});
// $("#eventListToggler").click(function(){ 
$(document).on("click", "#eventListToggler", function () {
  $('#demoEvoCalendar').evoCalendar('toggleSidebar', false);
});

$(document).on("click", "#sidebarToggler", function () {
  $('#demoEvoCalendar').evoCalendar('toggleEventList', false);
});
