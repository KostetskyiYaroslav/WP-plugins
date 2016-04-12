<?php
/*
 * Plugin Name: SoftGroup | Clicker
 * Plugin URI: https://github.com/Shooter75/WP-plugins/tree/master/sg-clicker
 * Description: This widget add a Circle Button to your selected widget area and count the click on it. Add into admin menu custom color setting of clicker <strong>The clicker name must be different and unique</strong>
 * Version: 2.0
 * Author: Yaroslav Kostecki
 * Author URI: https://github.com/Shooter75/
 * */

class Clicker extends WP_Widget
{
    function __construct()
    {
        parent::__construct('clicker', 'Clicker',
            [
                'description' => 'This widget add a Circle Button to your selected widget area and count the click on it. Add into admin menu custom color setting of clicker' . '<strong>The clicker name must be different and unique</strong>'
            ]
        );
    }

    function form($instance)
    {
        $clickerName     = "";
        $textColor       = "";
        $backgroundColor = "";

        if (!empty($instance)) {
            $textColor  = $instance["textColor"];
            $backgroundColor = $instance["backgroundColor"];
            $clickerName = $instance["clickerName"];
        }

        $textColorId   = $this->get_field_id('textColor');
        $textColorName = $this->get_field_name("textColor");

        $backgroundColorId   = $this->get_field_id('backgroundColor');
        $backgroundColorName = $this->get_field_name("backgroundColor");

        $clickerNameId   = $this->get_field_id('clickerName');
        $clickerNameName = $this->get_field_name("clickerName");

        echo <<<FIELD
        
        <input id="$clickerNameId" name="$clickerNameName" value="$clickerName"/>
        <label for="$clickerNameId">Clicker Name(Unique)*</label>
        <input id="$textColorId" name="$textColorName" value="$textColor"/>
        <label for="$textColorId">Body Color</label>
        <input id="$backgroundColorId" name="$backgroundColorName" value="$backgroundColor"/>
        <label for="$backgroundColorId">Background Color</label>
FIELD;
    }
    
    function update($new_instance, $old_instance)
    {
        $values = array();

        $values['clickerName']     = htmlentities( $new_instance['clickerName']     );
        $values['textColor']       = htmlentities( $new_instance['textColor']       );
        $values['backgroundColor'] = htmlentities( $new_instance['backgroundColor'] );

        return $values;
    }

    function widget($args, $instance)
    {
        $clickerName     = $instance['clickerName'];
        $textColor       = $instance['textColor'];
        $backgroundColor = $instance['backgroundColor'];

        echo <<<VISIBLE
        <script>
            $(function (){
            
                var clicker = $('#$clickerName');
                
                clicker.click(function () {
                  if(clicker.width() < parseInt(clicker.parent().parent().css('maxWidth')) - 5)
                  {
                     $(this).css({
                         width : clicker.width()  + 1 ,
                         height: clicker.height() + 1 
                     });
                     
                     var fontSize = parseInt( clicker.css('font-size') ) + 1;
                  
                     clicker.css( 'font-size', fontSize );
                  }
                     clicker.text( Number( clicker.text() ) + 1 );
                });
            });
        </script>
        <div class="funny-clicker-area col center">
            <button style="background: $backgroundColor; color: $textColor; font-weight: bold;" class="btn-floating denied-select" id="$clickerName" ></button>
        </div>
VISIBLE;
    }

};

add_action('widgets_init', function(){
    register_widget('Clicker');
});