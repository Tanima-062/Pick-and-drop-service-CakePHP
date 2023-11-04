$(document).ready(function() {
  $(".sp_pagetop").click(function() {
    $("body, html").animate({ scrollTop: 0 }, 500);
    return false;
  });
});
