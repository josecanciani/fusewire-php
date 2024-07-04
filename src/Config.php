<?php declare(strict_types = 1);

namespace FuseWire;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Config {
    private $templateTags = ['((', '))'];
    private $fuseWireBaseUrl;
    private $debug;
    private $request;

    public function __construct(HttpRequest $request = null, bool $debug = false) {
        $this->request = $request ?: HttpRequest::createFromGlobals();
        // assume it's in the same dir
        $this->fuseWireBaseUrl = pathinfo($this->request->getUriForPath(''), PATHINFO_DIRNAME) . '/fusewire';
        $this->debug = $debug;
    }

    public function getDebug(): bool {
        return $this->debug;
    }

    public function getClassFromComponent(string $name): string {
        return implode('\\', array_filter(array_map(function (string $part) { return preg_replace('/\W+/','', $part); }, explode('_', $name))));
    }

    public function getComponentFromClass(string $class): string {
        return str_replace('\\', '_', $class);
    }

    public function getComponentId(string $componentName, string $componentId): string {
        return $componentName . ($componentId ? '_' . $componentId : '');
    }

    public function getRequest(): HttpRequest {
        return $this->request;
    }

    public function setTemplateTags(string $open, string $close) {
        $this->templateTags = [$open, $close];
    }

    public function setFuseWireBaseUrl(string $fuseWireBaseUrl): void {
        if (substr($fuseWireBaseUrl, 0, 4) === 'http') {
            // full URI, do nothing
            $this->fuseWireBaseUrl = $fuseWireBaseUrl;
        } elseif (substr($fuseWireBaseUrl, 0, 1) === '/') {
            // absolute path, complete schema and host
            $this->fuseWireBaseUrl = $this->request->getSchemeAndHttpHost() . $fuseWireBaseUrl;
        } else {
            // relative, assume current directory
            $this->fuseWireBaseUrl = pathinfo($this->request->getUriForPath(''), PATHINFO_DIRNAME) . '/' . $fuseWireBaseUrl;
        }
    }
}
