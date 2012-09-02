<?php
/**
 * Alpine PhotoTile for Pinterest: Widget Setup
 *
 * @since 1.1.1
 *
 */
 

class Alpine_PhotoTile_for_Pinterest extends WP_Widget {

	function Alpine_PhotoTile_for_Pinterest() {
		$widget_ops = array('classname' => 'APTFPINbyTAP_widget', 'description' => __('Add images from Pinterest to your sidebar'));
		$control_ops = array('width' => 550, 'height' => 350);
		$this->WP_Widget(APTFPINbyTAP_DOMAIN, __('Alpine PhotoTile for Pinterest'), $widget_ops, $control_ops);
	}
  
	function widget( $args, $options ) {
		extract($args);
    wp_enqueue_style('APTFPINbyTAP_widget_css');
    wp_enqueue_script('APTFPINbyTAP_tiles');
    if( $options['pinterest_pin_it_button'] ) {
      wp_enqueue_script('pinterest_pinit');
    }
    
    // Set Important Widget Options    
    $id = $args["widget_id"];
    $defaults = APTFPINbyTAP_option_defaults();
    
    $source_results = APTFPINbyTAP_photo_retrieval($id, $options, $defaults);
    
    echo $before_widget . $before_title . $options['widget_title'] . $after_title;
    echo $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          echo APTFPINbyTAP_display_vertical($id, $options, $source_results);
        break;
        case "windows":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "floor":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "wall":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          echo APTFPINbyTAP_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          echo APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
      }
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      echo 'Sorry:<br>'.$source_results['message'];
    }
    echo $after_widget;
    
  }
    
	function update( $newoptions, $oldoptions ) {
    $optiondetails = APTFPINbyTAP_option_defaults();
    if ( function_exists( 'APTFPINbyTAP_MenuOptionsValidate' ) ) {
      foreach( $newoptions as $id=>$input ){
        $options[$id] = APTFPINbyTAP_MenuOptionsValidate( $input,$oldoptions[$id],$optiondetails[$id] );
      }
    }else{
      $options = $newoptions;
    }
    return $options;
	}

	function form( $options ) {

    $widget_container = $this->get_field_id( 'APTFPINbyTAP-pinterest' ); ?>

    <div id="<?php echo $widget_container ?>" class="APTFPINbyTAP-pinterest">
    <?php
      $defaults = APTFPINbyTAP_option_defaults();
      $positions = APTFPINbyTAP_option_positions();
   
    if( count($positions) && function_exists( 'APTFPINbyTAP_MenuDisplayCallback' ) ){
      foreach( $positions as $position=>$positionsinfo){
      ?>
        <div class="<?php echo $position ?>"> 
          <?php if( $positionsinfo['title'] ){ ?><h4><?php echo $positionsinfo['title']; ?></h4><?php } ?>
          <table class="form-table">
            <tbody>
              <?php
              if( count($positionsinfo['options']) ){
                foreach( $positionsinfo['options'] as $optionname ){
                  $option = $defaults[$optionname];
                  $fieldname = $this->get_field_name( $option['name'] );
                  $fieldid = $this->get_field_id( $option['name'] );
                  if($option['parent']){
                    $class = $option['parent'];
                  }elseif($option['child']){
                    $class = $this->get_field_id($option['child']);
                  }else{
                    $class = $this->get_field_id('unlinked');
                  }
                  $trigger = ($option['trigger']?('data-trigger="'.($this->get_field_id($option['trigger'])).'"'):'');
                  $hidden = ($option['hidden']?' '.$option['hidden']:'');
                  
                  ?> <tr valign="top"> <td class="<?php echo $class; ?><?php echo $hidden; ?>" <?php echo $trigger; ?>><?php
                    APTFPINbyTAP_MenuDisplayCallback($options,$option,$fieldname,$fieldid);
                  ?> </td></tr> <?php
                }
              }?>
            </tbody>  
          </table>
        </div>
      <?php
      }
    }
    ?>
    </div> 
    <div><span><?php _e('Need Help? Visit ') ?><a href="<?php echo APTFPINbyTAP_INFO; ?>" target="_blank">the Alpine Press</a> <?php _e('for more about this plugin.') ?></span></div> 
    
    <?php
    
	}
}
  
  ?>
