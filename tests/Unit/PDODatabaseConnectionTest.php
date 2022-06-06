<?php
namespace Tests\Unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Exceptions\DatabaseConnectionException;
use App\Exceptions\ConfigNotValidException;
use App\Helpers\Config;
use PDO;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase {
    public function testPDODatabaseConnectionImplementsDatabaseConnectionInterface() {
        $config = $this->getConfig();
        $pdoConnection = new PDODatabaseConnection($config);
        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
    }
    public function testConnectMethodShouldReturnValidInstance() {
        $config = $this->getConfig();
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoHandler = $pdoConnection->Connect();
        $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);
        return $pdoHandler;
    }

    /**
     * @depends testConnectMethodShouldReturnValidInstance
     */

    public function testConnectMethodShouldBeConnectToDatabase($pdoHandler){
        $this->assertInstanceOf(PDO::class, $pdoHandler->getConnection());
    }
    public function testItThrowsExceptionIfConfigIsInvalid() {
        $this->expectException(DatabaseConnectionException::class);
        $config = $this->getConfig();
        $config["database"] = "dummy";
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoConnection->Connect();
    }
    public function testReceivedConfigHaveRequiredKey(){
        $this->expectException(ConfigNotValidException::class);
        $config = $this->getConfig();
        unset($config["db_user"]);
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoConnection->Connect();
    }
    private function getConfig() {
        return Config::get("database","pdo_testing");
    }
}
