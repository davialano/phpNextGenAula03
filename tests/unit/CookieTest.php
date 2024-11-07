<?php

use App\Response\Cookie;
use App\Response\Expires;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Cookie::class)]
final class CookieTest extends PHPUnit\Framework\TestCase
{
    public static function positiveCookieDataProvider()
    {
        return [
            ['attribute', 'value', 'Set-Cookie: attribute=value'],
            ['mycookie', 'meu valor com espaço', 'Set-Cookie: mycookie=meu+valor+com+espa%C3%A7o'],
            ['complex', 'teste ()/\\$#*&}}{_-', 'Set-Cookie: complex=teste+%28%29%2F%5C%24%23%2A%26%7D%7D%7B_-'],
            ['attribute', '  value  ', 'Set-Cookie: attribute=value'],
            ['  attribute  ', 'value', 'Set-Cookie: attribute=value'],
        ];
    }

    #[DataProvider('positiveCookieDataProvider')]
    public function testCookieComponentShouldReturnCookieHeaderString(
        string $cookieName,
        string $cookieValue,
        string $expect
    ) {
        $cookie = new Cookie($cookieName, $cookieValue);
        $result = $cookie->getHeaderString();
        $this->assertEquals($expect, $result);
    }

    public static function negativeCookieDataProvider()
    {
        return [
            ['aço', 'valor aqui', InvalidArgumentException::class],
            ['primário', 'valor aqui', InvalidArgumentException::class],
            ['campo espaco', 'valor aqui', InvalidArgumentException::class],
            ['campo espaço mais', 'valor aqui', InvalidArgumentException::class]
        ];
    }

    #[DataProvider('negativeCookieDataProvider')]
    public function testCookieComponenteShouldNotAcceptWrongName(
        string $cookieName,
        string $cookieValue,
        string $expect
    ) {
        $this->expectException($expect);
        $cookie = new Cookie($cookieName, $cookieValue);
    }

    public function testCookieComponentShouldReturnValueUrlEncoded()
    {
        $cookie = new Cookie('attribute', 'a value with spaces');
        $result = $cookie->getHeaderString();
        $this->assertEquals('Set-Cookie: attribute=a+value+with+spaces', $result);
    }

    public static function positiveCookieExpiresDataProvider(): array
    {
        return require 'fixture/DataProviders/positiveCookieExpires.php';
    }

    #[DataProvider('positiveCookieExpiresDataProvider')]
    public function testCookieComponentShouldReturnExpiresAttribute(
        string $cookieName,
        string $cookieValue,
        string $startDate,
        string $expireInterval,
        string $expect
    ) {
        $expiresMock = $this->getExpiresMock();

        $expiresMock->expects()
                    ->get()
                    ->andReturn($expireInterval)
                ;
        
        $expiresMock->shouldReceive('hours->minutes')
                    ->andReturn($expiresMock)
                ;

        $cookie = new Cookie($cookieName, $cookieValue, new DateTimeImmutable($startDate));

        $cookie->setExpires(
            $expiresMock->hours()
                        ->minutes()
                    
        );

        $result = $cookie->getHeaderString();
        
        $this->assertEquals(
            $expect,
            $result
        );
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    protected function getExpiresMock(): Mockery\LegacyMockInterface|Expires
    {
        return Mockery::mock(Expires::class);
    }
}