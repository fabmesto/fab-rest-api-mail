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
            add_filter('login_head', array(&$this, 'login_head'), 10, 3);

            add_action('wp_head', array(&$this, 'wp_head'));
            // user last login
            if (get_option('mailrestapi_last_login', '0') == '1') {
                add_action('wp_login', array(&$this, 'user_last_login'), 10, 2);
                add_filter('manage_users_columns', array(&$this, 'manage_users_columns'));
                add_filter('manage_users_custom_column', array(&$this, 'manage_users_custom_column'), 10, 3);
                add_filter('manage_users_sortable_columns', array(&$this, 'manage_users_sortable_columns'));
                add_action('pre_get_users', array(&$this, 'pre_get_users'));
            }
        }


        function wp_head()
        {
            if (is_page() || is_single()) {
                $post_id = get_queried_object_id();
                $disable_adsense = get_post_meta($post_id, 'disable_adsense', true);

                if ($disable_adsense != '1') {
                    // solo se non vuoi disabilitare adsense in questa pagina
                    $adsense = get_option('mailrestapi_adsense', '');
                    if ($adsense != '') {
                        echo $adsense;
                    }
                }
            }
        }

        public function pre_get_users($query)
        {
            if (get_option('mailrestapi_last_login', '0') == '1') {
                if ('last_login' == $query->get('orderby')) {
                    $query->set('meta_query', array(
                        'relation' => 'OR',
                        array(
                            'key' => 'last_login',
                            'compare' => 'EXISTS'
                        ),
                        array(
                            'key' => 'last_login',
                            'compare' => 'NOT EXISTS'
                        )
                    ));
                    $query->set('orderby', 'meta_value_num');
                }
            }
        }

        public function manage_users_sortable_columns($columns)
        {
            if (get_option('mailrestapi_last_login', '0') == '1') {
                $columns['last_login'] = 'last_login';
            }
            return $columns;
        }

        public function manage_users_columns($columns)
        {
            if (get_option('mailrestapi_last_login', '0') == '1') {
                $columns['last_login'] = __('Ultima login');
            }
            return $columns;
        }

        public function manage_users_custom_column($value, $column_name, $user_id)
        {
            if (get_option('mailrestapi_last_login', '0') == '1') {
                if ('last_login' == $column_name) {
                    $last_login = get_the_author_meta('last_login', $user_id);
                    if ($last_login != '') {
                        $the_login_date = human_time_diff($last_login);
                        $value = $the_login_date;
                    } else {
                        $value = 'Mai';
                    }
                }
            }
            return $value;
        }
        /**
         * Cattura il login salvando il time
         *
         */
        public function user_last_login($user_login, $user)
        {
            if (get_option('mailrestapi_last_login', '0') == '1') {
                update_user_meta($user->ID, 'last_login', time());
                /*
                $last_login = get_the_author_meta('last_login');
                $the_login_date = human_time_diff($last_login);
                return $the_login_date; 
                */
            }
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
