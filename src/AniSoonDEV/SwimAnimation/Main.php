<?php

namespace AniSoonDEV\SwimAnimation;


use pocketmine\block\StillWater;
use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * Class Main
 * @package Swimming
 */
class Main extends PluginBase implements Listener {

	public const STOP_ACTION = 0;

	public const START_ACTION = 1;

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param PlayerMoveEvent $event
	 */
	public function onMove(PlayerMoveEvent $event): void{
		$player = $event->getPlayer();

		if($this->inWater($player)){
			if($player->isSprinting()){

				$this->send($player, self::START_ACTION);
				$player->exhaust(0.015, PlayerExhaustEvent::CAUSE_SWIMMING);
			} else {
				$this->send($player, self::START_ACTION);
				$player->exhaust(0.015, PlayerExhaustEvent::CAUSE_SWIMMING);
			}
		} else {
			$this->send($player, self::STOP_ACTION);
		}
	}

	/**
	 * @param Player $player
	 * @param int $action
	 */
	public function send(Player $player, int $action){

		switch($action){

			case self::STOP_ACTION:

				$pk = new PlayerActionPacket();
				$pk->entityRuntimeId = Entity::$entityCount++;
				$pk->x = $player->getFloorX();
				$pk->y = $player->getFloorY();
				$pk->z = $player->getFloorZ();
				$pk->face = $player->getDirection();
				$pk->action = $pk::ACTION_STOP_SWIMMING;

				$player->setGenericFlag($player::DATA_FLAG_SWIMMING, false);
				break;

			case self::START_ACTION:
				$pk = new PlayerActionPacket();
				$pk->entityRuntimeId = Entity::$entityCount++;
				$pk->x = $player->getFloorX();
				$pk->y = $player->getFloorY();
				$pk->z = $player->getFloorZ();
				$pk->face = $player->getDirection();
				$pk->action = $pk::ACTION_START_SWIMMING;

				$player->setGenericFlag($player::DATA_FLAG_SWIMMING, true);
				break;
		}

		$this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);

	}

	/**
	 * @param Player $player
	 * @return bool
	 */
	public function inWater(Player $player): bool{
		 return $player->getLevel()->getBlockAt($player->getFloorX(), $player->getFloorY() - 1, $player->getFloorZ()) instanceof Water || $player->getLevel()->getBlockAt($player->getFloorX(), $player->getFloorY() - 1, $player->getFloorZ()) instanceof StillWater;

	}
}
