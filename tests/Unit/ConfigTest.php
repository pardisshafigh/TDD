<?php
namespace Tests\Unit;

use App\Exceptions\ConfigFileNotFoundException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {
    public function testGetFileContentsReturnsArray() {
        $config = Config::getFileContents("database");
        $this->assertIsArray($config);
    }
    public function testItThrowsExceptionIfFileNotFound(){
        $this->expectException(ConfigFileNotFoundException::class);
        $config = Config::getFileContents("dummy");
    }
    public function testGetMethodReturnsValidData() {
        $config = Config::get("database","pdo");
        $expectedData = [
            "driver"=>"mysql",
            "host"=>"127.0.0.1",
            "database"=>"tdd",
            "db_user"=>"root",
            "db_password"=>""
        ];
        $this->assertEquals($config, $expectedData);
    }
}