<?php
/*
    * Plugin Name: SoftGroup - Math Captcha
    * Plugin URI: https://github.com/Shooter75/tree/master/sg-math-captcha
    * Description: This captcha will be protect your site from spammers
    * Version: 2.0
    * Author: Yaroslav Kostecki
    * Author URI: https://github.com/Shooter75/
*/

class MathCaptcha {

    public function __construct(){

        if(session_id() == ''){
            session_start();
        }

        $actionsClass = new Actions();

        add_action('admin_menu', [$actionsClass, 'AdminMenu']);

        add_action('comment_form_logged_in_after', [$actionsClass, 'CaptchaField']);
        add_action('comment_form_after_fields', [$actionsClass, 'CaptchaField']);
        add_action('preprocess_comment', [$actionsClass, 'CaptchaCheck']);

        add_action('register_form', [$actionsClass, 'CaptchaField']);

        add_action('login_form', [$actionsClass, 'CaptchaField']);
        add_action('login_init', [$actionsClass, 'CaptchaCheck']);

    }

    public static function GenerateCaptcha() {

        $arrayOperations = [
            1 => '-',
            2 => '+',
            3 => '/',
            4 => '*'
        ];

        $A = rand(get_option('math_captcha_min_A_number'), get_option('math_captcha_max_A_number'));
        $B = rand(get_option('math_captcha_min_B_number'), get_option('math_captcha_max_B_number'));

        $mathOperation = $arrayOperations[rand(1, 4)];

        switch($mathOperation) {

            case '-':
                $_SESSION['captcha'] = $A - $B;
                break;

            case '+':
                $_SESSION['captcha'] = $A + $B;
                break;

            case '*':
                $_SESSION['captcha'] = $A * $B;
                break;

            case '/':
                if($B != 0)
                    $_SESSION['captcha'] = $A / $B;
                else
                    $_SESSION['captcha'] = $A / ( $B + 1 ) ;
                break;

        }

        return
            [
                'A' => $A,
                'B' => $B,
                'action' => $mathOperation
            ];

    }

    public static function CaptchaCheck($result) {
        return $_SESSION['captcha'] === $result;
    }

};

class Actions {

    function CaptchaField()
    {
        $captcha = MathCaptcha::GenerateCaptcha();
        ?>
        <p class="comment-form-title">
            <label for="math_captcha">
                Здійсніть обрахунки —
                <?=$captcha['A']     ?>
                <?=$captcha['action']?>
                <?=$captcha['B']     ?>
                ?
            </label>
            <input type="text" name="math_captcha" id="math_captcha" />
        </p>
        <?php
    }

    function CaptchaOptions() {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        echo '<div class="wrap">';
        echo '<form method="post" action="options.php">';

        wp_nonce_field('update-options');

        echo '<table class="form-table"><tr valign="top">'
            . '<th scope="row">Максимальне значення числа А:</th>'
            . '<td><input type="number" name="math_captcha_max_A_number" value="' . get_option('math_captcha_max_A_number') . '" /></td>'
            . '</tr>'
            . '<th scope="row">Мінімальне значення числа А:</th>'
            . '<td><input type="number" name="math_captcha_min_A_number" value="' . get_option('math_captcha_min_A_number') . '" /></td>'
            . '</tr>'
            . '<th scope="row">Максимальне значення числа Б:</th>'
            . '<td><input type="number" name="math_captcha_max_B_number" value="' . get_option('math_captcha_max_B_number') . '" /></td>'
            . '</tr>'
            . '<th scope="row">Мінімальне значення числа Б:</th>'
            . '<td><input type="number" name="math_captcha_min_B_number" value="' . get_option('math_captcha_min_B_number') . '" /></td>'
            . '</tr>'
            . '</table>'
            . '<input type="hidden" name="action" value="update" />'
            . '<input type="hidden" name="page_options" value="math_captcha_max_A_number, math_captcha_min_A_number, math_captcha_min_B_number, math_captcha_max_B_number" />'
            . '<p class="submit">'
            . '<input type="submit" class="button-primary" value="Зберегти зміни" />'
            . '</p>';
        echo '</form></div>';
    }

    function AdminMenu() {
        add_options_page('Math Captcha', 'Math Captcha', 'manage_options', 'math-captcha', [$this, 'CaptchaOptions']);
    }

    function CaptchaCheck($commentData) {

        if(isset($_POST['math_captcha']))
        {
            $captchaValue = (int)($_POST['math_captcha']);

            if(!MathCaptcha::CaptchaCheck($captchaValue)) {
                wp_die('Помилка! Математична операція обрахована невірно!');
            }

        }

        return $commentData;
    }

};

new MathCaptcha();

