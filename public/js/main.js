$(document).ready(function () {
  //set laravel csrf-token
  window._token = $('meta[name="csrf-token"]').attr('content')

  //[TODO] - Fix console error caused by Classic editor library.

  //set the time and date
  setTimeAndDate();
  setInterval(() => {
    setTimeAndDate();
  }, 1000);


  

  ClassicEditor.create(document.querySelector('.ckeditor'))

  moment.updateLocale('en', {
    week: { dow: 1 } // Monday is the first day of the week
  })

  $('.date').datetimepicker({
    format: 'YYYY-MM-DD',
    locale: 'en'
  })

  $('.datetime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    locale: 'en',
    sideBySide: true
  })

  $('.timepicker').datetimepicker({
    format: 'HH:mm:ss'
  })

  $('.select-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', 'selected')
    $select2.trigger('change')
  })
  $('.deselect-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', '')
    $select2.trigger('change')
  })

  $('.select2').select2()

  $('.treeview').each(function () {
    var shouldExpand = false
    $(this).find('li').each(function () {
      if ($(this).hasClass('active')) {
        shouldExpand = true
      }
    })
    if (shouldExpand) {
      $(this).addClass('active')
    }
  });

  
});

//function that sets dashboard date and time
//target id id todayDate and todayTime
function setTimeAndDate() {
  var d = new Date();
  var n = d.getDay()
  var y = d.getFullYear();
  //var m = d.getMonth();
  var m = String(d.getDate()).padStart(2, '0');

  var weekday = new Array(7);
  weekday[0] = "Sunday";
  weekday[1] = "Monday";
  weekday[2] = "Tuesday";
  weekday[3] = "Wednesday";
  weekday[4] = "Thursday";
  weekday[5] = "Friday";
  weekday[6] = "Saturday";
  var day = weekday[d.getDay()];
  var month = new Array();
  month[0] = "January";
  month[1] = "February";
  month[2] = "March";
  month[3] = "April";
  month[4] = "May";
  month[5] = "June";
  month[6] = "July";
  month[7] = "August";
  month[8] = "September";
  month[9] = "October";
  month[10] = "November";
  month[11] = "December";
  var n = month[d.getMonth()];
  var dated = n + ", " + m + ", " + y;
  var TwentyFourHour = d.getHours();
  var hour = d.getHours();
  if (hour > 12) { hour = hour - 12; }
  if (hour == 0) { hour = 12; }
  var min = d.getMinutes();
  var mid = ' PM';
  if (min < 10) { min = "0" + min; }
  if (TwentyFourHour < 12) { mid = 'AM'; }
  document.getElementById("todayDate").innerHTML = dated;
  $("#todayDate").html(dated);
  $("#todayTime").html(hour + ':' + min + mid + ", " + day);
}
