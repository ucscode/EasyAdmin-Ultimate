"use strict";

export function propertyAccessor(obj, path) {
    return path.split('.').reduce((acc, part) => acc && acc[part], obj);
}

export const payload = (() => {
    const base64 = document.querySelector('#app-js-payload')?.dataset.jsPayload
    return JSON.parse(atob(base64));
})();