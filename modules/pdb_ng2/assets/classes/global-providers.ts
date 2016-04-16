/**
 * @author Shawn Stedman
 *
 * Load shared global services and inject as providers in main app bootstrap to
 * create one shared service instance between all components.
 */
import {provide} from "angular2/core";

export class GlobalProviders {
  injectables: any;
  ngServiceClassNames: [];

  constructor(private injectables: any) {
    this.injectables = injectables;
    this.ngServiceClassNames = Object.keys(injectables);
  }

  /**
   * importGlobalInjectables
   *
   * @return {object} array of promises returned from System.import.
   */
  importGlobalInjectables() {
    let importPromises = [];

    for (let ngServiceClassName in this.injectables) {
      // must use use absolute path in System.import
      let filename = this.injectables[ngServiceClassName];
      importPromises.push(System.import(filename));
    };

    return importPromises;
  }

  /**
   * createGlobalProvidersArray
   *
   * @param {object} globalServices - array of imported service definitions.
   * @return {object} array of provide functions for global @Injectable
   * services.
   */
  createGlobalProvidersArray(globalServices) {
    let globalProviders = [];

    for (let service = 0; service < globalServices.length; service++) {
      for (let ngName = 0; ngName < this.ngServiceClassNames.length; ngName++) {
        // Check for services with ngServiceClassNames defined in
        // global_injectables in the .info file and add to an array of providers
        // to pass into bootstrap.
        if (globalServices[service].hasOwnProperty(this.ngServiceClassNames[ngName])) {
          let globalService = globalServices[service][this.ngServiceClassNames[ngName]];

          if (Array.isArray(globalService)) {
            globalProviders.push(globalService);
          } else {
            globalProviders.push(
              provide(
                globalService,
                {
                  useClass: globalService
                }
              )
            );
          }
        }
      }
    }

    return globalProviders;
  }
}
