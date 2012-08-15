/*
 * Alpine PhotoTile for Pinterest: Widget Menu Display and Nesting
 * By: Eric Burger, http://thealpinepress.com
 * Version: 1.0.0
 * Updated: August 2012
 * 
 */

if( !jQuery().APTFPINbyTAPWidgetMenuPlugin ){
  (function( w, s ) {
    s.fn.APTFPINbyTAPWidgetMenuPlugin = function( options ) {
      // Create some defaults, extending them with any options that were provided
      options = s.extend( {}, s.fn.APTFPINbyTAPWidgetMenuPlugin.options, options );

      return this.each(function(i) { 
        var theParent = s(this);
        var triggerClass = theParent.attr('data-trigger');
        
        if(triggerClass){
          var selector = s('select',theParent);
          var theChildren = s('.'+triggerClass);
          var theHidden = s('.'+triggerClass+'.'+selector.val());
          theChildren.show();
          theHidden.hide();
          //theChildren.css({'opacity':'1'});
          //theHidden.css({'opacity':'0.3'});

          selector.change(function(){
            theHidden = s('.'+triggerClass+'.'+selector.val());
            theChildren.show();
            theHidden.hide();
          });
        }
      });
    }
  })( window, jQuery );
}
