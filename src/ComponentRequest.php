<?php declare(strict_types = 1);

namespace FuseWire;

class ComponentRequest {
    const RENDER_MODE_CSR = 'CSR';
    const RENDER_MODE_SERVER = 'SERVER';
    const RENDER_MODE_SERVER_WAIT = 'SERVER_WAIT';
    const RENDER_MODE_SSR = 'SSR';

    public static function load(\stdClass $request, Config $config): self {
        if (!isset($request->{'fusewire-type'}) || $request->{'fusewire-type'} !== 'component') {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.fusewire-type": must be "component"');
        }
        $modes = [static::RENDER_MODE_CSR, static::RENDER_MODE_SERVER, static::RENDER_MODE_SERVER_WAIT, static::RENDER_MODE_SSR];
        if (!isset($request->mode) || !is_string($request->mode) || !in_array($request->mode, $modes)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.mode": choose one of [' . implode(', ', $modes) . ']');
        }
        $componentClass = $config->getClassFromComponent(isset($request->component) ? (string) $request->component : '');
        if (!$componentClass || !class_exists($componentClass) || !is_subclass_of($componentClass, Component::class)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.component": verify its a valid class name');
        }
        if (isset($request->id) && !is_string($request->id)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.id": only strings are supported');
        }
        if (isset($request->version) && !is_string($request->version)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.version": : only strings are supported');
        }
        if (isset($request->vars) && !($request->vars instanceof \stdClass)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.vars": : only objects are supported');
        }
        $instance = new static();
        $instance->mode = $request->mode;
        $instance->id = $request->id ?? '';
        $instance->componentClass = $componentClass;
        $instance->version = $request->version ?? '';
        $instance->vars = $request->vars ?? new \stdClass();
        return $instance;
    }

    /** @var String Either SERVER_SIDE_RENDER or CLIENT_SIDE_RENDER */
    private $mode;
    /** @var String Component ID (optional) */
    private $id;
    /** @var String Component class to return */
    private $componentClass;
    /** @var String Version (optional) */
    private $version;
    /** @var \stdClass server component variables the client sent (optional)  */
    private $vars;

    protected function __construct() {
        /** Use load() to construct */
    }

    public function getMode(): string {
        return $this->mode;
    }

    public function getComponentClass(): string {
        return $this->componentClass;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getVersion(): string {
        return $this->version;
    }

    public function getVars(): \stdClass {
        return $this->vars;
    }
}
