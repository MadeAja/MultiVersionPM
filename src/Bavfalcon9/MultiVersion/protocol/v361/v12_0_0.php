<?php

namespace Bavfalcon9\MultiVersion\protocol\v361;

use Bavfalcon9\MultiVersion\Loader;
use Bavfalcon9\MultiVersion\player\VersionedPlayer;
use Bavfalcon9\MultiVersion\protocol\ProtocolAdapter;
use Bavfalcon9\MultiVersion\protocol\ProtocolVersion;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;

class v12_0_0 extends ProtocolAdapter {
    public function __construct(Loader $plugin) {
        parent::__construct($plugin, ProtocolVersion::v1_12_0);
    }

    public function onIncoming(DataPacketReceiveEvent $ev): void {
        if (!$this->getPlayer($ev->getOrigin()->getPlayer()->getName())) return;
    }

    public function onOutgoing(VersionedPlayer $player, ClientboundPacket &$packet): void {
        // TODO: Translate all outgoing packets to proper version.
        // These are for sure 1.12 players.
    }
}