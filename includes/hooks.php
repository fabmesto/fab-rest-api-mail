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
            $message = get_option('mailrestapi_new_user_notification_email_message', '');
            $message = str_replace("{blogname}", $blogname, $message);
            $message .= $wp_new_user_notification_email['message'];

            $subject = get_option('mailrestapi_new_user_notification_email_subject', $wp_new_user_notification_email['subject']);
            $subject = str_replace("{blogname}", $blogname, $subject);
            $wp_new_user_notification_email['subject'] = $subject;
            $wp_new_user_notification_email['message'] = $message;
            return $wp_new_user_notification_email;
        }
    }
}