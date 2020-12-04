import FlyoutMenuPlugin from 'src/plugin/main-menu/flyout-menu.plugin.js';
import DeviceDetection from 'src/helper/device-detection.helper';
import DomAccess from 'src/helper/dom-access.helper';
import Iterator from 'src/helper/iterator.helper';

export default class EcFlyoutMenuPlugin extends FlyoutMenuPlugin {

    static options = {
        flyoutIsTurnedOff: false,
        ...FlyoutMenuPlugin.options
    }

    init() {
        console.log(this.options);
        super.init();
    }

    /**
     * registers all needed events
     *
     * @private
     */
    _registerEvents() {
        if (this.options.flyoutIsTurnedOff) {
            console.log('this')
            this._registerEventsWithoutMouseEnter()
        } else {
            console.log('that')
            super._registerEvents()
        }
    }

    _registerEventsWithoutMouseEnter() {
        const clickEvent = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';
        // turn off MouseEnter
        //const openEvent = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'mouseenter';
        const closeEvent = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'mouseleave';

        // register opening triggers
        Iterator.iterate(this._triggerEls, el => {
            const flyoutId = DomAccess.getDataAttribute(el, this.options.triggerDataAttribute);
            // turn off MouseEnter
            //el.addEventListener(openEvent, this._openFlyoutById.bind(this, flyoutId, el));
            el.addEventListener(closeEvent, () => this._debounce(this._closeAllFlyouts));
        });

        // register closing triggers
        Iterator.iterate(this._closeEls, el => {
            el.addEventListener(clickEvent, this._closeAllFlyouts.bind(this));
        });

        // register non touch events for open flyouts
        if (!DeviceDetection.isTouchDevice()) {
            Iterator.iterate(this._flyoutEls, el => {
                el.addEventListener('mousemove', () => this._clearDebounce());
                el.addEventListener('mouseleave', () => this._debounce(this._closeAllFlyouts));
            });
        }
    }
};