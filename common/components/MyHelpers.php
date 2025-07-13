<?php

namespace common\components;



class MyHelpers
{
    // Assuming this is in a helper class or a component

    public static function generateUuidV4()
    {
        $data = random_bytes(16);

        // Set version to 0100 (UUIDv4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

        // Set variant to 10x (RFC 4122)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    public static function hasAtLeastTenDigits($phoneNumber)
    {
        // Remove all non-digit characters from the phone number
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Convert the phone number to a string
        $phoneNumberAsString = (string) $phoneNumber;

        // Get the length of the string
        $length = strlen($phoneNumberAsString);

        // Check if the length is at least 10
        if ($length >= 10 && $length <= 11) {
            return true;
        } else {
            return false;
        }
    }

    public static function timeAgo($timestamp)
    {
        $current_time = time();
        $time_difference = $current_time - $timestamp;
        $seconds = $time_difference;

        $minutes      = round($seconds / 60);           // value 60 is seconds
        $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
        $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 sec
        $weeks        = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
        $months       = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec
        $years        = round($seconds / 31553280);     // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 minutes * 60 sec

        if ($seconds <= 60) {
            return "Just now";
        } elseif ($minutes <= 60) {
            return "$minutes minute" . ($minutes > 1 ? 's' : '') . " ago";
        } elseif ($hours <= 24) {
            return "$hours hour" . ($hours > 1 ? 's' : '') . " ago";
        } elseif ($days <= 7) {
            return "$days day" . ($days > 1 ? 's' : '') . " ago";
        } elseif ($weeks <= 4.3) {  // 4.3 == 30/7
            return "$weeks week" . ($weeks > 1 ? 's' : '') . " ago";
        } elseif ($months <= 12) {
            return "$months month" . ($months > 1 ? 's' : '') . " ago";
        } else {
            return "$years year" . ($years > 1 ? 's' : '') . " ago";
        }
    }

    public static function timeAgoInMinutes($timestamp)
    {

        $current_time = time();
        $time_difference = $current_time - $timestamp;
        $seconds = $time_difference;

        return round($seconds / 60);
    }


    public static function  normalizePhoneNumber($phoneNumber)
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // If the number starts with a single zero, remove it
        if (strlen($phoneNumber) == 11 && $phoneNumber[0] == '0') {
            $phoneNumber = '1' . substr($phoneNumber, 1);
        }

        // If the number starts with a plus sign, remove it
        if (strlen($phoneNumber) > 0 && $phoneNumber[0] == '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        // If the number doesn't start with '1', prepend it
        if (strlen($phoneNumber) == 10) {
            $phoneNumber = '1' . $phoneNumber;
        }

        return $phoneNumber;
    }

    public static function validateEmail($email)
    {
        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Validate email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}
