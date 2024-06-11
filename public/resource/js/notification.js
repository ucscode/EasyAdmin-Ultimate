"use strict";

import $ from 'jquery';
import { appService } from "./app-service.js";
import { Toaster } from './toaster.js';

export class Notification
{
    #element;
    #identifier;

    /**
     * 
     * @param {HTMLElement} element 
     */
    constructor(element)
    {
        this.#element = element;
        this.#identifier = $(this.#element).parents('[data-notification-identifier]').attr('data-notification-identifier');
        
        this.#sendRequest()
            .then(response => this.#getResponseObject(response))
            .then(response => this.#updateDocumentElements(response))
            .catch(error => this.#catchError(error));
    }

    /**
     * Send an API Request and get a Promise<Response> Object
     * 
     * @returns {Promise<Response>}
     */
    #sendRequest()
    {
        return fetch(appService.getContext().asyncNotificationRoute, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: this.#element.dataset.notificationAction,
                token: appService.getContext().securityToken,
                entityId: this.#identifier ? atob(this.#identifier) : '',
            }).toString()
        })
    }

    /**
     * Convert the response into a javascript object and throw an error if the status != OK
     * 
     * @param {Response} response 
     * @returns {Promise}
     */
    #getResponseObject(response)
    {
        if(response.status !== 200) {
            throw new Error(`${response.status} ${response.statusText}`);
        }
        return response.json();
    }

    /**
     * Update the elements of the current page (relating to the notification)
     * 
     * @param {Object.<string, *>} response 
     * @returns {void}
     */
    #updateDocumentElements(response)
    {
        const topButton = $("[data-notification-anchor]");
        const container = this.#identifier ? $(`[data-notification-identifier='${this.#identifier}']`) : $('[data-notification-identifier]');

        if(container.length) {
            switch(response.action) {
                case 'delete':
                    container
                        .addClass('deleted')
                        .find('.btn-notification-control')
                        .attr('disabled', 'disabled');
                    break;
                default:
                    // read
                    container.removeClass('unread');
            }
        }
        
        topButton.find("[data-count]").text(response.count);

        if(!response.count) {
            topButton.find('.icon').removeClass('animate__swing');
            topButton.find('.badge').addClass('d-none');
        }
    }

    #catchError(error)
    {
        console.error(`Cannot Update Notification: ${error.message}`);

        new Toaster({
            body: 'Notification Update Failed',
            type: Toaster.TYPE_WARNING
        }).show()
    }
}