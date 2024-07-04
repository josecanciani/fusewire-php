<?php declare(strict_types = 1);

namespace FuseWire\Demo;

use FuseWire\Component;

class Index extends Component {
    public $counter = 0;
    public $serverDateString;

    public function run(): void {
        $this->serverDateString = new Dashlet\ServerTime($this->config);
        parent::run();
        if ($this->counter > 0) {
            sleep(1);
        }
    }
}
