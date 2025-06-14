<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use Chr15k\Codec\Helpers\AuthHeaderFormatter;

test('formats basic auth correctly', function (): void {
    $token = 'dXNlcjpwYXNz'; // base64 of "user:pass"
    $formattedHeader = AuthHeaderFormatter::formatBasicAuth($token);

    expect($formattedHeader)->toBe('Basic dXNlcjpwYXNz');
});

test('formats bearer token correctly', function (): void {
    $token = 'api_1234abcd';
    $formattedHeader = AuthHeaderFormatter::formatBearerToken($token);

    expect($formattedHeader)->toBe('Bearer api_1234abcd');
});

test('formats custom scheme correctly', function (): void {
    $token = 'custom-token-123';
    $formattedHeader = AuthHeaderFormatter::formatHeader('CustomScheme', $token);

    expect($formattedHeader)->toBe('CustomScheme custom-token-123');
});
