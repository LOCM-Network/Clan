<?php

declare(strict_types=1);

namespace phuongaz\openclan\provider;

use Generator;
use phuongaz\easylog\LogLevel;
use phuongaz\openclan\clan\Clan;
use phuongaz\openclan\util\trait\LoggerTrait;
use poggit\libasynql\DataConnector;
use SOFe\AwaitGenerator\Await;

class SQLProvider {
    use LoggerTrait;
    const INIT = "table.init";
    const SELECT = "table.select";
    const SELECT_ALL = "table.select";
    const INSERT = "table.insert";
    const UPDATE = "table.update";
    const DELETE = "table.delete";

    private DataConnector $connector;

    public function __construct(DataConnector $connector) {
        $this->connector = $connector;
        Await::f2c(function () {
            yield from $this->asyncGeneric(self::INIT);
        }, fn() => $this->loadAllClans());
    }

    public function getConnector(): DataConnector {
        return $this->connector;
    }

    public function loadClan(string $name, ?callable $callable = null) :void{
        Await::f2c(function () use ($name, $callable) {
            $rows = yield from $this->asyncSelect(self::SELECT, [
                "name" => $name
            ]);
            if(!empty($rows)){
                $clan = new Clan(
                    name: $rows[0]["name"],
                    tag: $rows[0]["tag"],
                    owner: $rows[0]["owner"],
                    deputy: $rows[0]["deputy"],
                    members: json_decode($rows[0]["members"], true),
                    created: $rows[0]["created"],
                    level: $rows[0]["level"],
                    point: $rows[0]["points"]
                );
                Session::add($clan);
                if ($callable !== null) $callable($clan);
            }
        });
    }

    public function loadAllClans() :void {
        Await::f2c(function () {
            $rows = yield from $this->asyncSelect(self::SELECT_ALL);
            foreach ($rows as $row) {
                $clan = new Clan(
                    name: $row["name"],
                    tag: $row["tag"],
                    owner: $row["owner"],
                    deputy: $row["deputy"],
                    members: json_decode($row["members"], true),
                    created: $row["created"],
                    level: $row["level"],
                    point: $row["points"]
                );
                Session::add($clan);
            }
        });
    }

    public function insertClan(Clan $clan, ?callable $callable = null) :void {
        Await::f2c(function() use ($clan, $callable) {
            yield from $this->asyncInsert(self::INSERT, [
                "name" => $clan->getName(),
                "tag" => $clan->getTag(),
                "owner" => $clan->getOwner(),
                "deputy" => $clan->getDeputy(),
                "members" => json_encode($clan->getMembers()),
                "created" => $clan->getCreatedTime(),
                "level" => $clan->getLevel(),
                "points" => $clan->getPoint()
            ]);
            if ($callable !== null) $callable($clan);
        });
    }

    public function updateClan(Clan $clan, ?callable $callable = null) :void {
        Await::f2c(function () use ($clan, $callable) {
            yield from $this->asyncChange(self::UPDATE, [
                "name" => $clan->getName(),
                "tag" => $clan->getTag(),
                "owner" => $clan->getOwner(),
                "deputy" => $clan->getDeputy(),
                "members" => json_encode($clan->getMembers()),
                "created" => $clan->getCreatedTime(),
                "level" => $clan->getLevel(),
                "points" => $clan->getPoint()
            ]);
            if ($callable !== null) $callable($clan);
        });
    }

    public function deleteClan(string $name, ?callable $callable = null) :void {
        Await::f2c(function () use ($name, $callable) {
            yield from $this->asyncChange(self::DELETE, [
                "name" => $name
            ]);
            if ($callable !== null) $callable($name);
        });
    }

    public function asyncGeneric(string $query, array $args = []): Generator{
        $this->connector->executeGeneric($query, $args, yield, yield Await::REJECT);
        return yield Await::ONCE;
    }

    public function asyncChange(string $query, array $args = []): Generator{
        $this->connector->executeChange($query, $args, yield, yield Await::REJECT);
        return yield Await::ONCE;
    }

    public function asyncInsert(string $query, array $args = []): Generator{
        $this->connector->executeInsert($query, $args, yield, yield Await::REJECT);
        return yield Await::ONCE;
    }

    public function asyncSelect(string $query, array $args = []): Generator{
        $this->connector->executeSelect($query, $args, yield, yield Await::REJECT);
        return yield Await::ONCE;
    }

    public function onError() :callable{
         return function (\Throwable $error) {
            $this->getConnector()->getLogger()->error($error->getMessage());
            self::log($error->getMessage(), LogLevel::ERROR);
        };
    }

}