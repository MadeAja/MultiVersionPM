<?php

namespace Bavfalcon9\MultiVersion\utils;

class Messages {
    public static array $defaults = [
        "disconnect.reason.outdated_protocol" => "&cYour game's version ({%0}) is not supported by MultiVersion",
        "disconnect.reason.newer_protocol" => "&cMultiVersion has not updated to your game version yet.",
        "disconnect.reason.start_error" => "&cMultiVersion ran into an issue whilst processing your login.",
        "disconnect.reason.not_allowed" => "&cYour current game version is dis-allowed by this server.",
        "disconnect.reason.shutdown" => "&cYou have been disconnected due to shutdown.",
        "error.unknown_message" => "&cMultiVersion encountered an error."
    ];

    public static function get(string $key, ...$args): string {

    }
}