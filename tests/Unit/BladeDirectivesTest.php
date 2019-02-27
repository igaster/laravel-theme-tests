<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BladeDirectivesTest extends TestCase
{
    /**
     * Blade Template Engine
     *
     * @var \Jenssegers\Blade\Blade
     */
	private $blade;

	public function setUp(): void
    {
        parent::setUp();
        $this->blade = app('view')->getEngineResolver()->resolve('blade')->getCompiler();
    }

    public function testCss_with_1_parameter()
    {
    	$input = '@css("filename.css")';
    	$output = "<?php Asset::style('filename.css', theme_url('filename.css'));?>";
    	$this->assertBlade($input, $output);
    }

    public function testCss_with_2_parameters()
    {
        $input = '@css("filename.css","name")';
        $output = "<?php Asset::style('name', theme_url('filename.css'));?>";
        $this->assertBlade($input, $output);
    }

    public function testCss_with_3_parameters()
    {
        $input = '@css("filename.css","name","dependencies")';
        $output = "<?php Asset::style('name', theme_url('filename.css'), 'dependencies');?>";
        $this->assertBlade($input, $output);
    }


    public function testJs()
    {
        $input = '@js("filename.css")';
        $output = "<?php Asset::script('filename.css', theme_url('filename.css'));?>";
        $this->assertBlade($input, $output);
    }

    public function testJsIn()
    {
        $input = '@jsIn("filename.css","bb")';
        $output = "<?php Asset::container('filename.css')->script('bb', theme_url('bb'));?>";
        $this->assertBlade($input, $output);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

   /**
    * Assert blade compailes $input to $output
    *
    * @param  string $input
    * @param  string $output
    */
   private function assertBlade($input, $output)
   {
        $compiled = $this->blade->compileString($input);

        $this->assertEquals($output,$compiled);
    }

}
