/**
 * Write custom javascript code here
 * 
 * Note: This file is imported as a module to make it highly compactible with symfony asset mapper
 */
'use strict';

$(function() {
    $(".eau-modal[data-bs-visible]").each(function() {
        const render = $(this).attr('data-bs-visible');
        if(render === 'true') new bootstrap.Modal(this).show();
    });
});