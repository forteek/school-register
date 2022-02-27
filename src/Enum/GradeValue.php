<?php

namespace App\Enum;

class GradeValue
{
    public const A = 'A';
    public const B = 'B';
    public const C = 'C';
    public const D = 'D';
    public const E = 'E';

    public const ALL = [
        self::A => self::A,
        self::B => self::B,
        self::C => self::C,
        self::D => self::D,
        self::E => self::E,
    ];

    public const VALUE = [
        self::A => 6,
        self::B => 5,
        self::C => 4,
        self::D => 3,
        self::E => 2,
    ];
}