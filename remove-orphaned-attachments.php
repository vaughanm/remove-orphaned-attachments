<?php
add_action('admin_init', 'roa_settings_api_init');
function roa_settings_api_init() {
    global $log_file, $log_file_url, $log_gilename;
    $log_filename = 'ROA-log.log';

    $log_filepath = get_home_path() . 'wp-content/';
    $log_file = $log_filepath . $log_filename;
    $log_file_url = get_home_url() . '/wp-content/' . $log_filename;

 	add_settings_section(
		'roa_setting_section',
		'Orphaned Attachments',
		'roa_setting_section_callback_function',
		'media'
	);

 	add_settings_field(
		'roa_setting_enable',
		'Enable',
		'roa_setting_enable_callback_function',
		'media',
		'roa_setting_section'
	);

 	add_settings_field(
		'roa_setting_limit',
		'Chunk Size Limit',
		'roa_setting_limit_callback_function',
		'media',
		'roa_setting_section'
	);

 	add_settings_field(
		'roa_setting_testmode',
		'Disable Test Mode',
		'roa_setting_testmode_callback_function',
		'media',
		'roa_setting_section'
	);

 	register_setting( 'media', 'roa_setting_enable' );
        register_setting( 'media', 'roa_setting_limit');
        register_setting( 'media', 'roa_setting_testmode');
}

function roa_setting_section_callback_function() {
    global $log_file_url, $log_file;
    echo '<p>Remove attachments where the file no longer exists on the server.</p>';

    if(file_exists($log_file)) {
        echo '<p>Click <a id="roa_download_log" href="' . $log_file_url . '" download>here</a> to download Log of removed attachments.</p>';
    }
}

function roa_setting_enable_callback_function() {
    echo '<input name="roa_setting_enable" id="roa_setting_enable" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'roa_setting_enable' ), false ) . ' />';
}

function roa_setting_testmode_callback_function() {
    echo '<input name="roa_setting_testmode" id="roa_setting_testmode" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'roa_setting_testmode' ), false ) . ' />';
}

function roa_setting_limit_callback_function() {
    $limit = (get_option( 'roa_setting_limit' )) ? (int) get_option( 'roa_setting_limit' ) : 250;
    echo '<input name="roa_setting_limit" id="roa_setting_limit" type="text" value="' . (int) $limit . '" />';
}

if(get_option( 'roa_setting_enable' )) {
    add_action('admin_init', 'roa_process_attachments');
}

function roa_process_attachments() {
    global $log_file;
    $test_mode = get_option( 'roa_setting_testmode' );
    $limit = (get_option( 'roa_setting_limit' )) ? (int) get_option( 'roa_setting_limit' ) : 250;

    $total = count(get_posts("post_type=attachment&numberposts=-1"));
    $pages = $total / $limit;

    $contents = "Remove Orphaned Attachments\r\n";
    $contents .= "---------------------------\r\n\r\n";

    if(!file_exists($log_file)) {
        $output = file_put_contents($log_file, $contents);
    }

    for($x = 0; $x <= $pages; $x++) {
        $attachments = get_posts("post_type=attachment&numberposts={$limit}");

        if ( empty( $attachments ) || is_wp_error( $attachments ) ) {
            $attachments = array();
        }

        foreach($attachments as $attachment) {
            $file = get_attached_file($attachment->ID);
            if(!file_exists($file)) {
                $testmode = (!$test_mode) ? 'Test Mode (Nothing removed)' : 'Attachment Removed';

                $content = "Time: " . current_time( 'd-m-Y - H:i:s' ) . "\r\n";
                $content .= "Attachment ID: " . $attachment->ID . "\r\n";
                $content .= "Attachment Filename: " . basename($file) . "\r\n";
                $content .= "Attachment Path: " . $file . "\r\n";
                $content .= "\r\n" . $testmode . "\r\n\r\n";

                if($test_mode) {
                    $deleted = wp_delete_post( $attachment->ID, false );

                    if(is_wp_error($deleted)) {
                        $content .= "ERROR: Could not delete attachment from DB\r\n";
                    }
                }
                
                $output = file_put_contents($log_file, $content, FILE_APPEND);
            }
        }
    }

    if(isset($content)) {
        $contente = "\r\nEnd of List\r\n\r\n";

        $output = file_put_contents($log_file, $contente, FILE_APPEND);
    }
}