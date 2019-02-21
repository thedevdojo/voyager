// Stolen from jquery-ui and customized
(function($){
  $.fn.disableSelection = function(){
    return this.bind( ( $.support.selectstart ? "selectstart" : "mousedown" ) +
      ".disableSelection", function( event ) {
      event.preventDefault();
      });
  };

  $.fn.enableSelection = function(){
    return this.unbind('.disableSelection');
  };
})(jQuery);