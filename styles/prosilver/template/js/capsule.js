$('div#capsule-switch-bbcode').click(function (e) {
  $('div#img-filename').toggle().css('margin', '9px');
  $('div#img-bbcode').toggle().closest('h5').css('margin', '0');
})