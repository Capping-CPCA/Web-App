function showMenu() {
    // Menu display is controlled by CSS classes
    $('.side-menu').removeClass('hidden');
    $('.menu-cover').addClass('show');
    $('.non-menu-content').addClass('blur');
}

function hideMenu() {
    // Menu display is controlled by CSS classes
    $('.side-menu').addClass('hidden');
    $('.menu-cover').removeClass('show');
    $('.non-menu-content').removeClass('blur');
}