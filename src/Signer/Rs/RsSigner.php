<?php

namespace Webdevcave\Jwt\Signer\Rs;

use RuntimeException;
use Webdevcave\Jwt\Secrets\RsSecret;
use Webdevcave\Jwt\Secrets\Secret;
use Webdevcave\Jwt\Signer\Signer;

abstract class RsSigner extends Signer
{
    /**
     * @var string
     */
    protected int $openSslAlgorithm;

    /**
     * @param string $header
     * @param string $payload
     *
     * @return mixed
     */
    public function sign(string $header, string $payload): mixed
    {
        /* @var $secret RsSecret */
        $secret = $this->getSecret();
        $signature = null;
        $signed = openssl_sign(
            "$header.$payload",
            $signature,
            $secret->privateKey,
            $this->openSslAlgorithm
        );

        if (!$signed) {
            throw new RuntimeException('Failed to create signature');
        }

        return $signature;
    }

    /**
     * @param string $header
     * @param string $payload
     * @param string $signature
     *
     * @return bool
     */
    public function verify(string $header, string $payload, string $signature): bool
    {
        /* @var $secret RsSecret */
        $secret = $this->getSecret();

        return openssl_verify(
            "$header.$payload",
            $signature,
            $secret->publicKey,
            $this->openSslAlgorithm
        ) === 1;
    }

    /**
     * @param Secret $secret
     *
     * @return bool
     */
    protected function validateSecret(Secret $secret): bool
    {
        return $secret instanceof RsSecret;
    }
}
