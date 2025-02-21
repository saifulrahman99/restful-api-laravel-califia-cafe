<?php

namespace App\Enums;

enum ResponseMessage: string
{
    // Success Messages
    case SUCCESS = 'Operation successful';
    case CREATED = 'Resource created successfully';
    case UPDATED = 'Resource updated successfully';
    case DELETED = 'Resource deleted successfully';

    // Error Messages
    case NOT_FOUND = 'Resource not found';
    case VALIDATION_ERROR = 'Validation error occurred';
    case SERVER_ERROR = 'Internal server error';
    case UNAUTHORIZED = 'Unauthorized access';
    case FORBIDDEN = 'Access is forbidden';

    // Method to customize or translate messages if needed
    public function message(): string
    {
        return match ($this) {
            self::SUCCESS => __('Operation successful'),
            self::CREATED => __('Resource created successfully'),
            self::UPDATED => __('Resource updated successfully'),
            self::DELETED => __('Resource deleted successfully'),
            self::NOT_FOUND => __('Resource not found'),
            self::VALIDATION_ERROR => __('Validation error occurred'),
            self::SERVER_ERROR => __('Internal server error'),
            self::UNAUTHORIZED => __('Unauthorized access'),
            self::FORBIDDEN => __('Access is forbidden'),
        };
    }
}
