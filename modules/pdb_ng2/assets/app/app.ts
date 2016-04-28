import {platform, Component, PACKAGE_ROOT_URL} from 'angular2/core';
import {bootstrap, BROWSER_PROVIDERS, BROWSER_APP_PROVIDERS} from 'angular2/platform/browser';

import 'rxjs/add/operator/map';

import {ScrollLoader} from '../classes/scroll-loader.ts';
import {GlobalProviders} from '../classes/global-providers.ts';

var injectables = drupalSettings.ng2.global_injectables;
var globalProviders = new GlobalProviders(injectables);
var importPromises = globalProviders.importGlobalInjectables();

// Dynamically load all globally shared @Injectable services and pass as
// providers into main app bootstrap.
Promise.all(importPromises).then((globalServices) => {
  // array of providers to pass into longform bootstrap to make @Injectable
  // services shared globally.
  var globalProvidersArray = globalProviders.createGlobalProvidersArray(globalServices);

  // components contains metadata about all ng2 components on the page.
  var components = drupalSettings.ng2.components;
  // app is the main root component, using longform bootstrap to allow multiple
  // components to be bootstrapped by ScrollLoader.
  var app = platform(BROWSER_PROVIDERS).application([BROWSER_APP_PROVIDERS, ...globalProvidersArray]);
  var loader = new ScrollLoader(app, components);
  loader.initialize();
});
