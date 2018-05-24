<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Mail\markdownEmail;

class MarkdownEmailsTest extends TestCase
{
    // -----------------------------------------------
    //  Description
    // -----------------------------------------------
    // Laravel publishes mail templates in "views/vendor/mail/..."
    // Themes can overide these templates by creating files inside
    // their folder: "THEME_FOLDER/vendor/mail/..."

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
    }

    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testViewPaths()
    {
        $viewFinder = $this->app->make('view.finder');

        \Theme::set('theme2');
        \Mail::to("dummy@email.com")->send(new markdownEmail());
        
        $paths = $this->trimPaths($viewFinder->addThemeNamespacePaths('mail'));
        
        $this->assertEquals([
            "resources/themes/theme2/vendor/mail/markdown",
            "resources/themes/theme1/vendor/mail/markdown",
            "resources/views/vendor/mail/markdown",
            "vendor/laravel/framework/src/Illuminate/Mail/resources/views/markdown",
        ], $paths);
    }

    public function testViews()
    {
        $viewFinder = $this->app->make('view.finder');
        
        // Theme1 doesn't override mail templates
        \Theme::set('theme1');
        $this->captureViews();
        \Mail::to("dummy@email.com")->send(new markdownEmail());

        $this->assertViewRendered("views/vendor/mail/markdown/button");
        $this->assertViewRendered("views/vendor/mail/html/button");

        // Theme2 overrided mail templates
        \Theme::set('theme2');
        $this->captureViews();
        \Mail::to("dummy@email.com")->send(new markdownEmail());

        $this->assertViewRendered("themes/theme2/vendor/mail/markdown/button");
        $this->assertViewRendered("themes/theme2/vendor/mail/html/button");
    }
}
