"use strict";

class Service
{
    #payload;

    constructor()
    {
        this.#payload = this.#setPayloadContext();
    }

    propertyAccessor(obj, path) 
    {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }

    getPayload()
    {
        return this.#payload;
    }

    #setPayloadContext() 
    {
        let dataset = Object.assign({}, document.querySelector('#app-js-payload-container')?.dataset ?? {});

        if(Object.keys(dataset).length) {
            let {jsPayload, ...context} = dataset;
            jsPayload = JSON.parse(atob(jsPayload) === '[]' ? '{}' : atob(jsPayload));
            context.public = JSON.parse(atob(context.public));
            return Object.assign({}, jsPayload, context);
        }

        return dataset;
    }
}

export const service = new Service();