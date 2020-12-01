$(function () {
  // ============================
  // SPバーガーメニュー
  // ============================
  $(".js-toggle-sp-menu").on("click", function () {
    $(this).toggleClass("active");
    $(".js-toggle-sp-nav").toggleClass("active");
  });

  // フラッシュメッセージのfadeout
  $(".js-flash-system-message").fadeOut(5000);
});
