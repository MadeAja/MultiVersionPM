<?php

namespace Bavfalcon9\MultiVersion\player;

use pocketmine\player\Player;
use Bavfalcon9\MultiVersion\utils\ProtocolVersion;

class VersionedPlayer {
	private int $protocol;
	private Player $player;

	public function __construct(Player $player, $version) {
		$this->protocol = $version;
		$this->player = $player;
	}

	public function getProtocol(): int {
		return $this->protocol;
	}

	public function getPlayer(): Player {
	    return $this->player;
    }
}