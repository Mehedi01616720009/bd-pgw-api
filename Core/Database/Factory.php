<?php

namespace Core\Database;

class Factory
{
    /**
     * Generate fake name
     */
    public static function name(): string
    {
        $firstNames = ['John', 'Jane', 'Bob', 'Alice', 'Charlie', 'Diana', 'Eve', 'Frank', 'Grace', 'Henry'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Generate fake email
     */
    public static function email(): string
    {
        return strtolower(str_replace(' ', '.', self::name())) . '@example.com';
    }

    /**
     * Generate unique email
     */
    public static function uniqueEmail(): string
    {
        return uniqid() . '_' . self::email();
    }

    /**
     * Generate random text
     */
    public static function text(int $length = 100): string
    {
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

        while (strlen($lorem) < $length) {
            $lorem .= ' ' . $lorem;
        }

        return substr($lorem, 0, $length);
    }

    /**
     * Generate random paragraph
     */
    public static function paragraph(): string
    {
        return self::text(rand(100, 300));
    }

    /**
     * Generate random sentence
     */
    public static function sentence(): string
    {
        return self::text(rand(20, 50)) . '.';
    }

    /**
     * Generate random number
     */
    public static function number(int $min = 0, int $max = 100): int
    {
        return rand($min, $max);
    }

    /**
     * Generate random boolean
     */
    public static function boolean(): bool
    {
        return (bool) rand(0, 1);
    }

    /**
     * Generate random date
     */
    public static function date(string $format = 'Y-m-d'): string
    {
        $timestamp = rand(strtotime('-1 year'), time());
        return date($format, $timestamp);
    }

    /**
     * Generate random datetime
     */
    public static function dateTime(string $format = 'Y-m-d H:i:s'): string
    {
        $timestamp = rand(strtotime('-1 year'), time());
        return date($format, $timestamp);
    }

    /**
     * Pick random element from array
     */
    public static function randomElement(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * Generate random URL
     */
    public static function url(): string
    {
        $domains = ['example.com', 'test.com', 'demo.com', 'sample.org'];
        return 'https://www.' . self::randomElement($domains);
    }

    /**
     * Generate random phone number
     */
    public static function phone(): string
    {
        return sprintf('(%03d) %03d-%04d', rand(100, 999), rand(100, 999), rand(1000, 9999));
    }

    /**
     * Generate random address
     */
    public static function address(): string
    {
        $streets = ['Main St', 'Oak Ave', 'Pine Rd', 'Maple Dr', 'Cedar Ln'];
        return rand(100, 9999) . ' ' . self::randomElement($streets);
    }

    /**
     * Generate slug from text
     */
    public static function slug(string $text): string
    {
        return strtolower(str_replace(' ', '-', $text));
    }

    /**
     * Generate unique slug
     */
    public static function uniqueSlug(string $text): string
    {
        return self::slug($text) . '-' . uniqid();
    }

    /**
     * Generate UUID v4
     */
    public static function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
