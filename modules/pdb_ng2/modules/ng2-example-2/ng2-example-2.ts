import {Component} from 'angular2/core';
import {FORM_DIRECTIVES} from 'angular2/common';

var path = drupalSettings.apps['ng2-example-2']['uri'] + "/ng2-example-2.html";
console.log(path);
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
