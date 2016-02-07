import {platform, Component} from 'angular2/core';
import {bootstrap, BROWSER_PROVIDERS, BROWSER_APP_PROVIDERS} from 'angular2/platform/browser';

import 'rxjs/add/operator/map';

import {ScrollLoader} from 'scroll-loader.ts';

// The apps variable is an array containing the path to each component on the
// page's main app.ts file.
var apps = Object.keys(Drupal.settings.angularmods.apps);

@Component({
    selector: "app",
    template: `
  `,
    directives: []
})
class AppComponent extends ScrollLoader{
    constructor() {
        super(apps);

        this.initialise();
    }
}
let app = platform(BROWSER_PROVIDERS).application([BROWSER_APP_PROVIDERS]);

app.bootstrap(AppComponent);
window.app = app;
