<?php

namespace Tests\Feature;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    private const TRUSTED_PROXY = '10.0.0.10';

    #[Test]
    public function direct_https_request_remains_secure_without_forwarded_headers(): void
    {
        $result = $this->inspectRequest([
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTPS' => 'on',
            'SERVER_PORT' => 443,
        ]);

        $this->assertTrue($result['secure']);
        $this->assertSame('https', $result['scheme']);
        $this->assertStringStartsWith('https://', $result['url']);
        $this->assertStringStartsWith('https://', $result['redirect']);
    }

    #[Test]
    public function trusted_proxy_can_report_the_original_https_scheme(): void
    {
        $result = $this->inspectRequest([
            'REMOTE_ADDR' => self::TRUSTED_PROXY,
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);

        $this->assertTrue($result['secure']);
        $this->assertSame('https', $result['scheme']);
        $this->assertStringStartsWith('https://', $result['url']);
        $this->assertStringStartsWith('https://', $result['redirect']);
    }

    #[Test]
    public function untrusted_client_cannot_forge_https_with_a_forwarded_header(): void
    {
        $result = $this->inspectRequest([
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);

        $this->assertFalse($result['secure']);
        $this->assertSame('http', $result['scheme']);
        $this->assertStringStartsWith('http://', $result['url']);
        $this->assertStringStartsWith('http://', $result['redirect']);
    }

    #[Test]
    public function request_without_proxy_headers_keeps_its_direct_scheme(): void
    {
        $result = $this->inspectRequest([
            'REMOTE_ADDR' => '203.0.113.10',
        ]);

        $this->assertFalse($result['secure']);
        $this->assertSame('http', $result['scheme']);
    }

    #[Test]
    public function signed_url_remains_valid_behind_the_trusted_proxy(): void
    {
        config(['trustedproxy.proxies' => [self::TRUSTED_PROXY]]);

        $context = $this->inspectRequest([
            'REMOTE_ADDR' => self::TRUSTED_PROXY,
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinute(),
            ['id' => 1, 'hash' => 'email-hash']
        );
        $internalUrl = preg_replace('/^https:/', 'http:', $signedUrl);
        $request = Request::create($internalUrl, 'GET', [], [], [], [
            'REMOTE_ADDR' => self::TRUSTED_PROXY,
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);

        $valid = (new TrustProxies())->handle(
            $request,
            fn (Request $request): bool => URL::hasValidSignature($request)
        );

        $this->assertSame('https', $context['scheme']);
        $this->assertStringStartsWith('https://', $signedUrl);
        $this->assertTrue($valid);
    }

    private function inspectRequest(array $server): array
    {
        config(['trustedproxy.proxies' => [self::TRUSTED_PROXY]]);

        $scheme = ($server['HTTPS'] ?? null) === 'on' ? 'https' : 'http';
        $request = Request::create($scheme.'://applicationbase.test/proxy-check', 'GET', [], [], [], $server);

        return (new TrustProxies())->handle($request, function (Request $request): array {
            URL::setRequest($request);

            return [
                'secure' => $request->isSecure(),
                'scheme' => $request->getScheme(),
                'url' => URL::to('/panel'),
                'redirect' => redirect('/panel')->getTargetUrl(),
            ];
        });
    }
}
