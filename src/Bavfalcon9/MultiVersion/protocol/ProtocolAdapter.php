<?php

declare(strict_types=1);

namespace Bavfalcon9\MultiVersion\protocol;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\handler\LoginPacketHandler;
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
     * @param DataPacketSendEvent $ev
     */
    abstract public function onOutgoing(DataPacketSendEvent $ev): void;

    /**
     * User is connecting to protocol
     * @param DataPacket $packet
     */
    abstract public function onConnecting(Player $player, DataPacket &$packet);

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
                $this->onConnecting($ev->getPlayer(), $packet);
                return;
            }
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