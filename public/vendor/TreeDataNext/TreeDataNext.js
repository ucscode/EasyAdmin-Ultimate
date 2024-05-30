/**
 * TreeDataNext.js
 * 
 * TreeDataNext is a modern JavaScript library for creating and customizing tree data structures in web applications. 
 * 
 * @link https://github.com/ucscode/TreeDataNext.js
 * 
 * @typedef {Object} NodeItem
 * @property {string|number} id     - The node id
 * @property {string} value         - The node Value
 * @property {string|number|null} parent - The node Parent Id
 */
class TreeDataNext {

    /** 
     * @type {NodeItem[]} 
     */
	#tree;
	
    /**
     * @param {NodeItem[]} tree 
     */
    constructor(tree) {
        this.#tree = tree;
    }

    /**
     * Build the tree elements and returns a promise
     * 
     * @param {function} callback  A callback that executes per created element allowing real-time update as each element is being created.
     * @returns {Promise<Element>}
     */
	build(callback) {
        return new Promise((resolve) => {
            // Get root node!
            let parent = this.#tree.find(function(item) {
                return [null, undefined, ''].includes(item.parent);
            });
            
            if(!parent) throw new Error("No root element found!");
            
            let nodeList = this.#createNode(parent, callback);
            let container = this.#createContainer('tree tree-base', nodeList);
            let wrapper = this.#createWrapper(container);

            resolve(wrapper);
        });
	}
	
    /**
     * Create a list element and recursively append children nodes if available
     * 
     * @param {NodeItem} nodeItem 
     * @param {function|undefined} callback
     * @param {number} depth
     * @returns {HTMLLIElement}
     */
	#createNode(nodeItem, callback, depth = 0) {
        let li = this.#createDOMElement(nodeItem);
        let children = this.#getChildren(nodeItem);

        if(children.length) {
            let ul = this.#createContainer();
            li.appendChild(ul);

            for(let child of children) {
                ul.appendChild(this.#createNode(child, callback, depth + 1))
            }
        }

        if(typeof callback === 'function') callback(li, depth, nodeItem);

        return li;
	}

    /**
     * Create and return a list element
     * 
     * @param {NodeItem} nodeItem
     * @returns {HTMLLIElement}
     */
    #createDOMElement(nodeItem) {
        let li = document.createElement('li');
        li.setAttribute('class', 'tree-item');

        let anchor = document.createElement('a');
        anchor.setAttribute('class', 'tree-anchor');
        anchor.setAttribute('href', 'javascript:void(0)');
        anchor.innerHTML = String(nodeItem.value);
        
        li.appendChild(anchor);
        return li;
    }
	
    /**
     * Get all children of a particular NodeItem
     * 
     * @param {NodeItem} nodeItem 
     * @returns {NodeItem[]}
     */
	#getChildren(nodeItem) {
		return this.#tree.filter(function(child) {
			return child.parent == nodeItem.id;
		});
	}

    /**
     * Create a base UL container and append the list Element
     * 
     * @param {string} className
     * @param {HTMLLIElement} nodeList 
     * @returns {HTMLUListElement}
     */
    #createContainer(className = '', nodeList) {
        let ul = document.createElement('ul');
        ul.setAttribute('class', `tree-family ${className}`);
        if(nodeList) ul.appendChild(nodeList);
        return ul;
    }

    /**
     * Create a wapper to append the UL container
     * 
     * @param {HTMLUListElement} nodeList 
     * @returns {HTMLDivElement}
     */
    #createWrapper(ul) {
        let container = document.createElement('div');
        container.setAttribute('class', 'tree-wrapper');
        container.appendChild(ul);
        return container;
    }
}