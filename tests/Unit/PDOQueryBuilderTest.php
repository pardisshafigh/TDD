<?php
namespace Tests\Unit;
use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
class PDOQueryBuilderTest extends TestCase{
    private $queryBuilder;
    public function setUp() :void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->Connect());
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }
    public function testItCanCreateData(){
        $result = $this->insertIntoDb();
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }
    public function testItCanUpdateData(){
        $this->insertIntoDb();
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("user", "Pari Shahi")
            ->update(["email"=>"Pari@gmail.com","name"=>"First After Update"]);
        return $this->assertEquals(1, $result);
    }
    public function testItCanUpdateWithMultipleWhere(){
        $this->insertIntoDb();
        $this->insertIntoDb(["user"=>"faezeh ahmadi"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("user", "Pari Shahi")
            ->where("link","http://link.com")
            ->update(["name" =>"After Multiple Where"]);
        $this->assertEquals(1, $result);
    }
    public function testItCanDeleteRecord(){
        $this->insertIntoDb();
        $this->insertIntoDb();
        $this->insertIntoDb();
        $this->insertIntoDb();
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("user","Pari Shahi")
            ->delete();
        return $this->assertEquals(4, $result);
    }
    public function testItCanFetchData(){
        $this->multipleInsertIntoDb(10);
        $this->multipleInsertIntoDb(10, ["user"=>"Maryam Ghaderi"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("user", "Maryam Ghaderi")
            ->get();
        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }
    public function testItCanFetchSpecificColumns(){
        $this->multipleInsertIntoDb(10);
        $this->multipleInsertIntoDb(10, ["name" => "New"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("name", "New")
            ->get(["name","user"]);
        $this->assertIsArray($result);
        $this->assertObjectHasAttribute("name", $result[0]);
        $this->assertObjectHasAttribute("user", $result[0]);
        $result = json_decode(json_encode($result[0]), true);
        $this->assertEquals(["name","user"], array_keys($result));
    }
    public function testItCanGetFirstRow(){
        $this->multipleInsertIntoDb(10, ["name" => "First Row"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("name", "First Row")
            ->first();
        $this->assertIsObject($result);
        $this->assertObjectHasAttribute("name", $result);
        $this->assertObjectHasAttribute("user", $result);
        $this->assertObjectHasAttribute("id", $result);
        $this->assertObjectHasAttribute("email", $result);
        $this->assertObjectHasAttribute("link", $result);
    }
    public function testItCanFindWhithId(){
        $this->insertIntoDb();
        $id = $this->insertIntoDb(["name" => "For Find"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->find($id);
        $this->assertIsObject($result);
        $this->assertEquals("For Find", $result->name);
    }
    public function testItCanFindBy(){
        $this->insertIntoDb();
        $id = $this->insertIntoDb(["name" => "For Find By"]);
        $result = $this->queryBuilder
            ->table("bugs")
            ->findBy("name", "For Find By");
        $this->assertIsObject($result);
        $this->assertEquals($id, $result->id);  
    }
    public function testItReturnsEmptyArrayWhenRecordNotFound(){
        $this->multipleInsertIntoDb(4);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("name", "Dummy")
            ->get();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
    public function testItReturnsNullWhenFirstRecordNotFound(){
        $this->multipleInsertIntoDb(4);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("name", "Dummy")
            ->first();
        $this->assertNull($result);
    }
    public function testItReturnsZeroWhenRecordNotFoundForUpdate(){
        $this->multipleInsertIntoDb(4);
        $result = $this->queryBuilder
            ->table("bugs")
            ->where("user","Dummy")
            ->update(["name" => "Test"]);
        $this->assertEquals(0, $result);
    }
    private function getConfig() {
        return Config::get("database","pdo_testing");
    }
    private function insertIntoDb($options = []){
        $data = array_merge([
            "name" => "First Bug Report",
            "link" => "http://link.com",
            "user" => "Pari Shahi",
            "email"=> "PariShahi@gmail.com"
        ], $options);
        return $this->queryBuilder->table("bugs")->create($data);
    }
    private function multipleInsertIntoDb($count, $options = []){
        for ($i=1; $i <=$count; $i++) { 
            $this->insertIntoDb($options);
        }
    }
    public function tearDown() :void
    {
        // $this->queryBuilder->truncateAllTable();
        $this->queryBuilder->rollback();
        parent::tearDown();
    }
}
