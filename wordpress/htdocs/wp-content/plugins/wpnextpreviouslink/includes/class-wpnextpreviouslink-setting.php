<?php
/**
 * weDevs Settings API wrapper class
 *
 * @version 1.1
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @link http://tareq.weDevs.com Tareq's Planet
 * @example src/settings-api.php How to use the class
 * Further modified by codeboxr.com team
 */
if (!class_exists('WPNextPreviousLink_Settings_API')):

    class WPNextPreviousLink_Settings_API {

        /**
         * settings sections array
         *
         * @var array
         */
        private $settings_sections = array();

        /**
         * Settings fields array
         *
         * @var array
         */
        private $settings_fields = array();

        /**
         * Singleton instance
         *
         * @var object
         */
        private static $_instance;

        public function __construct($wpnextpreviouslink, $version) {

        }

        /**
         * Set settings sections
         *
         * @param array   $sections setting sections array
         */
        function set_sections($sections) {
            $this->settings_sections = $sections;

            return $this;
        }

        /**
         * Add a single section
         *
         * @param array   $section
         */
        function add_section($section) {
            $this->settings_sections[] = $section;

            return $this;
        }

        /**
         * Set settings fields
         *
         * @param array   $fields settings fields array
         */
        function set_fields($fields) {
            $this->settings_fields = $fields;

            return $this;
        }

        function add_field($section, $field) {
            $defaults = array(
                'name'          => '',
                'label'         => '',
                'desc'          => '',
                'type'          => 'text',
                'placeholder'   => ''
            );

            $arg                               = wp_parse_args($field, $defaults);
            $this->settings_fields[$section][] = $arg;

            return $this;
        }

        /**
         * Initialize and registers the settings sections and fileds to WordPress
         *
         * Usually this should be called at `admin_init` hook.
         *
         * This function gets the initiated settings sections and fields. Then
         * registers them to WordPress and ready for use.
         */
        function admin_init() {
            //register settings sections
            foreach ($this->settings_sections as $section) {
                if (false == get_option($section['id'])) {
                    add_option($section['id']);
                }

                if (isset($section['desc']) && !empty($section['desc'])) {
                    $section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
                    $callback        = create_function('', 'echo "' . str_replace('"', '\"', $section['desc']) . '";');
                } else if (isset($section['callback'])) {
                    $callback = $section['callback'];
                } else {
                    $callback = null;
                }

                add_settings_section($section['id'], $section['title'], $callback, $section['id']);
            }

            //register settings fields
            foreach ($this->settings_fields as $section => $field) {
                foreach ($field as $option) {

                    $type           = isset($option['type']) ? $option['type'] : 'text';
                    $placeholder    = isset($option['placeholder']) ? $option['placeholder']: '';

                    $args = array(
                        'id'                => $option['name'],
                        'label_for'         => $args['label_for']  = "{$section}[{$option['name']}]",
                        'desc'              => isset($option['desc']) ? $option['desc'] : '',
                        'name'              => $option['label'],
                        'section'           => $section,
                        'size'              => isset($option['size']) ? $option['size'] : null,
                        'options'           => isset($option['options']) ? $option['options'] : '',
                        'std'               => isset($option['default']) ? $option['default'] : '',
                        'sanitize_callback' => isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '',
                        'type'              => $type,
                        'placeholder'       => $placeholder
                    );

                    add_settings_field($section . '[' . $option['name'] . ']', $option['label'], array($this, 'callback_' . $type), $section, $section, $args);
                }
            }

            // creates our settings in the options table
            foreach ($this->settings_sections as $section) {
                register_setting($section['id'], $section['id'], array($this, 'sanitize_options'));
            }
        }

        /**
         * Get field description for display
         *
         * @param array   $args settings field args
         */
        public function get_field_description($args) {
            if (!empty($args['desc'])) {
                $desc = sprintf('<p class="description">%s</p>', $args['desc']);
            } else {
                $desc = '';
            }

            return $desc;
        }
        
        /**
         * Displays a text field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_text($args) {

           
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $type  = isset($args['type']) ? $args['type'] : 'text';

            $html  = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>', $type, $size, $args['section'], $args['id'], $value);
            $html .= $this->get_field_description($args);

            echo $html;
        }
        
        
        /**
         * Displays a textimg field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_textimg($args) {

            $wpnp_image_name = $this->get_option('wpnp_image_name', 'wpnextpreviouslink_basics');
           
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $type  = isset($args['type']) ? $args['type'] : 'text';
            $placeholder  = isset($args['placeholder']) ? $args['placeholder'] : '';

            $html  = sprintf('<input type="text" class="wpnpcustomimg %1$s-text '.(($wpnp_image_name != 'custom') ? ' hidden' : '').'" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" placeholder="%5$s" style="float:left; "/>', $size, $args['section'], $args['id'], $value, $placeholder);
            //if($wpnp_image_name == 'custom') {
                $html .= $this->get_field_description($args);
            //}


            echo $html;
        }

        /**
         * Displays a url field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_url($args) {
            $this->callback_text($args);
        }

        /**
         * Displays a number field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_number($args) {
            $this->callback_text($args);
        }

        /**
         * Displays a checkbox for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_checkbox($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));

            $html = '<fieldset>';
            $html .= sprintf('<label for="wpuf-%1$s[%2$s]">', $args['section'], $args['id']);
            $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
            $html .= sprintf('<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked($value, 'on', false));
            //$html .= sprintf('%1$s</label>', $args['desc']);
            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a multicheckbox a settings field
         *
         * @param array   $args settings field args
         */
        function callback_multicheck($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);

            $html = '<fieldset>';
            foreach ($args['options'] as $key => $label) {
                $checked = isset($value[$key]) ? $value[$key] : '0';
                $html .= sprintf('<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
                $html .= sprintf('<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($checked, $key, false));
                $html .= sprintf('%1$s</label><br>', $label);
            }
            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a info field
         *
         * @param array  $args settings field args
         */
        function callback_info($args) {
            $html = $args['desc'];
            echo $html;
        }

        /**
         * Displays a multicheckbox a settings field
         *
         * @param array   $args settings field args
         */
        function callback_radio($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);

            $html = '<fieldset>';
            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
                $html .= sprintf('<input type="radio" class="radio" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($value, $key, false));
                $html .= sprintf('%1$s</label><br>', $label);
            }
            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a selectbox for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_select($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular chosen-select';

            $html = sprintf('<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);
            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<option value="%s"%s>%s</option>', $key, selected($value, $key, false), $label);
            }
            $html .= sprintf('</select>');
            $html .= $this->get_field_description($args);

            echo $html;
        }
        
        /**
         * Displays a multi-selectbox for a settings field
         *
         * @param array  $args settings field args
         */
        function callback_multiselect($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);

            $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular chosen-select';

            $html = sprintf('<select multiple class="%1$s" name="%2$s[%3$s][]" id="%2$s[%3$s]" style="width: 150px;">', $size, $args['section'], $args['id']);

            if ($value) {
                foreach ($args['options'] as $opt_grouplabel => $data) {
                    $html .= '<optgroup label="' . $opt_grouplabel . '">';
                    foreach ($data as $opt_key => $opt_val) {
                        if (in_array($opt_key, $value)) {
                            $html .= sprintf('<option value="%s"%s>%s</option>', $opt_key, selected($opt_key, $opt_key, false), $opt_val);
                        } else {
                            $html .= sprintf('<option value="%s">%s</option>', $opt_key, $opt_val);
                        }
                    }
                    $html .= '<optgroup>';
                }
            } else {
                foreach ($args['options'] as $opt_label => $data) {
                    foreach ($data as $opt_key => $opt_val) {
                        $html .= sprintf('<option value="%s"%s>%s</option>', $opt_key, selected($value, $opt_key, false), $opt_val);
                    }
                }
            }

            $html .= sprintf('</select>');
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a textarea for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_textarea($args) {

            $value = esc_textarea($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

            $html = sprintf('<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value);
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a textarea for a settings field
         *
         * @param array   $args settings field args
         * @return string
         */
        function callback_html($args) {
            echo $this->get_field_description($args);
        }

        /**
         * Displays a rich text textarea for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_wysiwyg($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : '500px';

            echo '<div style="max-width: ' . $size . ';">';

            $editor_settings = array(
                'teeny'         => true,
                'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
                'textarea_rows' => 10
            );
            if (isset($args['options']) && is_array($args['options'])) {
                $editor_settings = array_merge($editor_settings, $args['options']);
            }

            wp_editor($value, $args['section'] . '-' . $args['id'], $editor_settings);

            echo '</div>';

            echo $this->get_field_description($args);
        }

        /**
         * Displays a file upload field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_file($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $id    = $args['section'] . '[' . $args['id'] . ']';
            $label = isset($args['options']['button_label']) ?
                    $args['options']['button_label'] :
                    __('Choose File');

            $html = sprintf('<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
            $html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a password field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_password($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

            $html = sprintf('<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a color picker field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_color($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

            $html = sprintf('<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std']);
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Sanitize callback for Settings API
         */
        function sanitize_options($options) {
            foreach ($options as $option_slug => $option_value) {
                $sanitize_callback = $this->get_sanitize_callback($option_slug);

                // If callback is set, call it
                if ($sanitize_callback) {
                    $options[$option_slug] = call_user_func($sanitize_callback, $option_value);
                    continue;
                }
            }

            return $options;
        }

        /**
         * Get sanitization callback for given option slug
         *
         * @param string $slug option slug
         *
         * @return mixed string or bool false
         */
        function get_sanitize_callback($slug = '') {
            if (empty($slug)) {
                return false;
            }

            // Iterate over registered fields and see if we can find proper callback
            foreach ($this->settings_fields as $section => $options) {
                foreach ($options as $option) {
                    if ($option['name'] != $slug) {
                        continue;
                    }

                    // Return the callback name
                    return isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
                }
            }

            return false;
        }

        /**
         * Get the value of a settings field
         *
         * @param string  $option  settings field name
         * @param string  $section the section name this field belongs to
         * @param string  $default default text if it's not found
         * @return string
         */
        function get_option($option, $section, $default = '') {

            $options = get_option($section);

            if (isset($options[$option])) {
                return $options[$option];
            }

            return $default;
        }

        /**
         * Show navigations as tab
         *
         * Shows all the settings section labels as tab
         */
        function show_navigation() {
            $html = '<h2 class="nav-tab-wrapper">';

            foreach ($this->settings_sections as $tab) {
                $html .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
            }

            $html .= '</h2>';

            echo $html;
        }

        /**
         * Show the section settings forms
         *
         * This function displays every sections in a different form
         */
        function show_forms() {
            ?>
            <div class="metabox-holder">
                <?php foreach ($this->settings_sections as $form) { ?>
                    <div id="<?php echo $form['id']; ?>" class="wpnextpreviouslink_group" style="display: none;">
                        <form method="post" action="options.php">
                            <?php
                            do_action('wpnextpreviouslink_form_top_' . $form['id'], $form);
                            settings_fields($form['id']);
                            do_settings_sections($form['id']);
                            do_action('wpnextpreviouslink_form_bottom_' . $form['id'], $form);
                            ?>
                            <div style="padding-left: 10px">
                                <?php submit_button(); ?>
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
            <?php

        }

    }
endif;
