'use strict';

import { appService } from './app-service.js';
import { Notification } from './notification.js';
import ClipboardJs from 'clipboard';

$(function() {

    new class
    {
        constructor()
        {
            this.autoDisplayModal();
            this.togglePasswordVisibility();
            this.updateUserNotification();
            this.automateCopyEvent();
        }

        autoDisplayModal()
        {
            // Auto display modal on document load
            $(".eau-modal[data-bs-visible]").each(function() {
                const render = $(this).attr('data-bs-visible');
                if(render === 'true') new bootstrap.Modal(this).show();
            });
        }

        togglePasswordVisibility()
        {
            // Toggle password visibility in one click
            $("button[data-password-toggle").on('click', function() {
                const input = $($(this).attr('data-password-toggle'));
                if(input.length) input.attr('type', (index, attr) => attr == 'text' ? 'password' : 'text');
            });
        }

        updateUserNotification()
        {
            // Update user notifications
            $('[data-notification-delegate]').on('click', '[data-notification-action]', function(e) {
                e.preventDefault();
                new Notification(this);
            });
        }

        automateCopyEvent()
        {
            new ClipboardJs('[data-file-copy]', {

            });
            // copy an text or attribute value
            $("[data-file-copy]").on('click', function(e) {
                
                e.preventDefault();
                try {
                    let query = this.dataset.copy.split(':');
                    
                    if(query.length !== 2) {
                        throw new TypeError('[data-copy] attribute must be in the format "reference:property"');
                    }

                    const el = (query[0] === '_self') ? this : document.querySelector(query[0]);

                    if(!el) {
                        throw new ReferenceError(`[data-copy] cannot find reference to "${query[0]}" element"`)
                    }

                    const text = appService.propertyAccessor(el, query[1]);
                    console.log(text)
                } catch(e) {
                    console.error(e.message)
                }
            });
        }
    }
});