import {Component} from 'angular2/core';
var path = drupalSettings.apps['ng2-example-1']['uri'] + "/ng2-example-1.html";

@Component({
  selector: 'ng2-example-1',
  templateUrl : path,
})
export class Ng2Example1 {
}
