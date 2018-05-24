<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetsTest extends TestCase
{

    // -----------------------------------------------
    //  Setup: Run before each Test
    // -----------------------------------------------

    public $theme1;
    public $theme2;

    public function setUp()
    {
        parent::setUp();
        $this->theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        $this->theme2 = new \Igaster\LaravelTheme\Theme('theme2', null, null, $this->theme1);
    }

    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testDefaultAssets()
    {
        $this->assertEquals('/file1.txt', \Theme::url('file1.txt'));
        $this->assertEquals('/file1.txt', \Theme::url('/file1.txt'));
    }

    public function testBaseTheme()
    {
        \Theme::set('theme1');

        $this->assertEquals('/theme1/file1.txt', \Theme::url('file1.txt'));
        $this->assertEquals('/file2.txt', \Theme::url('file2.txt'));
        $this->assertEquals('/theme1/file3.txt', \Theme::url('file3.txt'));
        $this->assertEquals('http://test.css', \Theme::url('http://test.css'));
        $this->assertEquals('https://test.css', \Theme::url('https://test.css'));
    }

    public function testSecondTheme()
    {
        \Theme::set('theme2');

        $this->assertEquals('/theme2/file1.txt', \Theme::url('file1.txt'));
        $this->assertEquals('/theme2/file2.txt', \Theme::url('file2.txt'));
        $this->assertEquals('/theme1/file3.txt', \Theme::url('file3.txt'));
        $this->assertEquals('/theme2/file4.txt', \Theme::url('file4.txt'));
    }

    public function testUrlParameters()
    {
        \Theme::set('theme1');
        $this->assertEquals('/file2.txt?param=1', \Theme::url('file2.txt?param=1'));

        \Theme::set('theme2');
        $this->assertEquals('/theme2/file1.txt?param=1', \Theme::url('file1.txt?param=1'));
        $this->assertEquals('/theme1/file3.txt?param=1', \Theme::url('file3.txt?param=1'));
        $this->assertEquals('/theme2/file1.txt?a=1&b=2&c', \Theme::url('file1.txt?a=1&b=2&c'));
    }

    public function testAssetNotFoundThrowsException()
    {
        \Theme::set('theme2');
        config(['themes.asset_not_found' => 'THROW_EXCEPTION']);
        $this->expectException(\Igaster\LaravelTheme\Exceptions\themeException::class);
        \Theme::url('DOESNT_EXISTS.txt');
    }

    public function testAssetNotFoundReturnsUlr()
    {
        \Theme::set('theme2');
        config(['themes.asset_not_found' => 'IGNORE']);
        $this->assertEquals('/DOESNT_EXISTS.txt', \Theme::url('DOESNT_EXISTS.txt'));
    }

    public function testHelperFunctionThemeUrl()
    {
        $this->assertEquals('/file1.txt', theme_url('file1.txt'));
        \Theme::set('theme1');
        $this->assertEquals('/theme1/file1.txt', theme_url('file1.txt'));
    }

    public function testThemeReplaceUrlBracketedTermsFromSettings()
    {
        \Theme::set('theme1');
        config(['themes.asset_not_found' => 'IGNORE']);

        $this->assertEquals('/theme1/file-{key1}.txt', theme_url('file-{key1}.txt'));
        
        $this->theme1->setSetting('key1', 'value1');

        $this->assertEquals('/theme1/file-value1.txt', theme_url('file-{key1}.txt'));
    }
}
