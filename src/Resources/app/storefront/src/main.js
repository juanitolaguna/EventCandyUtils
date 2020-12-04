import EcFlyoutMenuPlugin from "./script/ec-flyout-menu.plugin";

const PluginManager = window.PluginManager;
PluginManager.override('FlyoutMenu', EcFlyoutMenuPlugin, '[data-flyout-menu]');


// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}