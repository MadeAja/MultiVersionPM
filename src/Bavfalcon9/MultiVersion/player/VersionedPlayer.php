<?php

namespace Bavfalcon9\MultiVersion\player;

use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\player\Player;
use Bavfalcon9\MultiVersion\utils\Queue;
use Bavfalcon9\MultiVersion\utils\ProtocolVersion;

class VersionedPlayer {
    private int $protocol;
    private Player $player;
    private Queue $packetQueue;
    private Queue $ignoreQueue;

    public function __construct(Player $player, $version) {
        $this->protocol = $version;
        $this->player = $player;
        $this->packetQueue = new Queue();
        $this->ignoreQueue = new Queue();
    }

    /**
     * Get the string value of the protocol version.
     * @return string
     */
    public function getVersion(): string {
        return "v0.0.0";
    }

    /**
     * Get the protocol version of the player.
     * @return int
     */
    public function getProtocol(): int {
        return $this->protocol;
    }

    /**
     * Get the player
     * @return Player
     */
    public function getPlayer(): ?Player {
        return $this->player;
    }

    /**
     * Send a DataPacket to a player without MultiVersion checking it.
     * @param ClientboundPacket $packet
     * @param bool $skipAll - Whether or not to send without calling any events.
     */
    public function sendDataPacket(ClientboundPacket $packet, bool $skipAll): void {
        if (!$skipAll) {
            $this->ignoreQueue->enqueue($packet);
            $this->player->getNetworkSession()->sendDataPacket([ $this->player ], [ $packet ]);
        } else {
            $this->player->getNetworkSession()->addToSendBuffer($packet);
        }
    }

    /**
     * Gets the packet send queue.
     * @return Queue
     */
    public function getPacketQueue(): Queue {
        return $this->packetQueue;
    }

    /**
     * Gets the ignored packet queue.
     * @return Queue
     */
    public function getIgnoreQueue(): Queue {
        return $this->ignoreQueue;
    }
}