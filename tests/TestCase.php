<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // -----------------------------------------------
    //  Setup: Run before each Test
    // -----------------------------------------------

    public $viewNames = [];
    public function setUp(): void
    {
        parent::setUp();

        // -- Capture View Names
        \Event::listen(
            'composing:*',
            function ($view, $data = []) {
                if ($data) {
                    $view = $data[0]; // For Laravel >= 5.4
                }
                $name = $view->getName();
                $path = $view->getPath();
                $path = substr($path, strlen(base_path('resources'))+1);
                $path = substr($path, 0, -strlen('.blade.php'));
                $this->viewNames[] = $path;
            }
        );
    }


    // -----------------------------------------------
    //  Helpers
    // -----------------------------------------------

    // create a view and capture all blade files that were rendered
    public function captureViews($view=null, $data = [])
    {
        $this->viewNames=[];
        if ($view) {
            view($view, $data)->render();
        }
    }

    // Only the $viewNames have been rendered
    public function assertViewsRendered($viewNames = [])
    {
        return $this->assertEquals($viewNames, $this->viewNames);
    }

    // $viewName has been rendered
    public function assertViewRendered($viewName = '')
    {
        return $this->assertTrue(in_array($viewName, $this->viewNames));
    }

    public function trimPaths($paths)
    {
        return array_map(function ($item) {
            return substr($item, strlen(base_path())+1);
        }, $paths);
    }
}
