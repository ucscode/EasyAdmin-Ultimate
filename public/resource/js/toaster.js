/**
 * @see https://github.com/jarstone/bs5-toast
 */
import bs5 from 'bs5-toast'; 

/**
 * @typedef {Object} ToasterArgument
 * @property {string} body - The content to render
 * @property {string} header - The title of the toast
 * @property {string} icon - Add icon to header or body (if header is not set)
 * @property {string} type - The toast type e.g Toaster.TYPE_SUCCESS
 * @property {string} placement - The position of the toast e.g Toaster.PLACEMENT_TOP_RIGHT
 * @property {string} className - Custom class attribute to be added to the toast
 * @property {boolean} animation - Apply transition to the toast (default: true)
 * @property {boolean} autohide - Auto hide the toast (default: true)
 * @property {boolean} btnClose - Show close button (default: true)
 * @property {boolean} btnCloseWhite - Set close button as white variant (default: false)
 * @property {number} delay - Delay hiding the toast (default: 5000)
 * @property {number} gap - Gap between toasts <megapixel> (default: 16)
 * @property {string} margin - Margin of corner. Can also be filled with a css variable. example: var(--toast-margin) (default: 1rem)
 */
export class Toaster extends bs5.Toast
{
    static PLACEMENT_TOP_RIGHT = 'top-right';
    static PLACEMENT_TOP_LEFT = 'top-left';
    static PLACEMENT_BOTTOM_RIGHT = 'bottom-right';
    static PLACEMENT_BOTTOM_LEFT = 'bottom-left';
    static PLACEMENT_DEFAULT = Toaster.PLACEMENT_TOP_RIGHT;

    static TYPE_SUCCESS = 'text-bg-success';
    static TYPE_PRIMARY = 'text-bg-primary';
    static TYPE_SECONDARY = 'text-bg-secondary';
    static TYPE_INFO = 'text-bg-info';
    static TYPE_WARNING = 'text-bg-warning';
    static TYPE_DANGER = 'text-bg-danger';
    static TYPE_LIGHT = 'text-bg-light';
    static TYPE_DARK = 'text-bg-dark';

    /**
     * @type {string}
     */
    #type;

    /**
     * @param {ToasterArgument} param 
     */
    constructor(param) {
        const type = param.type || Toaster.TYPE_INFO;

        param.btnCloseWhite = (
            type && type.trim().length && 
            ![Toaster.TYPE_WARNING, Toaster.TYPE_INFO, Toaster.TYPE_LIGHT].includes(type) && 
            !param.header
        ) || param.btnCloseWhite;
        
        if(param.icon) {
            (function() {
                const icon = `<i class="${param.icon}"></i>`;
                if(param.header && param.header != '') param.header = `${icon} ${param.header}`;
                else param.body = `${icon} ${param.body}`;
            })();
        }
        
        super(param);
        
        this.#type = `${type} bs5-toaster`;
    }

    show() {
        this.#addTypeClasses();
        super.show();
    }

    #addTypeClasses() {
        if(this.#type) {
            for(let className of this.#type.split(' ')) {
                this.element.classList.add(className);
            }
        }
    }
}