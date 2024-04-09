<?php

namespace App\Immutable;

final class SystemConfig
{
    const USER_IMAGE_UPLOAD_DIR = 'public/assets/images/users';
    const USER_IMAGE_BASE_PATH = 'assets/images/users';
    const USER_IMAGE_UPLOAD_FILE_PATTERN = '[contenthash]-[timestamp].[extension]';
}