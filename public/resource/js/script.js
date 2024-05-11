/**
 * Write custom javascript code here
 * 
 * Note: This file is imported as a module to make it highly compactible with symfony asset mapper
 */
'use strict';

$(function() {
    $(".eau-modal[data-bs-show]").each(function() {
        const show = $(this).attr('data-bs-show');
        if(show === 'true') new bootstrap.Modal(this).show();
    });
});