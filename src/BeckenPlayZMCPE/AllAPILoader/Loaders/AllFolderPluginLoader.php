<?php

namespace BeckenPlayZMCPE\AllAPILoader\Loaders;

use FolderPluginLoader\FolderPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;

class AllFolderPluginLoader extends FolderPluginLoader {

    private $server;

    public function __construct(Server $server) {
        parent::__construct($server);
        $this->server = $server;
    }

    public function getPluginDescription($file) {
        if (is_dir($file) and file_exists($file . "/plugin.yml")) {
            $yaml = @file_get_contents($file . "/plugin.yml");
            if ($yaml != "") {
                $description = new PluginDescription($yaml);
                if (!$this->server->getPluginManager()->getPlugin($description->getName()) instanceof Plugin and !in_array($this->server->getApiVersion(), $description->getCompatibleApis())) {
                    $api = (new \ReflectionClass("pocketmine\plugin\PluginDescription"))->getProperty("api");
                    $api->setAccessible(true);
                    $api->setValue($description, [$this->server->getApiVersion()]);
                    return $description;
                }
            }
        }

        return null;
    }

}
