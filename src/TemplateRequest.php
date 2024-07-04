<?php declare(strict_types = 1);

namespace FuseWire;

class TemplateRequest {
    public static function load(\stdClass $request, Config $config): self {
        $component = $config->getClassFromComponent(isset($request->component) ? (string) $request->component : '');
        if (!$component || !class_exists($component) || !is_subclass_of($component, Component::class)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.component": verify its a valid class name');
        }
        if (isset($request->version) && !is_string($request->version)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components.version": only strings are supported');
        }
        $instance = new static();
        $instance->component = (string) $request->component;
        $instance->version = isset($request->version) ? trim((string) $request->version) : '';
        return $instance;
    }

    /** @var String */
    private $component;
    /** @var String */
    private $version;

    protected function __construct() {
        /** Use load() to construct */
    }

    public function getComponent(): string {
        return $this->component;
    }

    public function getVersion(): string {
        return $this->version;
    }
}
