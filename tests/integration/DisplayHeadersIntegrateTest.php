<?php

use App\DisplayHeaders;
use App\Response\Cookie;

class DisplayHeadersIntegrateTest extends PHPUnit\Framework\TestCase
{
    public function testDisplayHeadersShouldIntegrateWithCookieClass()
    {
        $cookie1 = new Cookie('name1', 'value123456');
        $cookie2 = new Cookie('name2', 'value678912332', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie2->setExpires('2 hours 20 min 38 seconds');

        $displayHeaders = new DisplayHeaders();
        $displayHeaders->add($cookie1);
        $displayHeaders->add($cookie2);

        $result = $displayHeaders->getHeaderString();

        $this->assertEquals(<<<HEADERS
Set-Cookie: name1=value123456
Set-Cookie: name2=value678912332; Expires=Mon, 06 Feb 2023 15:20:38 GMT
HEADERS, $result);
    }

    public function testDisplayHeadersShouldIntegrateWithCookieClassWithExpires()
    {
        $cookie1 = new Cookie('name1', 'value123456', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie1->setExpires('1 day 2 hours 42 minutes 29 seconds');

        $cookie2 = new Cookie('name2', 'value678912332', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie2->setExpires('2 hours 20 min 38 seconds');

        $displayHeaders = new DisplayHeaders();
        $displayHeaders->add($cookie1);
        $displayHeaders->add($cookie2);

        $result = $displayHeaders->getHeaderString();

        $this->assertEquals(<<<HEADERS
Set-Cookie: name1=value123456; Expires=Tue, 07 Feb 2023 15:42:29 GMT
Set-Cookie: name2=value678912332; Expires=Mon, 06 Feb 2023 15:20:38 GMT
HEADERS, $result);
    }

    public function testDisplayHeadersShouldIntegrateWith3CookieClassWithExpires()
    {
        $cookie1 = new Cookie('name1', 'value123456', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie1->setExpires('1 day 2 hours 42 minutes 29 seconds');

        $cookie2 = new Cookie('name2', 'value678912332', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie2->setExpires('2 hours 20 min 38 seconds');

        $cookie3 = new Cookie('name3', 'valueqwee12334', new DateTimeImmutable('2023-02-06 13:00:00'));
        $cookie3->setExpires('1 hours 30 min 22 seconds');

        $displayHeaders = new DisplayHeaders();
        $displayHeaders->add($cookie1);
        $displayHeaders->add($cookie2);
        $displayHeaders->add($cookie3);

        $result = $displayHeaders->getHeaderString();

        $this->assertEquals(<<<HEADERS
Set-Cookie: name1=value123456; Expires=Tue, 07 Feb 2023 15:42:29 GMT
Set-Cookie: name2=value678912332; Expires=Mon, 06 Feb 2023 15:20:38 GMT
Set-Cookie: name3=valueqwee12334; Expires=Mon, 06 Feb 2023 14:30:22 GMT
HEADERS, $result);
    }

    public function testDisplayHeadersWithoutHeadersShouldThrowAnException()
    {
        $this->expectException(\Exception::class);
        $this->getExpectedExceptionMessage('There is no headers to display');
        
        $displayHeaders = new DisplayHeaders();
        $displayHeaders->getHeaderString();
    }
}