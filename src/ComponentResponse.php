<?php declare(strict_types = 1);

namespace FuseWire;

use Symfony\Component\Filesystem\Filesystem;

class ComponentResponse implements \JsonSerializable {
    public static function load(Config $config, Component $component, ComponentRequest $request = null, Filesystem $io = null): self {
        $vars = $component->jsonSerialize();
        foreach ($vars as $key => $value) {
            if ($value instanceof Component) {
                $childRequest = new \stdClass();
                $childRequest->{'fusewire-type'} = 'component';
                $childRequest->mode = $request->getMode();
                $childRequest->component = $value->getFuseWireName();
                $childRequest->id = $value->getFuseWireId();
                $childRequest->version = '';
                $childRequest->vars = new \stdClass();
                $vars->$key = static::load($config, $value, ComponentRequest::load($childRequest, $config), $io);
            }
        }
        return new static(
            $component->getFuseWireName(),
            $component->getFuseWireId(),
            $vars,
            TemplateResponse::load($component->getFuseWireName(), $config, $io),
            /* TODO: SSR */
            ''
        );
    }

    private $component;
    private $template;
    private $id;
    private $serverVars;
    private $innerHTML;

    public function __construct(
        string $component,
        string $id,
        \stdClass $serverVars,
        TemplateResponse $template,
        string $innerHTML = null
    ) {
        $this->component = $component;
        $this->id = $id;
        $this->serverVars = $serverVars;
        $this->template = $template;
        $this->innerHTML = $innerHTML;
    }

    public function jsonSerialize() {
        return [
            'fusewire-type' => 'component',
            'component' => $this->component,
            'id' => $this->id,
            'vars' => $this->serverVars,
            'innerHTML' => $this->innerHTML,
            'version' => $this->template->getVersion()
        ];
    }
}
