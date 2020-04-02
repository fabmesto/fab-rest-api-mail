<?php

namespace restapimail;

if (!class_exists('restapimail\settings')) {
    class settings
    {
        var $parent = false;

        public function __construct($parent)
        {
            $this->parent = $parent;
            if (is_admin()) {
                // wp-admin actions
                add_action('admin_menu', array(&$this, 'add_admin_menu'));
                add_action('admin_init', array(&$this, 'register_settings'));
            }
        }

        public function add_admin_menu()
        {
            // add_management_page -> Strumenti
            // add_options_page -> Impostazioni
            // add_menu_page -> in ROOT
            add_options_page(
                'Settings Admin',
                'Email settings (FAB)',
                'manage_options',
                'fabrestapimail_settings',
                array(&$this, 'settings')
            );
        }

        public function settings()
        {
            ob_start();
            $action_file = FAB_PLUGIN_DIR_PATH . 'includes/v/settings.php';
            if (file_exists($action_file)) {
                require_once($action_file);
            } else {
                echo "settings: Nessuna azione trovata: " . $action_file;
            }
            echo ob_get_clean();
        }

        public function register_settings()
        {
            register_setting('fabrestapimail-options', 'mailrestapi_email');
            register_setting('fabrestapimail-options', 'mailrestapi_subject');
            register_setting('fabrestapimail-options', 'mailrestapi_message');
            register_setting('fabrestapimail-options', 'mailrestapi_message_from');
            // mittente
            register_setting('fabrestapimail-options', 'mailrestapi_mittente_email');
            register_setting('fabrestapimail-options', 'mailrestapi_mittente_nome');
            // nuovo utente email
            register_setting('fabrestapimail-options', 'mailrestapi_new_user_notification_email_subject');
            register_setting('fabrestapimail-options', 'mailrestapi_new_user_notification_email_message');
            // login head
            register_setting('fabrestapimail-options', 'mailrestapi_login_head');
        }
    }
}
