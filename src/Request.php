<?php declare(strict_types = 1);

namespace FuseWire;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Request {
    public static function load(HttpRequest $request, Config $config): self {
        $instance = new static();
        $instance->components = [];
        $instance->templates = [];
        $components = json_decode($request->request->get('fusewire_components') ?: '[]');
        if (!is_array($components)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_components": it should be an array');
        }
        $templates = json_decode($request->request->get('fusewire_templates') ?: '[]');
        if (!is_array($templates)) {
            throw new Exception\InvalidArgument('Invalid "fusewire_templates": it should be an array');
        }
        foreach ($components as $component) {
            if (!($component instanceof \stdClass)) {
                throw new Exception\InvalidArgument('Invalid component request: "fusewire_components" show be an array of objects');
            }
            $instance->components[] = ComponentRequest::load($component, $config);
        }
        foreach ($templates as $template) {
            if (!($template instanceof \stdClass)) {
                throw new Exception\InvalidArgument('Invalid template request: "fusewire_templates" show be an array of objects');
            }
            $instance->templates[] = TemplateRequest::load($template, $config);
        }
        return $instance;
    }

    /** @var ComponentRequest[] */
    private $components;
    /** @var TemplateRequest[] */
    private $templates;

    protected function __construct() {
        // use load()
    }

    /** @return ComponentRequest[] */
    public function getComponents(): array {
        return $this->components;
    }

    /** @return TemplateRequest[] */
    public function getTemplates(): array {
        return $this->templates;
    }
}
