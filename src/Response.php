<?php declare(strict_types = 1);

namespace FuseWire;

class Response implements \JsonSerializable {
    /** @var ComponentResponse[] */
    public $components;
    /** @var TemplateResponse[] */
    public $templates;

    public function jsonSerialize() {
        return [
            'fusewire-type' => 'response',
            'components' => array_map(function (ComponentResponse $component) { return $component->jsonSerialize(); }, $this->components),
            'templates' => array_map(function (TemplateResponse $template) { return $template->jsonSerialize(); }, $this->templates)
        ];
    }
}
