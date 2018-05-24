<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThemesTest extends TestCase
{
    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testGetThemesFromAppContainer()
    {
        $themes = $this->app->make('igaster.themes');
        $this->assertEquals(\Igaster\LaravelTheme\Themes::class, get_class($themes));
    }

    public function testGetThemeViewFinderFromAppContainer()
    {
        $viewFinder = $this->app->make('view.finder');
        $this->assertEquals(\Igaster\LaravelTheme\themeViewFinder::class, get_class($viewFinder));
    }

    public function testThemeFacade()
    {
        $themes = \Theme::getFacadeRoot();
        $this->assertEquals(\Igaster\LaravelTheme\Themes::class, get_class($themes));
    }

    public function testThemesPath()
    {
        $this->assertEquals(base_path('resources/themes'), \Theme::themes_path());
        $this->assertEquals(base_path('resources/themes/filename'), \Theme::themes_path('filename'));
    }

    public function testViewNames()
    {
        $this->captureViews('view2');
        $this->assertViewsRendered([
            'views/view2',
            'views/view1',
        ]);
    }

    public function testViewPathsDefault()
    {
        $theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        $theme2 = new \Igaster\LaravelTheme\Theme('theme2', null, null, $theme1);

        $paths = array_map(function ($item) {
            return substr($item, strlen(base_path())+1);
        }, $theme1->getViewPaths());

        $this->assertEquals([
            "resources/themes/theme1",
        ], $paths);

        $paths = array_map(function ($item) {
            return substr($item, strlen(base_path())+1);
        }, $theme2->getViewPaths());

        $this->assertEquals([
            "resources/themes/theme2",
            "resources/themes/theme1",
        ], $paths);
    }

    public function testViewPathsCustom()
    {
        $theme1 = new \Igaster\LaravelTheme\Theme('theme1', 'assets1', 'views1');
        $theme2 = new \Igaster\LaravelTheme\Theme('theme2', 'assets2', 'views2', $theme1);

        $paths = $this->trimPaths($theme1->getViewPaths());
        $this->assertEquals([
            "resources/themes/views1",
        ], $paths);

        $paths = $this->trimPaths($theme2->getViewPaths());
        $this->assertEquals([
            "resources/themes/views2",
            "resources/themes/views1",
        ], $paths);
    }

    public function testThemeIsSet()
    {
        $theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        $theme2 = new \Igaster\LaravelTheme\Theme('theme2', null, null, $theme1);

        \Theme::set('theme2');
        $this->assertEquals(\Theme::current()->name, 'theme2');
    }

    public function testThemeExists()
    {
        $theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        $theme2 = new \Igaster\LaravelTheme\Theme('theme2', null, null, $theme1);

        $this->assertTrue(\Theme::exists('theme2'));
        $this->assertFalse(\Theme::exists('themeNotExists'));
    }

    private $capturedThemeChange = null;
    public function testThemeChangeBroadcastsEvent()
    {
        \Event::listen('igaster.laravel-theme.change', function ($theme) {
            $this->capturedThemeChange = $theme;
        });

        $theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        \Theme::set('theme2');
        $this->assertNotNull($this->capturedThemeChange);
    }


    public function testWhenWeChangeThemeTheViewPathsCacheIsFlushed(){

        \Theme::set('theme1');
        $theme1Contents = view('view8')->render();

        \Theme::set('theme2');
        $theme2Contents = view('view8')->render();

        $this->assertEquals("theme1", $theme1Contents);
        $this->assertEquals("theme2", $theme2Contents);
    }
}
