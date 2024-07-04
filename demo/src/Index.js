import { Component } from './component.js';
import { ReactorModes } from './reactor.js';

/**
 * Example to reference via JSDoc
 * @ typedef {import('./counter.js').Counter} Counter
 */

export class Index extends Component {
    increase() {
        this.counter++;
        this.react(ReactorModes.CSR);
    }

    increaseAndPush() {
        this.counter++;
        this.react(ReactorModes.SERVER);
    }

    increaseAndPushAndWait() {
        this.counter++;
        this.react(ReactorModes.SERVER_WAIT);
    }
}
