<?php

declare(strict_types=1);

namespace Bavfalcon9\MultiVersion\protocol;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\handler\LoginPacketHandler;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\player\Player;
use pocketmine\plugin\PluginManager;
use Bavfalcon9\MultiVersion\utils\Messages;
use Bavfalcon9\MultiVersion\player\VersionedPlayer;
use Bavfalcon9\MultiVersion\Loader;

abstract class ProtocolAdapter implements Listener {
    private Loader $plugin;
    private int $id;
    private bool $enabled;

    /** @var VersionedPlayer[] */
    private array $players;

    public function __construct(Loader $plugin, int $pid) {
        $this->plugin = $plugin;
        $this->id = $pid;
        $this->enabled = false;
        $this->players = [];
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * Used to inject data into incoming packets.
     * @param DataPacketReceiveEvent $ev
     */
    abstract public function onIncoming(DataPacketReceiveEvent $ev): void;

    /**
     * Used to inject data into packets.
     * @param VersionedPlayer $player
     * @param ClientboundPacket $packet
     */
    abstract public function onOutgoing(VersionedPlayer $player, ClientboundPacket &$packet): void;

    /**
     * User is connecting to protocol
     * @param NetworkSession $session
     * @param DataPacket $packet
     */
    abstract public function onConnecting(NetworkSession $session, DataPacket &$packet);

    /**
     * Hacks login.
     * @param DataPacketReceiveEvent $ev
     */
    public function handleConnecting(DataPacketReceiveEvent $ev): void {
        $packet = $ev->getPacket();

        if ($packet instanceof LoginPacket) {
            if ($this->id === $packet->protocol) {
                // TODO API method to log this information
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
                $this->onConnecting($ev->getOrigin(), $packet);
                return;
            }
        }
    }

    /**
     * Handles outgoing packets
     * @param DataPacketSendEvent $ev
     */
    public function handleOutgoing(DataPacketSendEvent $ev): void {
        /** @var VersionedPlayer[] $sendQueue */
        $sendQueue = [];
        $targets = $ev->getTargets();

        foreach ($targets as $id => $session) {
            if (($p = $this->getPlayer($session->getPlayer()->getName()))) {
                foreach ($ev->getPackets() as $pk) {
                    $containsPacket = $p->getIgnoreQueue()->contains(function ($packet) use ($pk): bool {
                        return (spl_object_id($pk) === spl_object_id($packet));
                    });
                    if ($containsPacket) {
                        $p->getPlayer()->getNetworkSession()->sendDataPacket($pk);
                        $p->getIgnoreQueue()->dequeue($pk);
                    } else {
                        $p->getPacketQueue()->enqueue($pk);
                    }
                }
                $sendQueue[] = $p;
                unset($targets[$id]);
            }
        }

        if (count($sendQueue) <= 0) return;

        foreach ($sendQueue as $versionedPlayer) {
            $packets = $versionedPlayer->getPacketQueue()->dequeueAll();
            foreach ($packets as &$packet) {
                $this->onOutgoing($versionedPlayer, $packet);
            }
            $versionedPlayer->getPlayer()->getNetworkSession()->getBroadcaster()->broadcastPackets([ $versionedPlayer->getPlayer() ], $packets);
        }
    }

    /**
     * Disconnects all players on this protocol
     *
     * @param string $reason
     * @return void
     */
    public function disconnectPlayers(string $reason): void {
        foreach ($this->players as $versionedPlayer) {
            $versionedPlayer->getPlayer()->close('', $reason);
        }
    }

    /**
     * Disconnects the adapter.
     */
    public function disconnect(): void {
        $message = Messages::get("disconnect.reason.shutdown", $this->id);
        $this->disconnectPlayers($message);
        $this->players = [];
    }

    /**
     * Get a versioned player.
     * @param string $name
     * @return VersionedPlayer|null
     */
    public function getPlayer(string $name): ?VersionedPlayer {
        $match = array_filter($this->players, function ($versionedPlayer) use ($name): bool {
            return $versionedPlayer->getPlayer()->getName() === $name;
        });

        return isset($match[0]) ? $match[0] : null;
    }

    public function getPlayers(): array {
        return $this->players;
    }

    public function getProtocolId(): int {
        return $this->id;
    }

    /**
     * Add a player to the adapter.
     * @param Player $player
     * @return VersionedPlayer
     */
    protected function addPlayer(Player $player): VersionedPlayer {
        return $this->players[] = new VersionedPlayer($player, $this->id);
    }

    /**
     * Removes a player from the adapter.
     * @param string $name
     * @return bool
     */
    protected function removePlayer(string $name): bool {
        foreach ($this->players as $i => $versionedPlayer) {
            if ($versionedPlayer->getPlayer()->getName() === $name) {
                $versionedPlayer->getPlayer()->kick(Messages::get("disconnect.reason.shutdown", $this->id));
                unset($this->players[$i]);
            }
        }
    }
}