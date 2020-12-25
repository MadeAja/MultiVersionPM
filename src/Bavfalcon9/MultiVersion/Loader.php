<?php

namespace Bavfalcon9\MultiVersion;

use pocketmine\plugin\PluginBase;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use Bavfalcon9\MultiVersion\protocol\ProtocolVersion;

class Loader extends PluginBase {
	public function onEnable(): void {
		if (!in_array(ProtocolInfo::CURRENT_PROTOCOL, ProtocolVersion::SUPPORTED_SERVER)) {
			throw new Exception("The server version is not supported by MultiVersion yet."); // throwing is easier to see
		}
	}
}