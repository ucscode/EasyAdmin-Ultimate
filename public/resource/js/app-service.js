"use strict";

class AppService
{
    propertyAccessor(obj, path) 
    {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }

    getContext()
    {
        return document.querySelector("#app-user-context")?.dataset;
    }
}

export const appService = new AppService();