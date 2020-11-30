$(function(){
  // ============================
  // SPバーガーメニュー
  // ============================
  $('.js-toggle-sp-menu').on('click', function() {
    $(this).toggleClass('active');
    $('.js-toggle-sp-nav').toggleClass('active');
  });
});