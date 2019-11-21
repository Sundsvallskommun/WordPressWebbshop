'use strict';

jQuery(document).ready(function($) {
    $('.js-search-enduser').select2({
        placeholder: "Ange slutanvändare",
        delay: 250,
        minimumInputLength: 3,
        language: "sv",
        ajax: {
            type: 'POST',
            url: ajax.url,
            data: function (params) {
                return {
                    action: 'search_enduser',
                    s: params.term
                }
            },
            dataType: 'json',
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                  results: data
                };
              }
        }
    });
});