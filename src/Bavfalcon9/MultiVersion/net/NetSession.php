<?php

namespace Bavfalcon9\MultiVersion\net;

use pocketmine\network\mcpe\compression\Compressor;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\PacketBroadcaster;
use pocketmine\network\mcpe\PacketSender;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\NetworkSessionManager;
use pocketmine\Server;

class NetSession extends NetworkSession {
    public function __construct(Server $server, NetworkSessionManager $manager, PacketPool $packetPool, PacketSender $sender, PacketBroadcaster $broadcaster, Compressor $compressor, string $ip, int $port) {
        parent::__construct($server, $manager, $packetPool, $sender, $broadcaster, $compressor, $ip, $port);
        $this->setHandler();
    }
}