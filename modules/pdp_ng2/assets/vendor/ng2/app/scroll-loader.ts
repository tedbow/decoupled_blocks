/**
 * Created by jefflu on 1/20/2016.
 * This is a generic concrete class for loading components
 * - Manually load in components by providing an array of component elements
 * - Load components on demand when a comonent element is scolled into view
 * - Auto components on initial load by detectting if component elements are in view
 */

import {Renderer, ElementRef} from 'angular2/core';

import {Observable} from 'rxjs/Observable';
import 'rxjs/add/observable/fromEvent';
import 'rxjs/add/operator/debounceTime';

import { _settings } from '../settings.ts';

export class ScrollLoader {

    elements:string[];
    elementObj:any;
    nodes: any;
    height: any;
    width: any;
    contentHeight: any;

    constructor(elements){
        this.contentHeight = 0;
        this.elements = elements;
        this.elementObj = elements.reduce(function(obj, value, index) {
            obj[value] = index;
            return obj
        }, {});
        this.nodes = [];
        this.subscribe();
    }

    /**
     * This function set the initial page view by calling waltTheDom to gather all component elements
     * that are in the content-wrap container.  Then it will call hydrateComponent recursively to initialize
     * components for any elements that are in the view port
     */
    initialise() {
        let content = document.querySelector('.content-wrap');
        if(content) {
            this.walkTheDom(content, this.nodes);
            this.hydrateComponent(this.nodes, 0);
        }
    }

    /**
     * A recursive function that operates on the nodes array that contains all elements in the content-wrapper
     * aka the page.  It checks one element at a time to see if it is in view, it creates an observer on that element
     * then calls loadComponents to hydrate the element.  Once the observer detects changes in the element,
     * it will add to contentHeight (the accumulative height) then recurse hydrateComponent with the next element
     * @param nodes
     * @param index
     */
    hydrateComponent(nodes, index) {
        // observer map and configuration
        let config = { attributes: false, childList: true, characterData: false, subtree: true };
        let self = this;
        let $el = nodes[index] ? document.querySelector(nodes[index]) : null;

        if($el && self.elementInViewport($el) && $el.innerHTML.length === 0) {
            let vp = self.getViewPort();
            let clientRect = $el.getBoundingClientRect();
            this.contentHeight += clientRect.height;
            if(this.contentHeight < vp.height || clientRect.top < vp.height) {
                let observer = new MutationObserver(function(mutations, instance) {
                    instance.disconnect();
                    setTimeout(function() {
                        self.contentHeight += $el.children[0].clientHeight;
                        if(++index < nodes.length) {
                            self.hydrateComponent(nodes, index);
                        }
                    }, 10);
                });
                observer.observe($el, config);
                self.loadComponents([$el.localName]);

            }
        } else if(++index < nodes.length) {
            self.hydrateComponent(nodes, index);
        }
    }

    /**
     * This is a recursive function that will traverse the tree of the given root element to gather all
     * child elements
     *
     * @param el
     * @param nodes
     * @returns {any}
     */
    walkTheDom(el, nodes) {
        let children = el.children,
            self = this;
        if(children) {
            for(let index=0; index<children.length; index++) {
                let child = children[index];
                let grandChildren = child.children;
                if(grandChildren && grandChildren.length > 0) {
                    self.walkTheDom(child, nodes);
                } else {
                    let elementName = child.localName;
                    if(self.elementObj[elementName] >= 0) {
                        nodes.push(elementName);
                        return nodes;
                    }
                }
            }
        }
    }

    /**
     * This function subscribes to window scroll event
     * checkElements is called to see if any of the elements have been scrolled into view
     */
    subscribe() {
        let self = this;
        Observable.fromEvent(window, 'scroll')
            .debounceTime(200)
            .subscribe(x => {
                self.checkElements(self.elements);
            });
    }

    /**
     * Helper function to check if any of the elements are in view.
     * If a element is in view, its corresponding component is loaded and initialized
     * @param elements
     */
    checkElements(elements) {
        let self = this;
        elements.forEach(function(el, index) {
            let $el = document.querySelector(el);
            if($el && self.elementInViewport($el)) {
                if($el.innerHTML.length === 0) {
                    let className = self.getClassName(el);
                    System.import(_settings.buildPath + '/' + el + '/' + el + '.ts').then(function(module) {
                        window.app.bootstrap(module[className]);
                    });
                }
            }
        })
    }

    loadComponents(elements) {
        this.checkElements(elements);
    }

    /**
     * Helper function to get class name out of selector
     * @param name
     * @returns {string}
     */
    getClassName(name) {
        return (name.toLowerCase().charAt(0).toUpperCase() + name.slice(1)).replace(/-(.)/g, function(match, group1) {
            return group1.toUpperCase();
        });
    }

    /**
     * Get current viewport width and height
     * @returns {{height: *, width: *}}
     */
    getViewPort() {
        let w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
        let h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)
        return {height: h, width: w};
    }

    /**
     * Checks to see if an element is in view
     * @param el
     * @returns {boolean}
     */
    elementInViewport(el) {
        var top = el.offsetTop;
        var left = el.offsetLeft;
        var width = el.offsetWidth;
        var height = el.offsetHeight;

        while(el.offsetParent) {
            el = el.offsetParent;
            top += el.offsetTop;
            left += el.offsetLeft;
            width = el.offsetWidth;
            height = el.offsetHeight;
        }

        return (
            top <= (window.pageYOffset + window.innerHeight) &&
            left < (window.pageXOffset + window.innerWidth) &&
            (top + height) > window.pageYOffset &&
            (left + width) > window.pageXOffset
        );
    }
}
