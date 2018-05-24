<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThemesInheritanceTest extends TestCase
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

    public function testViewPaths()
    {
        $viewFinder = $this->app->make('view.finder');

        \Theme::set('theme1');
        $paths = $this->trimPaths($viewFinder->getPaths());
        $this->assertEquals([
            "resources/themes/theme1",
            "resources/views",
        ], $paths);

        \Theme::set('theme2');
        $paths = $this->trimPaths($viewFinder->getPaths());
        $this->assertEquals([
            "resources/themes/theme2",
            "resources/themes/theme1",
            "resources/views",
        ], $paths);
    }

    public function testThemeHierarcy()
    {
        \Theme::set('theme2');
        $this->captureViews('view1');
        $this->assertViewsRendered([
            "themes/theme2/view1",
            "themes/theme1/layout",
        ]);
    }

    public function testThemeHierarcy2()
    {
        \Theme::set('theme2');
        $this->captureViews('view2');
        $this->assertViewsRendered([
            "themes/theme2/view2",
            "themes/theme1/view4",
            "themes/theme2/view1",
            "themes/theme1/layout",
        ]);
    }

    public function testFallbackToDefault()
    {
        \Theme::set('theme2');
        $this->captureViews('view3');
        $this->assertViewsRendered([
            "views/view3",
        ]);
    }

    public function testComponents()
    {
        \Theme::set('theme2');
        $this->captureViews('view5');
        // dd($this->viewNames);
        $this->assertViewsRendered([
            "themes/theme2/view5",
            "views/component",
            "themes/theme1/component2",
            "themes/theme2/component3",
        ]);
    }

    public function testThemeSettings()
    {
        $this->theme1->setSetting('key', 'value');
        $this->assertEquals('value', $this->theme1->getSetting('key'));
        $this->assertEquals('default', $this->theme1->getSetting('not-exists', 'default'));
        $this->assertNull($this->theme1->getSetting('not-exists'));
    }

    public function testThemeSettingsLoadFromParent()
    {
        $this->theme1->setSetting('key1', 'value1');
        $this->theme1->setSetting('key2', 'value2');
        $this->theme2->setSetting('key1', 'value3');

        $this->assertEquals('value1', $this->theme1->getSetting('key1'));
        $this->assertEquals('value2', $this->theme1->getSetting('key2'));
        $this->assertEquals('value3', $this->theme2->getSetting('key1'));
        $this->assertEquals('value2', $this->theme2->getSetting('key2'));

        $this->assertEquals('default', $this->theme2->getSetting('not-exists', 'default'));
        $this->assertNull($this->theme2->getSetting('not-exists'));
    }
}
