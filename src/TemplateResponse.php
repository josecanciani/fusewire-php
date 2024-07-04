<?php declare(strict_types = 1);

namespace FuseWire;

use FuseWire\Exception\MissingComponentFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 *  Represents a basic component code (not an instance of it)
 *  TODO: use Filesystem::readFile (requires new Synfony)
*/
class TemplateResponse implements \JsonSerializable {
    /** @var String */
    private $component;
    /** @var String */
    private $basePath;
    /** @var Filesystem */
    private $io;
    /** @var callable */
    private $readFile;
    private $cachedHtmlCode;
    private $cachedJsCode;
    private $cachedCssCode;
    private $cachedVersion;

    /** TODO: Filesystem does not have readFile() until Symfony 7.1, but it requires PHP 8.2, so temporarily you can DI using $readFile */
    public static function load(string $component, Config $config, Filesystem $io = null, callable $readFile = null): self {
        $reflector = new \ReflectionClass($config->getClassFromComponent($component));
        $instance = new static();
        $instance->component = $component;
        $instance->basePath = pathinfo($reflector->getFileName(), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($reflector->getFileName(), PATHINFO_FILENAME);
        $instance->io = $io ?: new Filesystem();
        $instance->readFile = $readFile ?: function(string $file) { return file_get_contents($file); };
        return $instance;
    }

    protected function __construct() {
        // use load()
    }

    public function getHtmlCode(): string {
        if (!$this->cachedHtmlCode) {
            $path = $this->basePath . '.html';
            if (!$this->io->exists($path)) {
                throw new MissingComponentFile('Cannot find HTML file: ' . $path . '.html');
            }
            $this->cachedHtmlCode = call_user_func($this->readFile, $path);
        }
        return $this->cachedHtmlCode;
    }

    public function getJsCode(): string {
        if (!$this->cachedJsCode) {
            $path = $this->basePath . '.js';
            if ($this->io->exists($path)) {
                $this->cachedJsCode = call_user_func($this->readFile, $path);
            } else {
                $this->cachedJsCode = "import { Component } from './component.js';\n\nexport class " . basename($this->basePath) . " extends Component {}\n";
            }
        }
        return $this->cachedJsCode;
    }

    public function getCssCode(): string {
        if (is_null($this->cachedCssCode)) {
            $path = $this->basePath . '.css';
            if ($this->io->exists($path)) {
                $this->cachedCssCode = call_user_func($this->readFile, $path);
            } else {
                $this->cachedCssCode = '';
            }
        }
        return $this->cachedCssCode;
    }

    public function getVersion(): string {
        if (!$this->cachedVersion) {
            $version = 0;
            foreach (['.html', '.js', '.css'] as $ext) {
                $path = $this->basePath . $ext;
                if ($this->io->exists($path) && ($mtime = filemtime($path)) && $mtime > $version) {
                    $version = $mtime;
                }
            }
            $this->cachedVersion = (string) $mtime;
        }
        return $this->cachedVersion;
    }

    public function jsonSerialize() {
        return [
            'fusewire-type' => 'template',
            'component' => $this->component,
            'jsCode' => $this->getJsCode(),
            'cssCode' => $this->getCssCode(),
            'htmlCode' => $this->getHtmlCode(),
            'version' => $this->getVersion()
        ];
    }
}
