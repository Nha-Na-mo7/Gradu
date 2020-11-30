$(function(){
  // ============================
  // SPバーガーメニュー
  // ============================
  $('.js-toggle-sp-menu').on('click', function() {
    $(this).toggleClass('active');
    $('.js-toggle-sp-nav').toggleClass('active');
  });
  
  // ============================
  // footerをページ最下部に固定
  // ============================
  var $ftr = $('#footer');
  
  // window.innerHeightで、画面全体の高さを取得
  // $ftr.offset().topで、ドキュメントの上からfooterまでの高さを取得
  // $ftr.outerHeight()で、footer自身の高さを取得
  if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    
    // $ftr.offset().topと$ftr.outerHeight()の合計よりも、画面全体の高さが大きくなったら、styleを付け足す
    // 付け足す高さは、画面全体の高さ-footer自身の高さ。これで最下部に固定される
    $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
  }
});