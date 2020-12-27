<?php

namespace Bavfalcon9\MultiVersion\utils;

class Queue {
    private array $enqueued;

    public function __construct() {
        $this->enqueued = [];
    }

    public function enqueue($val): int {
        $this->enqueued[] = $val;
        return count($this->enqueued) - 1;
    }

    public function dequeue($idOrPacket = -1) {
        if (is_object($idOrPacket)) {
            foreach ($this->enqueued as $index => $item) {
                if (spl_object_id($item) === spl_object_id($item)) {
                    $cache = $item;
                    unset($this->enqueued[$index]);
                    return $cache;
                }
            }
            return false;
        }
        if (is_numeric($idOrPacket)) {
            if ($idOrPacket === -1) {
                if (count($this->enqueued) > 1) {
                    $cache = $this->enqueued[count($this->enqueued) - 1];
                    unset($this->enqueued[count($this->enqueued) - 1]);
                    return $cache;
                }
            } else if (isset($this->enqueued[$idOrPacket])) {
                $cache = $this->enqueued[$idOrPacket];
                unset($this->enqueued[$idOrPacket]);
                return $cache;
            } else {
                return false;
            }
        }
    }

    public function dequeueAll(): array {
        $cache = $this->enqueued;
        $this->enqueued = [];
        return $cache;
    }

    public function contains($val): bool {
        return count(array_filter($this->enqueued, function ($v) use ($val): bool {
            if (is_callable($val)) {
                return $val($v);
            } else {
                return $v === $val;
            }
        })) >= 1;
    }

    public function first($match = null) {
        if (!$match) {
            return isset($this->enqueued[0]) ? $this->enqueued[0] : null;
        } else {
            foreach ($this->enqueued as $index => $item) {
                if (is_callable($match)) {
                    if ($match($item, $index)) {
                        return $item;
                    } else {
                        if ($item === $match) {
                            return $item;
                        }
                    }
                }
            }
        }
    }
}