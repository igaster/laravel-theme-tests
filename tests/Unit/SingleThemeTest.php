<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SingleThemeTest extends TestCase
{

    // -----------------------------------------------
    //  Setup: Run before each Test
    // -----------------------------------------------

    public $theme1;

    public function setUp(): void
    {
        parent::setUp();
        $this->theme1 = new \Igaster\LaravelTheme\Theme('theme1');
    }

    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testThemeOveridesDefaultView()
    {
        \Theme::set('theme1');

        $this->captureViews('view1');
        $this->assertViewsRendered([
            "themes/theme1/view1",
            "themes/theme1/layout",
        ]);
    }

    public function testThemeFallbackToDefaultView()
    {
        \Theme::set('theme1');

        $this->captureViews('view3');
        $this->assertViewsRendered([
            "views/view3",
        ]);
    }

    public function testThemeIncludesOveridenView()
    {
        \Theme::set('theme1');

        $this->captureViews('view4');
        $this->assertViewsRendered([
            "themes/theme1/view4",
            "themes/theme1/view1",
            "themes/theme1/layout",
        ]);
    }

    public function testThemeIncludesViewFromDefault()
    {
        \Theme::set('theme1');
        $this->captureViews('view5');
        $this->assertViewsRendered([
            "themes/theme1/view5",
            "views/view3",
        ]);
    }

    public function testThemeIncludesComponentFromDefault()
    {
        \Theme::set('theme1');
        $this->captureViews('view6');
        $this->assertViewsRendered([
            "themes/theme1/view6",
            "views/component",
        ]);
    }

    public function testThemeOverridesComponent()
    {
        \Theme::set('theme1');
        $this->captureViews('view7');
        $this->assertViewsRendered([
            "themes/theme1/view7",
            "themes/theme1/component2",
        ]);
    }

    public function testMapFuctionsToCurrentTheme()
    {
        \Theme::set('theme1');

        $this->theme1->setSetting('key1', 'value1');
        $this->assertEquals('value1', \Theme::getSetting('key1'));

        \Theme::setSetting('key2', 'value2');
        $this->assertEquals('value2', $this->theme1->getSetting('key2'));
    }

    public function testMapFuctionsToCurrentThemeThrowsExceptionIfNotFound()
    {
        \Theme::set('theme1');
        $this->expectException(\Exception::class);
        \Theme::adasdas();
    }
}
