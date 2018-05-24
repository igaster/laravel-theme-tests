<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErrorPagesTest extends TestCase
{
    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testErrorPage()
    { // Can't Test Error Pages!
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        abort(404);
    }
}
