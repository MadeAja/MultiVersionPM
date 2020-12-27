<?php

namespace Bavfalcon9\MultiVersion\protocol\v361;

use Bavfalcon9\MultiVersion\Loader;
use Bavfalcon9\MultiVersion\protocol\ProtocolAdapter;
use Bavfalcon9\MultiVersion\protocol\ProtocolVersion;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;

class v12_0_0 extends ProtocolAdapter {
    public function __construct(Loader $plugin) {
        parent::__construct($plugin, ProtocolVersion::v1_12_0);
    }

    public function onIncoming(DataPacketReceiveEvent $ev): void {

    }

    public function onOutgoing(DataPacketSendEvent $ev): void {

    }

    public function onConnecting(Player $player, &$packet): void {
        $this->addPlayer($player, $this->getProtocolId());
    }
}