<?php
/**
 * Project: inksandnibs.
 * User: ruben
 * Date: 16/08/17
 * Time: 17:34
 */

namespace Inks;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testLogin()
    {
        $app = new App();
        $result = $app->login('rubendx@gmail.com', '1nk5&n1b5');
        $this->assertCount(1, $result);
        $result = $app->login('', '');
        $this->assertCount(0, $result);
        $result = $app->login('otro', 'asdfasd');
        $this->assertCount(0, $result);
    }
}
