<?php

namespace DavyCraft648\ItemLifeTime;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemDespawnEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\EventPriority;
use function floor;

final class Main extends \pocketmine\plugin\PluginBase {

    private int $defaultDespawnDelay = 300; // default = 5 minutes
    private array $customWorlds = [];

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->checkConfig();

        $this->getServer()->getPluginManager()->registerEvent(ItemSpawnEvent::class, function(ItemSpawnEvent $event): void {
            $entity = $event->getEntity();
            if (!($entity instanceof ItemEntity)) {
                return;
            }

            $age = $entity->getDespawnDelay();
            $delay = $this->customWorlds[$entity->getWorld()->getFolderName()] ?? $this->defaultDespawnDelay;
            $delay = $delay === -1 ? -1 : $delay * 20;
            if ($age > $delay) {
                $entity->setDespawnDelay($delay);
            }
        }, EventPriority::LOW, $this);
        $this->getServer()->getPluginManager()->registerEvent(ItemDespawnEvent::class, function(ItemDespawnEvent $event): void {
            $entity = $event->getEntity();
            if (!($entity instanceof ItemEntity)) {
                return;
            }

            $delay = $this->customWorlds[$entity->getWorld()->getFolderName()] ?? $this->defaultDespawnDelay;
            if ($delay === -1) {
                $event->cancel();
            }
        }, EventPriority::LOW, $this);
    }

    private function checkConfig(): void {
        $config = $this->getConfig();
        $despawnDelay = $config->get("item-lifetime", 300);
        if (!$this->checkDespawnDelay($despawnDelay)) {
            $this->getLogger()->warning("\"item-lifetime\" must be in range 0 ... " . floor(ItemEntity::MAX_DESPAWN_DELAY / 20) . " or " . ItemEntity::NEVER_DESPAWN . ", got $despawnDelay");
            $this->getLogger()->info("Item lifetime was set to default value (300 seconds)");
        } else {
            $this->defaultDespawnDelay = $despawnDelay;
        }

        foreach ($config->get("worlds", []) as $name => $delay) {
            if ($this->checkDespawnDelay($delay)) {
                $this->customWorlds[$name] = $delay;
                continue;
            }

            $this->getLogger()->info("Item lifetime in \"$name\" world was set to default value ($this->defaultDespawnDelay seconds)");
        }
    }

    public function checkDespawnDelay(int $despawnDelay): bool {
        return !((($despawnDelay * 20) < 0 or ($despawnDelay * 20) > ItemEntity::MAX_DESPAWN_DELAY) and $despawnDelay !== ItemEntity::NEVER_DESPAWN);
    }

}
