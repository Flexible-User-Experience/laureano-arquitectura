<?php

namespace App\Enum;

enum EnvironmentEnum: string
{
    case CLI = 'cli';
    case PROD = 'prod';
    case DEV = 'dev';
    case TEST = 'test';
}
