<?php

declare(strict_types=1);

namespace Bavfalcon9\MultiVersion\protocol;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
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
     * @param VersionedPlayer $player
     * @param DataPacket $packet
     */
    abstract public function onIncoming(VersionedPlayer $player, DataPacket &$packet): void;

    /**
     * Used to inject data into packets.
     * @param VersionedPlayer $player
     * @param DataPacket $packet
     */
    abstract public function onOutgoing(VersionedPlayer $player, DataPacket &$packet): void;

    /**
     * User is connecting to protocol
     * @param Player $session
     * @param DataPacket $packet
     */
    abstract public function onConnecting(Player $session, DataPacket &$packet);

    /**
     * Hacks login and handles batches.
     * @param DataPacketReceiveEvent $ev
     */
    public function handleIncoming(DataPacketReceiveEvent $ev): void {
        $packet = $ev->getPacket();
        $player = $this->getPlayer($ev->getPlayer()->getName());

        if ($packet instanceof LoginPacket) {
            if ($this->id === $packet->protocol) {
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
                $this->onConnecting($ev->getPlayer(), $packet);
                $this->addPlayer($ev->getPlayer(), $this->getProtocolId());
                return;
            }
        }

        if ($packet instanceof BatchPacket) {
            $newPackets = [];
            foreach ($packet->getPackets() as $buf) {
                $pk = PacketPool::getPacket($buf);
                $pk->decode();
                $this->onIncoming($player, $pk);
                $newPackets[] = $pk;
            }

            // rebatch
            $packet = new BatchPacket();

            foreach ($newPackets as $pk) {
                $packet->addPacket($pk);
            }
            return;
        }
    }

    /**
     * Handles outgoing packets
     * @param DataPacketSendEvent $ev
     */
    public function handleOutgoing(DataPacketSendEvent $ev): void {

    }

    public function handleCreatedPlayer(PlayerCreationEvent $ev): void {

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
                $versionedPlayer->getPlayer()->close('', Messages::get("disconnect.reason.shutdown", $this->id));
                unset($this->players[$i]);
            }
        }
    }
}