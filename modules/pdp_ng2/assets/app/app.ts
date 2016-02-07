import {platform, Component} from 'angular2/core';
import {bootstrap, BROWSER_PROVIDERS, BROWSER_APP_PROVIDERS} from 'angular2/platform/browser';

import 'rxjs/add/operator/map';

import {ScrollLoader} from 'modules/pdp/modules/pdp_ng2/assets/app/scroll-loader.ts';

// The apps variable is an array containing the name of each angular module on
// the page as the key and the path to the angular module as the value.
var appkeys = Object.keys(drupalSettings.apps);
var apps = drupalSettings.apps;

@Component({
    selector: "app",
    template: '',
    directives: []
})
class AppComponent extends ScrollLoader{
    constructor() {
        super(appkeys, apps);
        this.initialise();
    }
}
let app = platform(BROWSER_PROVIDERS).application([BROWSER_APP_PROVIDERS]);

app.bootstrap(AppComponent);
window.app = app;
