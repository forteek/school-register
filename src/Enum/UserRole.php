<?php

namespace App\Enum;

enum UserRole: string
{
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
}