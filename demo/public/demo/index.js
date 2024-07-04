import { Component } from './component.js';
import { ReactorModes } from './reactor.js';

export class Index extends Component {
    increase() {
        this.counter++;
        this.react(ReactorModes.CSR_ONLY);
    }
}
