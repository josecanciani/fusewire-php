<?php declare(strict_types = 1);

namespace FuseWire;

class Component implements \JsonSerializable {
    /** @var Config */
    protected $config;

    /** @var String */
    private $fuseWireName;
    /** @var String */
    private $fuseWireId;

    /**
     * @param String $id Unique identifier for this component
     */
    final function __construct(Config $config, string $id = '') {
        $this->config = $config;
        $this->fuseWireName = $this->config->getComponentFromClass(static::class);
        $this->fuseWireId = $id;
    }

    /** Extend this method and put any logic to initialize your Component here */
    public function run(): void {
        foreach ($this->getFuseWireVars() as $var) {
            if ($this->$var instanceof Component) {
                $this->$var->run();
            }
        }
    }

    final public function getFuseWireVars(): array {
        static $varCache = [];
        if (!isset($varCache[__CLASS__])) {
            $reflection = new \ReflectionClass($this);
            $varCache[__CLASS__] = [];
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $varCache[__CLASS__][] = $property->getName();
            }
        }
        return $varCache[__CLASS__];
    }

    final public function getFuseWireName(): string {
        return $this->fuseWireName;
    }

    final public function getFuseWireId(): string {
        return $this->fuseWireId;
    }

    /** @return \stdClass */
    final public function jsonSerialize() {
        $json = new \stdClass();
        foreach ($this->getFuseWireVars() as $name) {
            $json->$name = $this->$name;
        }
        return $json;
    }
}
