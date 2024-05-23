/**
 * Write custom javascript code here
 * 
 * Note: This file is imported as a module to make it highly compactible with symfony asset mapper
 */
'use strict';

$(function() {

    // get app context
    const appContext = $("#app-user-context").get(0)?.dataset;
    
    // Auto display modal on document load
    $(".eau-modal[data-bs-visible]").each(function() {
        const render = $(this).attr('data-bs-visible');
        if(render === 'true') new bootstrap.Modal(this).show();
    });

    // Toggle password visibility in one click
    $("button[data-password-toggle").click(function() {
        const input = $($(this).attr('data-password-toggle'));
        if(input.length) input.attr('type', (index, attr) => attr == 'text' ? 'password' : 'text');
    });
    
    // Update user notifications
    $('[data-notification-action]').click(function(e) {
        e.preventDefault();
        
        const identifier = $(this).parents('[data-notification-identifier]').attr('data-notification-identifier');

        fetch(appContext.asyncNotificationRoute, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: this.dataset.notificationAction,
                token: appContext.userToken,
                entityId: identifier ? atob(identifier) : '',
            }).toString()
        })
        .then(response => {
            if(response.status !== 200) {
                throw Error(`${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(response => {
            const topButton = $("[data-notification-anchor]");
            const container = identifier ? $(`[data-notification-identifier='${identifier}']`) : $('[data-notification-identifier]');

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
        })
        .catch(error => console.error(`${error}: Cannot update notification`));
    });
});