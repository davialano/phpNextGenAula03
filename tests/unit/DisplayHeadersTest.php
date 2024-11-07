<?php

use App\DisplayHeaders;
use App\Interfaces\HeaderStringInterface;
use App\Response\Cookie;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DisplayHeaders::class)]
final class DisplayHeadersTest extends PHPUnit\Framework\TestCase
{
    protected $cookieStub;

    protected $contentStub;

    protected function setUp(): void
    {
        chdir('tests/unit');
        file_put_contents('output.txt', "HTTP/1.1 200 OK\n");

        $this->cookieStub = \Mockery::mock(Cookie::class);
        $this->contentStub = \Mockery::mock(HeaderStringInterface::class);
    }

    public function tearDown(): void
    {
        unlink('output.txt');
        \Mockery::close();
    }

    public function testDisplayHeadersComponentShouldDisplayHeadersAsString()
    {
        $displayHeaders = new DisplayHeaders();

        $this->cookieStub->allows()
                   ->getHeaderString()
                   ->andReturn('Set-Cookie: name=valor')
        ;

        $this->contentStub->allows()
                   ->getHeaderString()
                   ->andReturn('Content-Type: text/html; charset=utf-8')
        ;

        $displayHeaders->add($this->cookieStub);
        $displayHeaders->add($this->contentStub);

        $result = $displayHeaders->getHeaderString();
        $this->assertEquals(<<<HEADER
Set-Cookie: name=valor
Content-Type: text/html; charset=utf-8
HEADER, $result);
    }

    public function testDisplayHeadersComponentShouldDisplayNewHeadersAsString()
    {
        $displayHeaders = new DisplayHeaders();

        $this->cookieStub->allows()
                   ->getHeaderString()
                   ->andReturn('Set-Cookie: maisumcampo=valor12345')
        ;

        $this->contentStub->allows()
                   ->getHeaderString()
                   ->andReturn('Content-Type: multipart/form-data; boundary=something')
        ;

        $displayHeaders->add($this->cookieStub);
        $displayHeaders->add($this->contentStub);

        $result = $displayHeaders->getHeaderString();
        $this->assertEquals(<<<HEADER
Set-Cookie: maisumcampo=valor12345
Content-Type: multipart/form-data; boundary=something
HEADER, $result);
    }

    public function testDisplayHeaderShouldDisplayHearsInsideAFile()
    {
        $cookieStub = $this->getCookieStub();
        $contentStub = $this->getContentStub();

        $displayHeaders = new DisplayHeaders();
        $displayHeaders->add($cookieStub);
        $displayHeaders->add($contentStub);

        $displayHeaders->displayInFile('output.txt');

        $this->assertStringEqualsFile('output.txt', str_replace("\r", "", <<<HEADER
HTTP/1.1 200 OK
Set-Cookie: maisumcampo=valor12345
Content-Type: text/html; charset=utf-8
HEADER));
    }

    protected function getCookieStub(): Mockery\LegacyMockInterface|Cookie
    {
        return Mockery::mock(Cookie::class, ['getHeaderString' => 'Set-Cookie: maisumcampo=valor12345']);
    }

    protected function getContentStub(): Mockery\LegacyMockInterface|HeaderStringInterface
    {
        return Mockery::mock(HeaderStringInterface::class, ['getheaderString' => 'Content-Type: text/html; charset=utf-8']);
    }
}