<?php

namespace MPHB;

class Downloader
{
    public function __construct()
    {
        add_action('init', array($this, 'maybeDownload'), 5);
    }

    public function maybeDownload()
    {
        if (!isset($_GET['mphb_action']) || $_GET['mphb_action'] != 'download') {
            return;
        }

        $filename = isset($_GET['filename']) ? sanitize_text_field($_GET['filename']) : '';

        if(!wp_verify_nonce($_GET['nonce'], 'mphb_download-' . $filename)) {
            $this->fireError(__('Nonce verification failed.', 'motopress-hotel-booking'));
        }

        $file = mphb_uploads_dir() . $filename;

        if (!file_exists($file)) {
            $this->fireError(__('The file does not exist.', 'motopress-hotel-booking'));
        }

        $removeAfterDownload = !isset($_GET['remove']) || $_GET['remove'] != 'no';

        $this->download($filename, $file, $removeAfterDownload);
    }

    /**
     * @param string $filename
     * @param string $file Absolute path to the file.
     * @param string $removeAfter
     */
    protected function download($filename, $file, $removeAfter = true)
    {
        ignore_user_abort(true);
        nocache_headers();

        $disabledFunction = explode(',', ini_get('disable_functions'));

        if (!in_array('set_time_limit', $disabledFunction)) {
            set_time_limit(0);
        }

        $mime = wp_check_filetype($file);
        $content = @file_get_contents($file);

        if ($removeAfter) {
            @unlink($file);
        }

        header('Content-Type: ' . $mime['type'] . '; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');

        echo $content;

        exit();
    }

    protected function fireError($errorMessage)
    {
        wp_die($errorMessage, __('Error', 'motopress-hotel-booking'), array('response' => 403));
    }
}
