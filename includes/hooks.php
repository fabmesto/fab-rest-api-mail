<?php

namespace restapimail;

if (!class_exists('restapimail\hooks')) {

    class hooks
    {
        public function __construct()
        {
            add_filter('wp_new_user_notification_email', array(&$this, 'wp_new_user_notification_email'), 10, 3);
            add_filter('wp_mail_from', array(&$this, 'wp_mail_from'), 10, 3);
            add_filter('wp_mail_from_name', array(&$this, 'wp_mail_from_name'), 10, 3);
            add_filter('login_head', array(&$this, 'login_head'));
        }

        public function wp_mail_from($old)
        {
            return get_option('mailrestapi_mittente_email', $old);
        }

        public function wp_mail_from_name($old)
        {
            return get_option('mailrestapi_mittente_nome', $old);
        }

        public function wp_new_user_notification_email($wp_new_user_notification_email, $user, $blogname)
        {
            $message = wpautop(get_option('mailrestapi_new_user_notification_email_message', ''));
            $message = str_replace("{blogname}", $blogname, $message);
            $message = str_replace("{user_login}", $user->user_login, $message);
            $message = str_replace("{user_email}", $user->user_email, $message);
            $message = str_replace("{user_first_name}", $user->first_name, $message);
            $message = str_replace("{user_last_name}", $user->last_name, $message);
            $message .= nl2br($wp_new_user_notification_email['message']);

            $subject = get_option('mailrestapi_new_user_notification_email_subject', $wp_new_user_notification_email['subject']);
            $subject = str_replace("{blogname}", $blogname, $subject);
            $wp_new_user_notification_email['subject'] = $subject;
            $wp_new_user_notification_email['message'] = $message;
            $wp_new_user_notification_email['headers'] = 'Content-Type: text/html; charset=UTF-8';
            return $wp_new_user_notification_email;
        }

        public function login_head()
        {
            $login_head = get_option('mailrestapi_login_head', '');
            if ($login_head != '') {
                echo '<style type="text/css">
                h1 a {background-image:url(' . $login_head . ') !important; background-size:contain !important; margin:0 auto;}
                </style>';
            }
        }
    }
}
