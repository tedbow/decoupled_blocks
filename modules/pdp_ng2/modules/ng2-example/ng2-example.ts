import {Component} from 'angular2/core';
var path = drupalSettings.apps['ng2-example']['uri'] + "/ng2-example.html";
console.log('THE PATH! ' + path);

@Component({
  selector: 'ng2-example',
  templateUrl : path,
})
export class Ng2Example {
}
