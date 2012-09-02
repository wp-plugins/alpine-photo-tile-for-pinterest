<?php
/**
 * Alpine PhotoTile for Pinterest: Options Display
 *
 * @since 1.0.0
 *
 * Display Callback Version 1
 */

if ( !function_exists( 'theAlpinePressMenuDisplayCallbackV1' ) ) {
    function theAlpinePressMenuDisplayCallbackV1($options,$option,$fieldname,$fieldid){
        $default = $option['default'];
        $optionname = $option['name'];
        $optiontitle = $option['title'];
        $optiondescription = $option['description'];
        $fieldtype = $option['type'];
        $value = ( Null != $options[$optionname] ? $options[$optionname] : $default );
        
         // Output checkbox form field markup
        if ( 'checkbox' == $fieldtype ) {
          ?>
          <input type="checkbox" id="<?php echo $fieldid; ?>" name="<?php echo $fieldname; ?>" <?php checked( $value ); ?> />
          <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <span class="description"><?php echo $optiondescription; ?></span>
          <?php
        }
        // Output radio button form field markup
        else if ( 'radio' == $fieldtype ) {
          $valid_options = array();
          $valid_options = $option['valid_options'];
          ?><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label><?php
          foreach ( $valid_options as $valid_option ) {
            ?>
            <input type="radio" name="<?php echo $fieldname; ?>" <?php checked( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" />
            <span class="description"><?php echo $optiondescription; ?></span>
            <?php
          }
        }
        // Output select form field markup
        else if ( 'select' == $fieldtype ) {
          $valid_options = array();
          $valid_options = $option['valid_options']; 
          ?>
          <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
            <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
            <?php 
            foreach ( $valid_options as $valid_option ) {
              ?>
              <option <?php selected( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" ><?php echo $valid_option['title']; ?></option>
              <?php
            }
            ?>
            </select>
            <span class="description"><?php echo $optiondescription; ?></span>
          
          <?php
        } // Output select form field markup
        else if ( 'range' == $fieldtype ) {     
          ?>
          <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
            <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
            <?php 
            for($i = $option['min'];$i <= $option['max']; $i++){
              ?>
              <option <?php selected( $i == $value ); ?> value="<?php echo $i; ?>" ><?php echo $i ?></option>
              <?php
            }
            ?>
            </select>
            <span class="description"><?php echo $optiondescription; ?></span>
          
          <?php
        } 
        // Output text input form field markup
        else if ( 'text' == $fieldtype ) {
          ?>
          <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" value="<?php echo wp_filter_nohtml_kses( $value ); ?>" />
          <div class="description"><span class="description"><?php echo $optiondescription; ?></span></div>
          <?php
        } 
        else if ( 'textarea' == $fieldtype ) {
          ?>
          <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <textarea id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="sassafras_textarea" ><?php echo $value; ?></textarea><br>
          <span class="description"><?php echo esc_textarea( $optiondescription ); ?></span>
          <?php
        }   
        else if ( 'color' == $fieldtype ) {
          ?>
          
          <label for="<?php echo $fieldid ?>">
          <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="sassafras_color"  value="<?php echo wp_filter_nohtml_kses( $value ); ?>" /><span class="description"><?php echo $optiondescription; ?></span></label>
         
          <div id="<?php echo $fieldid; ?>_picker" class="sassafras_color_picker" ></div>

          <?php
        }
    }
}
  
  ?>
