<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use Chr15k\AuthGenerator\Builders\JWTBuilder;
use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Generators\JWT;
use InvalidArgumentException;
use JsonSerializable;
use stdClass;

test('jwt builder creates data with correct key and algorithm', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->algorithm(Algorithm::HS256);

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    expect($builder->toString())->toBeString();
});

test('jwt builder adds claims correctly', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123)
        ->claim('role', 'admin');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder adds multiple claims at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claims([
            'user_id' => 123,
            'role' => 'admin',
            'name' => 'Test User',
        ]);

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder adds standard claims correctly', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->expiresIn(3600)
        ->issuedBy('https://test.com')
        ->subject('user-123')
        ->audience('api');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder can add unique jwt id', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withUniqueJwtId();

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can set not-before time', function (): void {
    $tomorrow = time() + 86400;

    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->notBefore($tomorrow);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can add all timestamp claims at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can add multiple headers at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->headers([
            'kid' => 'key-123',
            'typ' => 'JWT',
            'custom' => 'value',
        ]);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder can add specific jwt id', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withJwtId('custom-id-123');

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder sets default not-before time when not specified', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600, null); // Explicitly pass null for notBefore

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder sets not-before', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600, time());

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can generate formatted header string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $formatted = $builder->toHeader();

    expect($formatted)->toBeString();
    expect($formatted)->toStartWith('Bearer ');
});

test('jwt builder toString returns correct token string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder toArray returns correct headers', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $headers = $builder->toArray();

    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toStartWith('Bearer ');
});

test('jwt builder toHeader returns correct header string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $header = $builder->toHeader();

    expect($header)->toBeString();
    expect($header)->toStartWith('Bearer ');
});

test('jwt builder can work with nested payloads', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user', [
            'id' => 123,
            'role' => 'admin',
            'details' => [
                'email' => 'chris@example.com',
            ],
        ]);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
    $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
    expect($payload)->toHaveKey('user');
    expect($payload['user'])->toHaveKey('id');
    expect($payload['user']['id'])->toBe(123);
    expect($payload['user'])->toHaveKey('role');
    expect($payload['user']['role'])->toBe('admin');
    expect($payload['user'])->toHaveKey('details');
    expect($payload['user']['details'])->toHaveKey('email');
    expect($payload['user']['details']['email'])->toBe('chris@example.com');
});

test('jwt builder can handle nested payload via claims method', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claims([
            'user' => [
                'id' => 123,
                'role' => 'admin',
                'details' => [
                    'email' => 'chris@example.com',
                ],
            ],
        ]);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
    $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
    expect($payload)->toHaveKey('user');
    expect($payload['user'])->toHaveKey('id');
    expect($payload['user']['id'])->toBe(123);
    expect($payload['user'])->toHaveKey('role');
    expect($payload['user']['role'])->toBe('admin');
    expect($payload['user'])->toHaveKey('details');
    expect($payload['user']['details'])->toHaveKey('email');
    expect($payload['user']['details']['email'])->toBe('chris@example.com');
});

test('jwt builder throws exception for non-serializable claim values', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->claim('non_serializable', fopen('php://temp', 'r'));
})->throws(InvalidArgumentException::class, "Claim 'non_serializable' contains non-serializable data");

test('jwt builder throws exception for non-scalar header values', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->header('non_scalar', fopen('php://temp', 'r'));
})->throws(InvalidArgumentException::class, 'Header value must be scalar, resource (stream) given');

test('jwt builder stringifies headers correctly', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->header('custom_header', ['value' => 'test']);
})->throws(InvalidArgumentException::class, 'Header value must be scalar, array given');

test('jwt builder handles empty claims gracefully', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->claims([]);
    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
    $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
    expect($payload)->toBeArray();
    expect($payload)->toBeEmpty();
});

test('jwt builder handles empty headers gracefully', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->headers([]);
    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
    $headers = json_decode(base64_decode(explode('.', $token)[0]), true);
    expect($headers)->toBeArray();
    expect($headers)->toBe(['typ' => 'JWT', 'alg' => 'HS256']);
});

test('jwt builder ignores null header values', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->header('nullable_header', null);

    $token = $builder->toString();
    $headers = json_decode(base64_decode(explode('.', $token)[0]), true);

    expect($headers)->not->toHaveKey('nullable_header');
    expect($headers)->toBe(['typ' => 'JWT', 'alg' => 'HS256']);
});

test('jwt builder converts int to string header values', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->header('int_header', 123);
    $token = $builder->toString();
    $headers = json_decode(base64_decode(explode('.', $token)[0]), true);
    expect($headers)->toHaveKey('int_header');
    expect($headers['int_header'])->toBe('123');
});

test('jwt builder throws exception for non-serializable object claims values', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->claim('object_claim', new stdClass);
})->throws(InvalidArgumentException::class, "Claim 'object_claim' contains non-serializable data");

test('jwt builder throws exception for non-serializable object claims values within an array', function (): void {
    $builder = new JWTBuilder;
    $builder->key('test-key');
    $builder->claims(['test' => ['object_claim' => new stdClass]]);
})->throws(InvalidArgumentException::class, "Claim 'test' contains non-serializable data");

test('jwt builder handles serializable object claims', function (): void {
    $builder = new JWTBuilder;
    $object = new class implements JsonSerializable
    {
        public function jsonSerialize(): array
        {
            return ['key' => 'value'];
        }
    };
    $builder->key('test-key');
    $builder->claim('serializable_object', $object);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});
