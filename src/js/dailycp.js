require(['jquery', 'jqueryui/datepicker'], function($) {
  var cal = $('#ucotd_cal');
  cal.datepicker({
    dateFormat: "yy-mm-dd",
    defaultDate: cal.data('date'),
    minDate: "2011-05-04",
    maxDate: "+0",
    onSelect: function(dateText, inst) {
      window.location.href = "/codepoint_of_the_day?date="+dateText;
    }
  });
});
