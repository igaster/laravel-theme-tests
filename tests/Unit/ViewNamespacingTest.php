<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewNamespacingTest extends TestCase
{

    // -----------------------------------------------
    //  Description
    // -----------------------------------------------
    // As a general rule all namespaced paths are also registered inside theme folder,
    // except from those that are located inside installed packages. Examples:
    // A. Custom namespaces:
    //     Can be defined with view()->addNamespace('namespace', [base_path('NAMESPACE_PATH')]);
    //     A namespaced view is rendered with view('namespace::viewName')
    //     Can be overidden inside theme folder: "THEME_FOLDER/NAMESPACE_PATH/..."
    // B. Package View Namespaces:
    //     Laravel defines two locations: 1) inside package folder and 2) inside 'views/vendor/NAMESPACE_PATH' folder
    //     Theme will register "THEME_FOLDER/vendor/NAMESPACE_PATH/..."

    // -----------------------------------------------
    //  Setup: Run before each Test
    // -----------------------------------------------

    public $theme1;
    public $theme2;
    public $viewFinder;

    public function setUp()
    {
        parent::setUp();
        $this->theme1 = new \Igaster\LaravelTheme\Theme('theme1');
        $this->theme2 = new \Igaster\LaravelTheme\Theme('theme2', null, null, $this->theme1);

        $this->viewFinder = $this->app->make('view.finder');

        view()->addNamespace('namespace', [
            base_path('resources/namespace'),
        ]);

        view()->addNamespace('dummy-package', [
            base_path('resources/views/vendor/package'),
            base_path('vendor/username/package_name'),
        ]);
    }

    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testCustomNamespace()
    {
        \Theme::set('theme1');

        $paths = $this->trimPaths($this->viewFinder->addThemeNamespacePaths('namespace'));

        $this->assertEquals($paths, [
            "resources/themes/theme1/resources/namespace",
            "resources/namespace",
        ]);

        // View located in original Namespace path
        $this->captureViews('namespace::view1');
        $this->assertViewRendered('namespace/view1');

        // View overidden in theme path
        $this->captureViews('namespace::view2');
        $this->assertViewRendered('themes/theme1/resources/namespace/view2');
    }

    public function testCustomNamespaceThemeInheritance()
    {
        \Theme::set('theme2');

        $paths = $this->trimPaths($this->viewFinder->addThemeNamespacePaths('namespace'));

        $this->assertEquals($paths, [
            "resources/themes/theme2/resources/namespace",
            "resources/themes/theme1/resources/namespace",
            "resources/namespace",
        ]);

        // View located in original Namespace path
        $this->captureViews('namespace::view1');
        $this->assertViewRendered('namespace/view1');

        // View overidden in parent theme
        $this->captureViews('namespace::view2');
        $this->assertViewRendered('themes/theme1/resources/namespace/view2');

        // View overidden in current theme & includes previous views
        $this->captureViews('namespace::view3');
        
        $this->assertViewsRendered([
            'themes/theme2/resources/namespace/view3',
            'namespace/view1',
            'themes/theme1/resources/namespace/view2',
        ]);

        $this->assertViewsRendered([
            'themes/theme2/resources/namespace/view3',
            'namespace/view1',
            'themes/theme1/resources/namespace/view2',
        ]);
    }

    public function testPackageNamespaceWithRemapingPaths()
    {
        \Theme::set('theme2');

        $paths = $this->trimPaths($this->viewFinder->addThemeNamespacePaths('dummy-package'));

        $this->assertEquals($paths, [
            "resources/themes/theme2/vendor/package",
            "resources/themes/theme1/vendor/package",
            "resources/views/vendor/package",
            "vendor/username/package_name",
        ]);
    }
}
