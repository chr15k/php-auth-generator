<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Generators;

use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\JWTData;
use Chr15k\AuthGenerator\Enums\Algorithm;
use Closure;
use DomainException;
use Exception;
use InvalidArgumentException;

/**
 * @internal
 *
 * Generates a JSON Web Token (JWT) based on provided headers, payload, and algorithm.
 * This class handles the encoding of headers and payload, generates the signature
 * using the specified algorithm, and constructs the final JWT string in the format:
 * 'header.payload.signature'.
 *
 * This implementation supports various algorithms including HMAC, RSA, ECDSA, and EdDSA.
 * It uses OpenSSL for RSA and ECDSA signatures, and libsodium for EdDSA signatures.
 *
 * The token generation process conforms to the JWT specification as defined in RFC 7519.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7519
 *
 * NOTE: This implementation focuses exclusively on JWT token generation and does
 * not provide decoding functionality. If full JWT encoding/decoding is required, I'd
 * recommend using a dedicated library such as `firebase/php-jwt`.
 */
final readonly class JWT implements Generator
{
    /**
     * ASN.1 tag identifier for BIT STRING type
     * Used in DER encoding/decoding for ECDSA signatures
     */
    private const ASN1_BIT_STRING = 0x03;

    /** Encoded headers */
    private string $headers;

    /** Encoded payload */
    private string $payload;

    /** Message for signing */
    private string $message;

    /**
     * Initializes a new JWT generator with the provided JWT data.
     *
     * @param  JWTData  $data  The data transfer object containing JWT configuration
     */
    public function __construct(private JWTData $data)
    {
        $this->headers = $this->encodeHeaders();
        $this->payload = $this->encodePayload();

        $this->message = implode('.', [$this->headers, $this->payload]);
    }

    /**
     * Generates the complete JWT token.
     *
     * This method creates the JWT by:
     * 1. Generating the signature based on headers and payload
     * 2. Base64Url encoding the signature
     * 3. Concatenating headers, payload, and signature with period separators
     *
     * @return string The complete JWT string in format 'header.payload.signature'
     *
     * @throws DomainException When signature generation fails
     */
    public function generate(): string
    {
        $signature = $this->generateSignature();

        $encoded = $this->base64UrlEncode($signature);

        return implode('.', [$this->message, $encoded]);
    }

    /**
     * Generates the signature for the JWT.
     *
     * Creates and executes a signature generator closure based on
     * the algorithm specified in the JWTData DTO.
     *
     * @return string The generated signature
     *
     * @throws DomainException When signature generation fails
     */
    private function generateSignature(): string
    {
        $closure = $this->createSignatureGenerator();

        return $closure();
    }

    /**
     * Prepares a closure for generating JWT signature based on the DTO's specified algorithm.
     *
     * Creates and returns a closure function that will generate a signature using one of the
     * following methods:
     *
     * - hash_hmac: For HMAC-based algorithms
     * - openssl: For RSA and ECDSA algorithms
     * - sodium_crypto: For EdDSA algorithms
     *
     * @return Closure Returns a closure that generates the appropriate signature
     *
     * @throws DomainException When the algorithm is not supported, or when there's an issue with key validation
     * @throws InvalidArgumentException When the key format is invalid for EdDSA algorithms
     */
    private function createSignatureGenerator(): Closure
    {
        ['func' => $func, 'alg' => $alg] = $this->data->algorithm->config();

        $key = $this->data->key;

        if ($this->data->keyBase64Encoded) {
            $key = base64_decode($key, true);

            if ($key === false) {
                throw new InvalidArgumentException('Invalid base64 encoded key');
            }
        }

        return match ($func) {

            'hash_hmac' => fn (): string => hash_hmac((string) $alg, $this->message, $key, true),

            'openssl' => function () use ($alg, $key) {
                $signature = '';

                if (is_resource($key) === false && openssl_pkey_get_private($key) === false) {
                    throw new DomainException('OpenSSL unable to validate key');
                }

                openssl_sign($this->message, $signature, $key, $alg);

                return match ($alg) {
                    Algorithm::ES256->value,
                    Algorithm::ES256K->value => $this->generateSignatureFromDER($signature, 256),
                    Algorithm::ES384->value => $this->generateSignatureFromDER($signature, 384),
                    default => $signature
                };
            },

            'sodium_crypto' => function () use ($key): string {
                try {
                    $lines = array_filter(explode("\n", $key));
                    $key = base64_decode((string) end($lines));

                    $key = (string) $key;

                    if ($key === '') {
                        throw new DomainException('Key cannot be empty string');
                    }

                    return sodium_crypto_sign_detached($this->message, $key);
                } catch (Exception $e) {
                    throw new DomainException($e->getMessage(), 0, $e);
                }
            },

            default => throw new DomainException('Algorithm not supported')
        };
    }

    /**
     * Converts a DER-encoded signature to a raw signature format.
     *
     * This method extracts the R and S components from a DER-encoded signature,
     * removes leading zeros, and ensures they have the correct byte length based
     * on the specified key size. The final output is the concatenation of the
     * properly padded R and S values.
     *
     * @param  string  $der  The DER-encoded signature to convert
     * @param  int  $keySize  The size of the key in bits
     * @return string The formatted raw signature as a concatenation of R and S values
     */
    private function generateSignatureFromDER(string $der, int $keySize): string
    {
        // Extract the signature components from the DER format
        [$offset, $_] = $this->parseDER($der);
        [$offset, $r] = $this->parseDER($der, $offset);
        [$offset, $s] = $this->parseDER($der, $offset);

        // Remove leading zeros from the signature values
        $r = is_string($r) ? ltrim($r, "\x00") : '';
        $s = is_string($s) ? ltrim($s, "\x00") : '';

        // Ensure both values have the correct byte length based on key size
        $r = str_pad($r, $keySize / 8, "\x00", STR_PAD_LEFT);
        $s = str_pad($s, $keySize / 8, "\x00", STR_PAD_LEFT);

        return $r.$s;
    }

    /**
     * Reads and parses DER (Distinguished Encoding Rules) encoded data.
     *
     * This method processes DER-encoded binary data, commonly used in ASN.1 structures such as X.509 certificates
     * and private keys. It handles both primitive and constructed types, with special handling for BIT_STRING.
     *
     * @param  string  $der  The DER-encoded data to be parsed
     * @param  int  $offset  The starting position in the DER data (default: 0)
     * @return array{0: int, 1: mixed} An array containing two elements:
     *                                 - The new position after reading
     *                                 - The decoded data (or null for constructed types)
     */
    private function parseDER(string $der, int $offset = 0): array
    {
        $pos = $offset;
        $size = strlen($der);
        $constructed = (ord($der[$pos]) >> 5) & 0x01;
        $type = ord($der[$pos++]) & 0x1F;

        $len = ord($der[$pos++]);
        if (($len & 0x80) !== 0) {
            $n = $len & 0x1F;
            $len = 0;
            while ($n-- && $pos < $size) {
                $len = ($len << 8) | ord($der[$pos++]);
            }
        }

        if ($type === self::ASN1_BIT_STRING) {
            $pos++;
            $data = substr($der, $pos, $len - 1);
            $pos += $len - 1;
        } elseif ($constructed === 0) {
            $data = substr($der, $pos, $len);
            $pos += $len;
        } else {
            $data = null;
        }

        return [$pos, $data];
    }

    /**
     * Encodes the payload data into base64url format.
     *
     * This method filters out null/empty values from the payload array
     * and then encodes the result using base64url encoding.
     *
     * @return string The base64url encoded payload string
     */
    private function encodePayload(): string
    {
        return $this->base64UrlEncode(array_filter($this->data->payload));
    }

    /**
     * Encodes the JWT headers.
     *
     * This method merges the headers from the data object with the algorithm value,
     * removes duplicates and null values, then Base64Url encodes the result.
     *
     * @return string The Base64Url encoded headers
     */
    private function encodeHeaders(): string
    {
        return $this->base64UrlEncode(
            array_filter(array_unique(array_merge(
                $this->data->headers, [
                    'typ' => 'JWT',
                    'alg' => $this->data->algorithm->value,
                ]
            )))
        );
    }

    /**
     * URL-safe base64 encode a string or array.
     *
     * Encodes the input as base64 and then converts it to be URL-safe by replacing
     * '+' with '-', '/' with '_', and removing '=' padding characters.
     *
     * @param  string|array<mixed>  $text  The data to encode. If array, it will be JSON encoded first.
     * @return string The URL-safe base64 encoded string
     */
    private function base64UrlEncode(string|array $text): string
    {
        if (is_array($text)) {
            $text = $text !== [] ? json_encode($text) : '{}';
        }

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode((string) $text));
    }
}
