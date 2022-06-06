<?php
namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use App\Helpers\HttpClient;

class CrudTest extends TestCase {
    private $httpClient;
    private $queryBuilder;
    public function setUp() :void {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->Connect());
        $this->httpClient = new HttpClient();
        parent::setUp();
    }
    public function testItCanCreateDataWithApi(){
        $data = [
            "json" => [
                "name" => "API",
                "user" => "Ahmad",
                "email" => "api@gmail.com",
                "link" => "api.com"
            ]
        ];
        $response = $this->httpClient->post("index.php" , $data);
        echo $response->getBudy();
        $this->assertEquals(200, $response->getStatusCode());
        $bug = $this->queryBuilder
            ->table("bugs")
            ->where("name" , "API")
            ->where("user" , "Ahmad")
            ->first();
        $this->assertNotNull($bug);
        return $bug;
    }
    /**
     * @depends ItCanCreateDataWithApi
     */
    public function ItCanUpdateDataWithApi($bug){
        $data = [
            "json" => [
                "id" => $bug->id,
                "name" => "Api For Update"
            ]
        ];
        $response = $this->httpClient->put("index.php", $data);
        $this->assertEquals(200, $response->getStatusCode());
        $bug = $this->queryBuilder
            ->table("bugs")
            ->find($bug->id);
        $this->assertNotNull($bug);
        $this->assertEquals("Api For Update", $bug->name);

    }
    /**
     * @depends testItCanCreateDataWithApi
     */
    public function ItCanFetchDataWithApi($bug){
        $response = $this->httpClient->get("index.php",[
            "json" => [
                "id" => $bug->id
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey("id", json_decode($response->getBody()), true);

    }
    /**
     * @depends testItCanCreateDataWithApi
     */
    public function ItCanDeleteDataWithApi($bug){
        $response = $this->httpClient->delete("index.php",[
            "json" => [
                "id" => $bug->id
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode());
        $bug = $this->queryBuilder
            ->table("bugs")
            ->find($bug->id);
        $this->assertNull($bug);
    }
    public function tearDown() :void {
        $this->httpClient = null;
        parent::tearDown();
    }
    private function getConfig() {
        return Config::get("database","pdo_testing");
    }
}