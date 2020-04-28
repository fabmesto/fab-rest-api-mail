<?php

namespace restapimail;

if (!class_exists('restapimail\metabox')) {
    class metabox
    {
        public function __construct()
        {
            add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
            add_action('save_post', array(&$this, 'save_post'));
        }

        public function add_meta_boxes()
        {
            $screens = ['post', 'page'];
            foreach ($screens as $screen) {
                add_meta_box(
                    'fab_adsense_box_id',           // Unique ID
                    'Adsense BOX',                  // Box title
                    [&$this, 'custom_box_html'],    // Content callback, must be of type callable
                    $screen,                        // Post type
                    'side'
                );
            }
        }

        public function custom_box_html($post)
        {

            $value = get_post_meta($post->ID, 'disable_adsense', true);
            $text = 'Disabilita Adsense in questo post';
            echo '
            <input type="hidden" name="disable_adsense" value="0" />
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="disable_adsense" value="1" ' . ($value == 1 ? 'checked' : '') . '> ' . $text . '
                </label>
            </div>
          ';
        }

        public function save_post($post_id)
        {
            if (array_key_exists('disable_adsense', $_POST)) {
                update_post_meta(
                    $post_id,
                    'disable_adsense',
                    $_POST['disable_adsense']
                );
            }
        }
    }
}
