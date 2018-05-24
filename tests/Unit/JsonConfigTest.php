<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JsonConfigTest extends TestCase
{
    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testThemeLoadedFromJson()
    {
        $theme = \Theme::find('theme-child');
        $this->assertEquals(\Igaster\LaravelTheme\Theme::class, get_class($theme));
    }

    public function testThemePaths()
    {
        $theme = \Theme::find('theme-child');
        $this->assertEquals('customPath', $theme->assetPath);
        $this->assertEquals('themeChild', $theme->viewsPath);

        $theme = \Theme::find('theme-parent');
        $this->assertEquals('json-theme-parent', $theme->assetPath);
        $this->assertEquals('themeParent', $theme->viewsPath);
    }

    public function testParentIsSet()
    {
        $theme = \Theme::find('theme-child');
        $this->assertEquals(\Igaster\LaravelTheme\Theme::class, get_class($theme->getParent()));
        $this->assertEquals('theme-parent', $theme->getParent()->name);
    }

    public function testSettingsAreLoaded()
    {
        $theme = \Theme::find('theme-child');
        $this->assertEquals('value1', $theme->getSetting('option1'));
    }

    public function testSettingsAreLookedUpInTheParentTheme()
    {
        $theme = \Theme::find('theme-child');
        $this->assertEquals($theme->getSetting('parent-option'), 'parent-value');
    }

    public function testStandardConfigurationNotLoadedAsSetting()
    {
        $theme = \Theme::find('theme-child');
        $this->assertNull($theme->getSetting('name'));
    }

    public function testEmptyJsonFileLoadsDefaults()
    {
        $theme = \Theme::find('theme4');
        $this->assertEquals('theme4', $theme->name);
        $this->assertEquals('theme4', $theme->viewsPath);
        $this->assertEquals('theme4', $theme->assetPath);
    }
}
