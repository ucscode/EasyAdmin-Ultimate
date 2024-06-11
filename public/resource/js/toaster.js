/**
 * https://github.com/jarstone/bs5-toast
 */
import bs5 from 'bs5-toast'; 

/**
 * @typedef {Object} ToasterArgument
 * @property {string} body - The content to render
 * @property {string} header - The title of the toast
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

    static TYPE_DEFAULT = '';
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
        const type = param.type || Toaster.TYPE_DEFAULT;
        param.btnCloseWhite = (type && type.trim().length && ![Toaster.TYPE_WARNING, Toaster.TYPE_INFO, Toaster.TYPE_LIGHT].includes(type) && !param.header) || param.btnCloseWhite;
        super(param);
        this.#type = type;
    }

    show() {
        !this.#type || this.element.classList.add(this.#type);
        super.show();
    }
}