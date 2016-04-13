<?php
/*
 * Plugin Name: Soft Group - `Authorized Only!`
 * Plugin URI: https://github.com/Shooter75/WP-plugins/tree/master/sg-authorized-only
 * Description: This plugin protect your site from not authorized users and add new feature on your site - invites!
 * Version: 2.0
 * Author: Yaroslav Kostecki
 * Author URI: https://github.com/Shooter75
 * License: Pirat
 * */

class Authorized_Only
{

    public function __construct()
    {
        $actions = new Authorized_Only_Actions();

        if( $this->get_current_setting() == 'Yes')
        {
            add_action('protection_from_no_authorized', [$actions, 'show_notification']);
        }
        else
        {
            add_action('wp_head', [$actions, 'do_redirect']);
        }

        add_action('admin_menu', [$actions, 'auth_AdminMenu']);
    }

    private function get_current_setting()
    {
        $result = get_option('auth_protection_option');

        return $result ;
    }

}

class Authorized_Only_Actions
{

    public static function do_redirect()
    {
        if(!self::Check_Authorization())
        {
            wp_redirect('wp-login.php');
        }
    }

    public static function show_notification()
    {
        if(!self::Check_Authorization())
        { ?>
            <div class="center">
                <h5>You are not authorized user!</h5>
                <h6>
                    Please!
                    <a href="<?php echo home_url('wp-login.php');?>">Log In</a> or
                    <a href="<?php echo home_url('wp-login.php?action=register');?>">Sing Up</a>
                    to get access on this page!
                </h6>
            </div>
        <?php }
    }

    function auth_options_form()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                wp_nonce_field('update-options');
                ?>
                <table class="form-table"><tr valign="top">
                        <th scope="row">Show form(`Yes`,`No`): </th>
                        <td>
                            <input type="text" name="auth_protection_option" value="<?php echo get_option('auth_protection_option'); ?>" />
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="auth_protection_option" />
                <p class="submit">
                    <input type="submit" class="button-primary" value="Зберегти зміни" />
                </p>
            </form>
        </div>
        <?php
    }

    function auth_AdminMenu()
    {
        add_options_page('Authorized Only', 'Authorized Only', 'manage_options', 'authorized_only', [$this, 'auth_options_form']);
    }

    public function registrationForm()
    {
        ?>
        <form class="col s12">
            <div class="row">
                <div class="input-field col s6">
                    <i class="material-icons prefix">account_circle</i>
                    <input id="first-name" name="first-name" type="text" class="valid" placeholder="First Name" />
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">account_circle</i>
                    <input id="last-name" name="last-name" type="text" class="valid" placeholder="Last Name" />
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">contacts</i>
                    <input id="user-login" name="user-login" type="text" class="valid" placeholder="Login" />
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">vpn_key</i>
                    <input id="user-password" name="user-password" type="password" class="valid" placeholder="Password" />
                </div>
            </div>
            <button type="button" class="btn waves-effect waves-light" >Join
                <i class="material-icons right">send</i>
            </button>
        </form>
        <?php
    }

    public static function Check_Authorization()
    {
        return is_user_logged_in();
    }

};

class Custom_Registration
{

    private $username;
    private $email;
    private $password;
    private $website;
    private $nickname;
    private $invitor;

    function __construct()
    {
        add_shortcode('custom_registration_form', array($this, 'shortcode'));
    }

    function shortcode()
    {
        ob_start();

        if ($_POST) {
            $this->username = $_POST['reg_name'];
            $this->email    = $_POST['reg_email'];
            $this->password = $_POST['reg_password'];
            $this->website  = $_POST['reg_website'];
            $this->nickname = $_POST['reg_nickname'];
            $this->invitor  = $_POST['reg_invitor'];

            $this->validation();
            $this->registration();
        }

        $this->registration_form();
        return ob_get_clean();
    }

    function registration_form()
    {
        ?>
        <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
            <div class="col s12">
                <div>
                    <input name="reg_name" type="text" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_name']) ? $_POST['reg_name'] : null); ?>"
                           placeholder="Username" id="reg-name" required/>
                </div>

                <div>
                    <input name="reg_email" type="email" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_email']) ? $_POST['reg_email'] : null); ?>"
                           placeholder="Email" id="reg-email" required/>
                </div>

                <div>
                    <input name="reg_password" type="password" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_password']) ? $_POST['reg_password'] : null); ?>"
                           placeholder="Password" id="reg-pass" required/>
                </div>

                <div>
                    <input name="reg_website" type="text" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_website']) ? $_POST['reg_website'] : null); ?>"
                           placeholder="Website" id="reg-website"/>
                </div>

                <div>
                    <input name="reg_nickname" type="text" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_nickname']) ? $_POST['reg_nickname'] : null); ?>"
                           placeholder="Nickname" id="reg-nickname"/>
                </div>

                <div>
                    <input name="reg_invitor" type="text" class="input-field col s6"
                           value="<?php echo(isset($_POST['reg_invitor']) ? $_POST['reg_invitor'] : null); ?>"
                           placeholder="Invitor" id="reg-invitor"/>
                </div>

                <input class="btn waves-effect waves-light" type="submit" name="reg_submit" value="Register"/>
        </form>
        <?php
    }

    function validation()
    {

        if (empty($this->username) || empty($this->password) || empty($this->email)) {
            return new WP_Error('field', 'Required form field is missing');
        }

        if (strlen($this->username) < 4) {
            return new WP_Error('username_length', 'Username too short. At least 4 characters is required');
        }

        if (!username_exists($this->invitor)) {
            return new WP_Error('user_invitor', 'User dose not exist!');
        }

        if (strlen($this->password) < 5) {
            return new WP_Error('password', 'Password length must be greater than 5');
        }

        if (!is_email($this->email)) {
            return new WP_Error('email_invalid', 'Email is not valid');
        }

        if (email_exists($this->email)) {
            return new WP_Error('email', 'Email Already in use');
        }

        if (!empty($website)) {
            if (!filter_var($this->website, FILTER_VALIDATE_URL)) {
                return new WP_Error('website', 'Website is not a valid URL');
            }
        }

        $details = array(
            'Username' => $this->username,
            'Nickname' => $this->nickname,
            'Invitor' => $this->invitor
        );

        foreach ($details as $field => $detail) {
            if (!validate_username($detail)) {
                return new WP_Error('name_invalid', 'Sorry, the "' . $field . '" you entered is not valid');
            }
        }

        return true;
    }

    function registration()
    {
        if (is_wp_error($this->validation())) {
            ?>
            <div>
                <strong>
                    <?php $this->validation()->get_error_message() ?>
                </strong>
            </div>';
            <?php

        } else {

            //Database need this format
            $date = date('Y-m-d G:i:s');

            global $wpdb;

            $wpdb->insert('wp_users', array(
                'ID'                    => NULL,
                'user_login'            => $this->username,
                'user_pass'             => wp_hash_password($this->password),
                'user_nicename'         => $this->nickname,
                'user_email'            => $this->email,
                'user_invator'          => $this->invitor,
                'user_url'              => $this->website,
                'user_registered'       => $date,
                'user_activation_key'   => '',
                'user_status'           => 0,
                'display_name'          => $this->nickname
            ));
        }

    }

};

new Custom_Registration;

new Authorized_Only();

