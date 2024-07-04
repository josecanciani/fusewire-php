<?php declare(strict_types = 1);

namespace FuseWire;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Reactor {
    private $config;
    private $httpRequest;

    public function __construct(Config $config, HttpRequest $httpRequest = null) {
        $this->config = $config;
        $this->httpRequest = $httpRequest ?: HttpRequest::createFromGlobals();
    }

    public function run(): void {
        // TODO: use Symfony Http Foundation classes for header too
        header("Content-Type: application/json");
        try {
            $request = Request::load($this->httpRequest, $this->config);
            echo json_encode($this->createResponse($request));
        } catch (\Throwable $e) {
            $showAllErrors = $this->config->getDebug() || $e instanceof Exception\FuseWireException;
            echo json_encode([
                'error' => $showAllErrors ? $e->getMessage() : 'Internal server error'
            ]);
            throw $e;
        }
    }

    private function createResponse(Request $request): Response {
        $componentResponses = [];
        foreach ($request->getComponents() as $componentRequest) {
            $component = $this->createComponent($componentRequest);
            $component->run();
            $componentResponses[] = ComponentResponse::load($this->config, $component, $componentRequest);
        }
        $templateResponses = [];
        foreach ($request->getTemplates() as $templateRequest) {
            $templateResponses[] = TemplateResponse::load($templateRequest->getComponent(), $this->config);
        }
        $response = new Response();
        $response->components = $componentResponses;
        $response->templates = $templateResponses;
        return $response;
    }

    private function createComponent(ComponentRequest $request): Component {
        $class = $request->getComponentClass();
        $component = new $class($this->config, $request->getId());
        $vars = $request->getVars();
        foreach ($component->getFuseWireVars() as $name) {
            // TODO: should we fail here? Should we allow for an upgrade method? Support component versioning?
            if (property_exists($vars, $name)) {
                // TODO: offer a custom json encoder/decoder?
                $component->$name = $vars->$name;
            }
        }
        return $component;
    }
}
