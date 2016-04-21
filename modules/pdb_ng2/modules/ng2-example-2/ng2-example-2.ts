import {Component} from 'angular2/core';
import {FORM_DIRECTIVES} from 'angular2/common';

var path = "/modules/pdb/modules/pdb_ng2/modules/ng2-example-2/ng2-example-2.html";

@Component({
    selector: 'ng2-example-2',
    templateUrl: path,
    directives: [FORM_DIRECTIVES]
})
export class Ng2Example2 {
  foo: string;
  bar: string;
  constructor() {
    this.foo = 'Foo';
    this.bar = 'Bar';
  }
}
