class OverlayManager {
    constructor() {
        this.overlays = {};
        this.timeouts = {};
    }

    /**
     * @param {Component} component
     * @param {ComponentElement} container
     */
    render(component, container, _force) {
        if (this.overlays[component.fuseWireComponentId]) {
            if (_force) {
                // race condition, a new refresh has been called
                return;
            }
            this.remove(component, container);
            setTimeout(() => this.render(component, container, true), 150);
            return;
        }
        this.overlays[component.fuseWireComponentId] = this._createOverlay(container);
        this.timeouts[component.fuseWireComponentId] = setTimeout(() => this.remove(component, container), 1100);
    }

    remove(component, container) {
        this.overlays[component.fuseWireComponentId].remove();
        clearTimeout(this.timeouts[component.fuseWireComponentId]);
        delete this.overlays[component.fuseWireComponentId];
        delete this.timeouts[component.fuseWireComponentId];
    }

    _createOverlay(element) {
        const scrollTop = document.documentElement.scrollTop;
        const scrollLeft = document.documentElement.scrollLeft;
        const rect = element.getBoundingClientRect();
        const overlay = document.createElement('div');
        overlay.style.position = 'absolute';
        overlay.style.top = `${rect.top - 3 + scrollTop}px`;
        overlay.style.left = `${rect.left - 3}px`;
        overlay.style.width = `${rect.width + 6 + scrollLeft}px`;
        overlay.style.height = `${rect.height + 6}px`;
        overlay.style.border = '2px solid red';
        overlay.style.pointerEvents = 'none'; // Allow clicks to pass through
        document.body.appendChild(overlay);
        return overlay;
    }
}
