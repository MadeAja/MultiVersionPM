<?php

namespace Bavfalcon9\MultiVersion\player;

use pocketmine\player\Player;
use Bavfalcon9\MultiVersion\utils\ProtocolVersion;

class VersionedPlayer {
	private number $protocol;
	private Player $player;

	public function __construct(Player $player, $version) {
		$this->protocol = $version;
		$this->player = $player;
	}
}