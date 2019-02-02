<?php
if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'PLURAL_RULE' => 1,
    'CAPSULE_TITLE' => 'Image hosting',
    'CAPSULE_MAXIMUM_SIZE' => 'Maximum size:',
    'CAPSULE_ALLOWED_EXTENSIONS' => 'Allowed extensions:',
    'CAPSULE_BUTTON_SEND' => 'Upload image',
    'CAPSULE_EMPTY_DB' => 'You do not have any images yet.',
    'CAPSULE_ERROR_MIME' => 'Upload failed. Not allowed file extension',
    'CAPSULE_ERROR_SIZE' => 'Upload failed. Size too large',
    'CAPSULE_ERROR_GENERAL' => 'Upload failed. Try again',
    'CAPSULE_ERROR_FILE_TOO_LARGE' => 'File too large',
    'CAPSULE_ERROR_PARTIAL' => 'File upload was not completed',
    'CAPSULE_ERROR_NO_FILE' => 'Zero-length file uploaded',
    'CAPSULE_ERROR_INTERNAL' => 'Internal error',
    'CAPSULE_IMAGES' => [
        1 => '%d image',
        2 => '%d images'
    ],
    'CAPSULE_DONE' => '<strong>Success!</strong> Your picture is ready to share.',
    'CAPSULE_DIRECT_LINK' => 'Direct link:',
    'CAPSULE_BBCODE_LINK' => 'BBCode:',
    'CAPSULE_THUMB_TITLE' => 'Thumbnail',
    'CAPSULE_NAME' => 'Filename',
    'CAPSULE_TIME' => 'Uploaded on',
    'CAPSULE_SIZE' => 'Size',
    'CAPSULE_SHOW_HIDE_BBCODES' => 'Show/hide bbcode'
));