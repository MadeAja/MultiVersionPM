# MultiVersionPM
Multiversion is a plugin that allows multiple MCPE client's to join a pocketmine server.

## Information
⚠ **Disclaimer:** Player protocol in this plugin is api guarded, meaning all plugins that utilize the PM-API should function normally. However, plugins that rely or are dependent on player packets or a specific protocol may result in conflicts.

**What protocol's are supported?**
- For a list of supported protocol's please visit the [versions table](/resources/Versions.md). <br />

**How do I allow/deny a certain protocol?**
- Go to the plugin config and add the protocol id from the [versions table](/resources/Versions.md) to the `allowed` key. <br />
  For instance, if I would like 1.14+ to join the server, my config may look like:
  ```yaml
  allowed:
    - 390
    - 407
    - 408
    - 418
    - 419
    - 421
  ```
  
 ## API
 The following is guidance for the API.
  
 **How do I get a connected player's client version?**
  - To get a version of a player, you must do the following:
    ```php
    use Bavfalcon9\MultiVersion\API;
    
    $player = API::getPlayer("PlayerNameExact");
    $player->getVersion(); // 1.14.0
    $player->getProtocol(); // 390
    ```
 **How do I send a player a packet from their version?**
   - MultiVersion by default, does it's best to translate all packets sent to the player.
     However if you want to send a player a packet directly without MultiVersion attempting to filter it,
     You need to get the versioned player, and call `sendDataPacket` as you would on a normal `pocketmine\player\Player` class.
     ```php
     use Bavfalcon9\MultiVersion\API;
     class ExplodePacket_v12 extends DataPacket {
        /** ... 1.12 explode packet */
     }
     
     // ... You run an explode command ...
     $player = API::getPlayer("Bavfalcon9");
     if ($player->getProtocol() === ProtocolVersion::v1_12_0) {
         $player->sendDataPacket(new ExplodePacket);
     }
     ```
     **⚠ Disclaimer:** MultiVersion is not responsible for how the client may receive the packets.
     If you choose to do this, you should be sure the packet you are sending is accurate for the version of the client.