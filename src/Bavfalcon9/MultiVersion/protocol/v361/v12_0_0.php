<?php

namespace Bavfalcon9\MultiVersion\protocol\v361;

use Bavfalcon9\MultiVersion\Loader;
use Bavfalcon9\MultiVersion\player\VersionedPlayer;
use Bavfalcon9\MultiVersion\protocol\ProtocolAdapter;
use Bavfalcon9\MultiVersion\protocol\ProtocolVersion;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Player;

class v12_0_0 extends ProtocolAdapter {
    public function __construct(Loader $plugin) {
        parent::__construct($plugin, ProtocolVersion::v1_12_0);
    }

    public function onIncoming(VersionedPlayer $player, DataPacket &$packet): void {
        if (!$this->getPlayer($ev->getPlayer()->getName())) return;
    }

    public function onOutgoing(VersionedPlayer $player, DataPacket &$packet): void {
        // TODO: Translate all outgoing packets to proper version.
        // These are for sure 1.12 players.
    }

    public function onConnecting(Player $session, DataPacket &$packet) {
        // TODO: Implement onConnecting() method.
    }
}