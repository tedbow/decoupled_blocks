import {Component} from 'angular2/core';
var path = drupalSettings.apps['ng2-example']['uri'] + "/ng2-example.html";

@Component({
  selector: 'ng2-example',
  templateUrl : path,
})
export class Ng2Example {
}
