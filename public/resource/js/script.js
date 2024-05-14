/**
 * Write custom javascript code here
 * 
 * Note: This file is imported as a module to make it highly compactible with symfony asset mapper
 */
'use strict';

$(function() {
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
});