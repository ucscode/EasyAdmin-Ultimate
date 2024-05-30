"use strict";

import $ from 'jquery';
// import bootbox from 'bootbox';

$(function() {
    const structure = JSON.parse(atob($("[data-node-structure]").attr("data-node-structure")));
    
    new TreeDataNext(structure)

        .build(function(element, depth, item) {
            $(element)
                .attr('data-depth', depth)
                .addClass(item.hasChildren ? 'branch' : 'leaf')
                .find('> a.tree-anchor')
                    .attr('href', item.hierarchyUrl);
        })

        .then(node => {
            let clone = node.cloneNode(true);

            $(clone).find('.tree')
                .removeClass('tree')
                .addClass('tree-items');

            $(clone).find('.tree-anchor')
                .attr('href', 'javascript:void(0)')
                .on('click', function() {
                    $(this).toggleClass('show');
                });

            $(clone).find(".tree-item[data-depth=0] > .tree-anchor")
                .addClass('show');

            $('#tree-nodes').append(node);
            $("#tree-list").append(clone);
        });
});