$(function () {
  // ============================
  // トップページのヒーローバナー用のvh調整
  // ============================
  // (iOSのMobileSafariはアドレスバーの大きさも含んだ画面サイズになり、
  // スワイプ時などでずれ込むなど挙動が変化するため、jQueryで大きさを決める)
  $('.p-landing__hero').height(window.innerHeight + 'px');

  // ============================
  // SPバーガーメニュー
  // ============================
  $('body').removeClass('u-scroll-prevent');

  spnav = $('.js-toggle-sp-nav');

  // 背景がスクロールしないように固定する
  $('.js-toggle-sp-menu').on('click', function () {
    $(this).toggleClass('active');
    spnav.toggleClass('active');

    if (spnav.hasClass('active')) {
      scrollPosition = $(window).scrollTop();
      $('body').toggleClass('u-scroll-prevent').css({ top: -scrollPosition });
    } else {
      scrollPosition = $(window).scrollTop();
      $('body').toggleClass('u-scroll-prevent').css({ top: 0 });
      window.scrollTo(0, scrollPosition);
    }
  });
  
  // ==========================
  // スムーススクロール(ふわっと出る)
  // ==========================
  // 対象はトップページ
  // Android4.4未対応
  
  // ==========================
  // フラッシュメッセージのfadeout
  // ==========================
  $('.js-flash-system-message').fadeOut(5000);
});
