<?php declare(strict_types = 1);

namespace FuseWire\Demo\Dashlet;

use FuseWire\Component;

class ServerTime extends Component {
    public $serverDateString;

    public function run(): void {
        $this->serverDateString = (new \DateTime())->format(\DateTime::RFC850);
        parent::run();
    }
}
