YUI().use('node', 'transition', function (Y) {

    Y.one('#stacktrace').addClass('accessibly-hide');

    Y.one('.stacktrace .toggle').on('click', function (e) {
        var node = Y.one('#stacktrace');

        if (node.hasClass('accessibly-hide')) {
            node.removeClass('accessibly-hide');
            this.set('innerHTML', '-');
        } else {
            node.addClass('accessibly-hide');
            this.set('innerHTML', '+');
        }

        e.preventDefault();
    });

});