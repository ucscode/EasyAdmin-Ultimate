'use strict';

/**
 * Internal Service
 */
import { Notification } from './notification.js';
import { Toaster } from './toaster.js';
/**
 * https://github.com/zenorocha/clipboard.js
 */
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
            new ClipboardJs('[data-media-field-copier]', {
                text: (trigger) => $(trigger).prev().val()
            })
                .on('success', (e) => {
                    new Toaster({
                        body: 'Copied to clipboard',
                        type: Toaster.TYPE_WARNING,
                        placement: Toaster.PLACEMENT_BOTTOM_RIGHT,
                    }).show();
                })
        }
    }
});