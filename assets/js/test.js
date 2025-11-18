    $(document).ready(function() {
    const $sidebar = $('#layout-menu');

    $('.layout-menu-toggle').on('click', function() {
        $sidebar.toggleClass('collapsed');

        // Toggle the chevron direction
        const $icon = $(this).find('i');
        if ($sidebar.hasClass('collapsed')) {
            $icon.removeClass('bx-chevron-left').addClass('bx-chevron-right');
        } else {
            $icon.removeClass('bx-chevron-right').addClass('bx-chevron-left');
        }
    });
});
