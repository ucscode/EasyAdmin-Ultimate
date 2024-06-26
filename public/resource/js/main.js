'use strict';

/**
 * Internal Service
 */
import { Notification } from './notification.js';
import { Toaster } from './toaster.js';
/**
 * @see https://github.com/zenorocha/clipboard.js
 */
import ClipboardJs from 'clipboard';
/**
 * @see https://github.com/biati-digital/glightbox
 */
import GLightBox from 'glightbox';

$(function() {

    new class
    {
        constructor()
        {
            this.autoDisplayModal();
            this.togglePasswordVisibility();
            this.updateUserNotification();
            this.clipboardEvents();
            this.configureGLightBox();
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

        clipboardEvents()
        {
            const toastFactory = (text = 'Copied to clipboard') => {
                return new Toaster({
                    body: text,
                    type: Toaster.TYPE_WARNING,
                    placement: Toaster.PLACEMENT_BOTTOM_RIGHT
                });
            }
            
            new ClipboardJs('[data-crud-field-clip]', {
                text: (el) => $(el).parent().find('.form-control,.form-select').val()
            })
                .on('success', () => toastFactory().show());
                
            new ClipboardJs('[data-media-index-clip]', {
                text: (el) => el.href
            })
                .on('success', () => toastFactory('File link copied').show())
            
            $('table').on('click', '[data-media-index-clip]', e => e.preventDefault());
        }

        configureGLightBox()
        {
            let lightbox = GLightBox({
                selector: '[data-glightbox]'
            });
        }
    }
});