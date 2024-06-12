<?php

namespace App\Constants;

final class FilePathConstants
{
    public const SYSTEM_IMAGE_UPLOAD_DIR = 'public/resource/images/system';
    public const SYSTEM_IMAGE_BASE_PATH = 'resource/images/system';
    public const SYSTEM_CSS_FILE = 'resource/css/style.css';
    public const SYSTEM_JS_FILE = 'resource/js/main.js';
    public const USER_IMAGE_UPLOAD_DIR = 'public/resource/images/users';
    public const USER_IMAGE_BASE_PATH = 'resource/images/users';
    public const USER_IMAGE_UPLOAD_FILE_PATTERN = '[contenthash]-[timestamp].[extension]';
}
