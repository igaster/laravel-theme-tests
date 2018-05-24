<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConfigThemesPhpTest extends TestCase
{
    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testThemeDefinedInThemesPhpConfigFile()
    {
        $theme5 = \Theme::find('theme5');
        $this->assertEquals('config-views', $theme5->viewsPath);
        $this->assertEquals('config-assets', $theme5->assetPath);
    }

    public function testThemeDefinedWithNoConfiguration()
    {
        $theme = \Theme::find('no-configuration');
        $this->assertEquals('no-configuration', $theme->viewsPath);
        $this->assertEquals('no-configuration', $theme->assetPath);
    }

    public function testHierarchy()
    {
        $theme6 = \Theme::find('theme6');
        $this->assertEquals('theme5', $theme6->getParent()->name);
    }

    public function testHierarchyUndeclaredParent()
    {
        $theme8 = \Theme::find('theme8');
        $this->assertEquals('theme9', $theme8->getParent()->name);
    }

    // public function testThemeDefinedInThemesPhpConfigFileCanBeOveridden(){
    // 	$theme5 = \Theme::add(new \Igaster\LaravelTheme\Theme('theme5','overide-assets'));
    // 	$this->assertEquals('overide-assets', $theme5->assetPath);
    // 	$this->assertEquals('theme5', $theme5->viewsPath);
    // }

    public function testSettingsAreOveriddenFromThemesPhpConfigFile()
    {
        $theme = \Theme::find('theme3');
        $this->assertEquals($theme->getSetting('key1'), 'value1');
        $this->assertEquals($theme->getSetting('key2'), 'value changed');
    }

    public function testJsonParentOveriddenFromThemesPhpConfigFile()
    {
        $theme = \Theme::find('theme3');
        $this->assertEquals('theme-parent', $theme->getParent()->name);
    }
}
