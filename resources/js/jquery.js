$(function () {
  
  
  
  
  // ============================
  // トップページのvh調整
  // ============================
  $(".p-landing__hero").height(window.innerHeight + "px");
  
  // ============================
  // SPバーガーメニュー
  // ============================
  $("body").removeClass('u-scroll-prevent')
  
  spnav = $(".js-toggle-sp-nav");
  
  // 背景がスクロールしないように固定する
  $(".js-toggle-sp-menu").on("click", function () {
    $(this).toggleClass("active");
    spnav.toggleClass("active");
    
    if(spnav.hasClass("active")) {
      scrollPosition = $(window).scrollTop();
      $("body").toggleClass('u-scroll-prevent').css({'top': - scrollPosition});
    }else {
      scrollPosition = $(window).scrollTop();
      $("body").toggleClass('u-scroll-prevent').css({'top': 0});
      window.scrollTo(0, scrollPosition);
    }
  });

  // ==========================
  // フラッシュメッセージのfadeout
  // ==========================
  $(".js-flash-system-message").fadeOut(5000);
  
});
