<?php
/**
 * Alpine PhotoTile for Pinterest: Options Validate
 *
 * @since 1.0.0
 *
 * Options Validate Version 1
 *
 */

if ( !function_exists( 'theAlpinePressMenuOptionsValidateV1' ) ) {
    function theAlpinePressMenuOptionsValidateV1( $newinput, $oldinput, $optiondetails ) {
        $valid_input = $oldinput;

        // Validate checkbox fields
        if ( 'checkbox' == $optiondetails['type'] ) {
          // If input value is set and is true, return true; otherwise return false
          $valid_input = ( ( isset( $newinput ) && true == $newinput ) ? true : false );
        }
        // Validate radio button fields
        else if ( 'radio' == $optiondetails['type'] ) {
          // Get the list of valid options
          $valid_options = $optiondetails['valid_options'];
          // Only update setting if input value is in the list of valid options
          $valid_input = ( array_key_exists( $newinput, $valid_options ) ? $newinput : $valid_input );
        }
        // Validate select fields
        else if ( 'select' == $optiondetails['type'] || 'select-trigger' == $optiondetails['type']) {
          // Get the list of valid options
          $valid_options = $optiondetails['valid_options'];
          // Only update setting if input value is in the list of valid options
          $valid_input = ( array_key_exists( $newinput, $valid_options ) ? $newinput : $valid_input );
        }
        else if ( 'range' == $optiondetails['type'] ) {
          // Only update setting if input value is in the list of valid options
          $valid_input = ( ($newinput>=$optiondetails['min'] && $newinput<=$optiondetails['max']) ? $newinput : $valid_input );
        }    
        // Validate text input and textarea fields
        else if ( ( 'text' == $optiondetails['type'] || 'textarea' == $optiondetails['type'] || 'image-upload' == $optiondetails['type']) ) {
          // Check if numeric
          if ( 'numeric' == $optiondetails['sanitize'] && is_numeric( wp_filter_nohtml_kses( $newinput ) ) ) {
            // Pass input data through the wp_filter_nohtml_kses filter
            $valid_input = wp_filter_nohtml_kses( $newinput );
            if( NULL !== $optiondetails['min'] && $valid_input<$optiondetails['min']){
              $valid_input = $optiondetails['min'];
            }
            if( NULL !== $optiondetails['max'] && $valid_input>$optiondetails['max']){
              $valid_input = $optiondetails['max'];
            }
          }
          if ( 'int' == $optiondetails['sanitize'] && is_numeric( wp_filter_nohtml_kses( $newinput ) ) ) {
            // Pass input data through the wp_filter_nohtml_kses filter
            $valid_input = round( wp_filter_nohtml_kses( $newinput ) );
            if( NULL !== $optiondetails['min'] && $valid_input<$optiondetails['min']){
              $valid_input = $optiondetails['min'];
            }
            if( NULL !== $optiondetails['max'] && $valid_input>$optiondetails['max']){
              $valid_input = $optiondetails['max'];
            }
          }      
          // Validate no-HTML content
          if ( 'nospaces' == $optiondetails['sanitize'] ) {
            // Pass input data through the wp_filter_nohtml_kses filter
            $valid_input = wp_filter_nohtml_kses( $newinput );
            $valid_input = str_replace(' ','',$valid_input);
          }      
          if ( 'tag' == $optiondetails['sanitize'] ) {
            // Pass input data through the wp_filter_nohtml_kses filter
            $valid_input = wp_filter_nohtml_kses( $newinput );
            $valid_input = str_replace(' ','-',$valid_input);
          }            
          // Validate no-HTML content
          if ( 'nohtml' == $optiondetails['sanitize'] ) {
            // Pass input data through the wp_filter_nohtml_kses filter
            $valid_input = wp_filter_nohtml_kses( $newinput );
            $valid_input = wp_filter_nohtml_kses( $newinput );
          }
          // Validate HTML content
          if ( 'html' == $optiondetails['sanitize'] ) {
            // Pass input data through the wp_filter_kses filter using allowed post tags
            $valid_input = wp_kses_post($newinput );
          }
          // Validate URL address
          if( 'url' == $optiondetails['sanitize'] ){
            $valid_input = esc_url( $newinput );
          }
          // Validate URL address
          if( 'css' == $optiondetails['sanitize'] ){
            $valid_input = wp_htmledit_pre( stripslashes( $newinput ) );
          }      
        }else if( 'wp-textarea' == $optiondetails['type'] ){
            // Text area filter
            $valid_input = wp_kses_post( force_balance_tags($newinput) );
        }
        elseif( 'color' == $optiondetails['type'] ){
          $value =  wp_filter_nohtml_kses( $newinput );
          if( '#' == $value ){
            $valid_input = '';
          }else{
            $valid_input = $value;
          }
        }
        
        return $valid_input;
    }
}

  ?>
