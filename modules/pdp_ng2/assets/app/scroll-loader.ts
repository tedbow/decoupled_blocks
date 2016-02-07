/**
 * Created by jefflu on 1/20/2016.
 * This is a generic concrete class for loading components
 * - Manually load in components by providing an array of component elements
 * - Load components on demand when a comonent element is scolled into view
 * - Auto components on initial load by detectting if component elements are in view
 */

import {Renderer, ElementRef} from "angular2/core";

import {Observable} from "rxjs/Observable";
import "rxjs/add/observable/fromEvent";
import "rxjs/add/operator/debounceTime";

export class ScrollLoader {

    elements: string[];
    elementObj: any;
    nodes: any;
    height: any;
    width: any;
    contentHeight: any;

    constructor(elements, apps) {
        this.contentHeight = 0;
        this.elements = elements;
        console.log('elements!!! ' + elements);
        this.elementObj = elements.reduce(function(obj, value, index) {
            obj[value] = index;
            return obj;
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
        console.log('initializing');
        let content = document.querySelector(".region-content");

        if (content) {
            console.log('got content, walking dom');
            this.walkTheDom(content, this.nodes);
            console.log('heres where we end up! ' + this.nodes);
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
        console.log(nodes);
        // observer map and configuration
        let config = { attributes: false, childList: true, characterData: false, subtree: true };
        let $el = nodes[index] ? document.querySelector(nodes[index]) : null;
        console.log($el);

        if ($el && this.elementInViewport($el) && $el.innerHTML.length === 0) {
            console.info('we are in viewport');
            let vp = this.getViewPort();
            let clientRect = $el.getBoundingClientRect();
            this.contentHeight += clientRect.height;

            if (this.contentHeight < vp.height || clientRect.top < vp.height) {
                let observer = new MutationObserver(function(mutations, instance) {
                    instance.disconnect();
                    setTimeout(() => {
                        this.contentHeight += $el.children[0].clientHeight;
                        if (++index < nodes.length) {
                            this.hydrateComponent(nodes, index);
                        }
                    }, 0);
                }.bind(this));

                observer.observe($el, config);
                this.loadComponents([$el.localName]);
            }
        } else if (++index < nodes.length) {
            console.log('well what now?');
            this.hydrateComponent(nodes, index);
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
        console.log(el);
        let children = el.children;

        if (children) {
            for (let index = 0; index < children.length; index++) {
                let child = children[index];
                let grandChildren = child.children;

                if (grandChildren && grandChildren.length > 0) {
                    this.walkTheDom(child, nodes);
                } else {
                    let elementName = child.localName;

                    if (this.elementObj[elementName] >= 0) {
                        nodes.push(elementName);
                        console.log('nodes pushed');
                        return nodes;
                    } else {
                        console.log('nothing pushed');
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
        Observable.fromEvent(window, "scroll")
            .debounceTime(200)
            .subscribe(x => {
                this.checkElements(this.elements);
            }, this);

        Observable.fromEvent(window, "resize")
            .debounceTime(200)
            .subscribe(x => {
                this.checkElements(this.elements);
            }, this);

        Observable.fromEvent(window, "orientationchange")
            .subscribe(x => {
                this.checkElements(this.elements);
            }, this);
    }

    /**
     * Unsubscribe elements which have already been bootstrapped by deleting from
     * elements array.
     *
     * @param el - element to be unsubscribed
     */
    unsubscribe(el) {
        if (this.elements.length) {
            let index = this.elements.indexOf(el);

            if (index > -1) {
                // immutable splice
                this.elements = [
                    ...this.elements.slice(0, index),
                    ...this.elements.slice(index + 1)
                ];
            }
        }
    }

    /**
     * Helper function to check if any of the elements are in view.
     * If a element is in view, its corresponding component is loaded and initialized
     * @param elements
     */
    checkElements(elements) {
        elements.forEach(function(el, index) {
            let $el = document.querySelector(el);

            if ($el && this.elementInViewport($el)) {
                // assuming if innerHTML is empty module has not been loaded
                if ($el.innerHTML.length === 0) {
                    let className = this.getClassName(el);
                    this.bootstrapComponent(el, className);
                }
            }
        }, this);
    }

    loadComponents(elements) {
        console.log('loading ' + elements);
        this.checkElements(elements);
    }

    /**
     * Bootstraps individual component into the DOM using System.js.
     * @param el
     * @param className
     */
    bootstrapComponent(el, className) {
        let componentEntryPoint = drupalSettings.apps[el]['uri'] + "/" + el + ".ts";
        console.log('our entry point: ' + componentEntryPoint);
        System.import(componentEntryPoint).then(function(components) {
            window.app.bootstrap(components[className]).then(function(bootstrappedComponent) {
                this.unsubscribe(el);
            }.bind(this));
        }.bind(this));
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
        let w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        let h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
        return {height: h, width: w};
    }

    /**
     * Checks to see if an element is in view
     *
     * @param el {object}
     * @returns {boolean}
     *
     * @see based on SO answer: http://stackoverflow.com/a/7557433/1774183
     */
    elementInViewport(el) {
        var rect = el.getBoundingClientRect();
        console.log(rect);
        return (
            rect.top >= 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
}
