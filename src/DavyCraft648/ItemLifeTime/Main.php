<?php

namespace DavyCraft648\ItemLifeTime;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemDespawnEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\EventPriority;
use pocketmine\scheduler\ClosureTask;
use function explode;
use function floor;
use function gmdate;
use function str_replace;

final class Main extends \pocketmine\plugin\PluginBase {

    private int $defaultDespawnDelay = 60 * 5; // default = 5 minutes
    private array $customWorlds = [];
    private bool $displayTime = false;
    private string $displayText = "";
    /** @var ItemEntity[] */
    private array $itemToUpdate = [];

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->checkConfig();

        $plmanager = $this->getServer()->getPluginManager();
        $plmanager->registerEvent(ItemSpawnEvent::class, function(ItemSpawnEvent $event): void {
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
            if ($this->displayTime && $delay !== -1) {
                $this->itemToUpdate[$entity->getId()] = $entity;
            }
        }, EventPriority::LOW, $this);
        $plmanager->registerEvent(ItemDespawnEvent::class, function(ItemDespawnEvent $event): void {
            $entity = $event->getEntity();
            if (!($entity instanceof ItemEntity)) {
                return;
            }

            $delay = $this->customWorlds[$entity->getWorld()->getFolderName()] ?? $this->defaultDespawnDelay;
            if ($delay === -1) {
                $event->cancel();
            }
        }, EventPriority::LOW, $this);

        if ($this->displayTime) {
            $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function(): void {
                foreach ($this->itemToUpdate as $id => $item) {
                    if ($item->isClosed()) {
                        unset($this->itemToUpdate[$id]);
                        continue;
                    }
                    $despawnDelay = $item->getDespawnDelay() / 20;
                    if ($despawnDelay < 0) {
                        continue;
                    }
                    [$s, $m] = explode(":", gmdate("s:i", (int) $despawnDelay));
                    try {
                        $item->setNameTag(str_replace(
                            ["{SECOND}", "{MINUTE}", "{TIME}"],
                            [$s, $m, match (true) {
                                $m > 1 => "$m minutes",
                                $m > 0 => "$m minute",
                                $s > 1 => "$s seconds",
                                $s > 0 => "$s second"
                            }],
                            $this->displayText
                        ));
                    } catch (\UnhandledMatchError) {
                    }
                    $item->setNameTagVisible(true);
                    $item->setNameTagAlwaysVisible(true);
                }
            }), 20, 10);
        }
    }

    private function checkConfig(): void {
        $config = $this->getConfig();
        $config->setDefaults([
            "item-lifetime" => 300,
            "worlds" => [],
            "display-time" => [
                "enabled" => false,
                "text" => "{MINUTE}:{SECOND}"
            ]
        ]);
        $despawnDelay = $config->get("item-lifetime", 300);
        if (!$this->isValidDespawnDelay($despawnDelay)) {
            $this->getLogger()->warning("\"item-lifetime\" must be in range 0 ... " . floor(ItemEntity::MAX_DESPAWN_DELAY / 20) . " or " . ItemEntity::NEVER_DESPAWN . ", got $despawnDelay");
            $this->getLogger()->info("Item lifetime was set to default value (300 seconds)");
        } else {
            $this->defaultDespawnDelay = $despawnDelay;
        }

        foreach ($config->get("worlds", []) as $name => $delay) {
            if ($this->isValidDespawnDelay($delay)) {
                $this->customWorlds[$name] = $delay;
                continue;
            }

            $this->getLogger()->info("Item lifetime in \"$name\" world was set to default value ($this->defaultDespawnDelay seconds)");
        }

        $this->displayTime = $config->getNested("display-time.enabled", false);
        if (empty($text = $this->displayTime ? $config->getNested("display-time.text", "") : "")) {
            $this->displayTime = false;
        }
        $this->displayText = $text;
        if ($config->hasChanged()) {
            $config->save();
        }
    }

    /**
     * Check for invalid despawn delay
     * @param int $despawnDelay Despawn delay in second
     * @return bool
     */
    public function isValidDespawnDelay(int $despawnDelay): bool {
        return !((($despawnDelay * 20) < 0 || ($despawnDelay * 20) > ItemEntity::MAX_DESPAWN_DELAY) && $despawnDelay !== ItemEntity::NEVER_DESPAWN);
    }

}
