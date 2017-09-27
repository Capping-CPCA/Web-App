/**
 * A class for the on-screen tutorials. These are small popovers that are displayed
 * to inform the user about the functions of the application. Once a tutorial is
 * viewed, it is saved into the localStorage as already viewed.
 *
 * @param title - the title displayed in the popover
 * @param content - the content displayed in the popover
 * @param selector - a CSS selector to the element the popover points to
 * @param showElements - an array of CSS selectors of the elements to highlight
 * @param next - (optional) the name of the next tutorial to show (if any)
 * @param placement - (optional) the placement of the popover (left, right, top, bottom).
 *  Default is to 'auto'.
 * @param trigger - (optional) how the popover is triggers (click, focus, manual). Default
 *  is to 'manual'.
 * @constructor
 */
function Tutorial(title, content, selector, showElements, next, placement, trigger) {
    this.selector = selector;
    this.elements = showElements;
    this.viewed = false;
    this.popover = {
        title: title,
        content: content,
        placement: placement ? placement : 'auto',
        trigger: trigger ? trigger : 'manual',
        template:
        '<div class="popover" role="tooltip">' +
        '<div class="arrow"></div>' +
        '<h3 class="popover-header"></h3>' +
        '<div class="popover-body"></div>' +
        '<div class="popover-footer">' +
        '<button type="button" class="btn cpca popover-btn">OK</button>' +
        '</div>' +
        '</div>'
    };
    this.next = next;
}

var tutorials = {
    // Dashboard Page
    menu: new Tutorial(
        'Navigation Menu',
        'Use this menu to navigate to major pages ' +
        'within the application.',
        'nav.nav',
        ['.side-menu'],
        'login'
    ),
    login: new Tutorial(
        'Account Settings',
        'Click here to log out of your account and' +
        ' manage other account settings.',
        '.navbar-right',
        ['.navbar']
    ),
    // Curriculum Page
    curriculum: new Tutorial(
        'Curriculum Add',
        'Click here to add a new curriculum.',
        '#new-curriculum-btn',
        ['#new-curriculum-btn'],
        'curriculumFilter'
    ),
    curriculumFilter: new Tutorial(
        'Curriculum Filter',
        'Type here to filter curriculum results by ' +
            'name and type.',
        '#curriculum-filter',
        ['#curriculum-filter'],
        'curriculumCard'
    ),
    curriculumCard: new Tutorial(
        'Curriculum Options',
        'Use the buttons below each curriculum to: ' +
            'View, Edit, or Archive the curriculum.',
        '.result-card:first-child',
        ['.result-card:first-child']
    )
};

function showTutorial(name) {
    const tutorial = tutorials[name];
    if (!tutorial.viewed) {
        $(tutorial.selector).popover(tutorial.popover);
        for (var i = 0; i < tutorial.elements.length; i++) {
            $(tutorial.elements[i]).addClass('tutorial');
        }
        $('.menu-cover').addClass('tutorial-cover');
        $(tutorial.selector).popover('show');

        // "OK" button closes tutorial
        $('.popover-btn').on('click', function () {
            hideTutorial(name);
        });
    }
}

function hideTutorial(name) {
    const tutorial = tutorials[name];
    for (var i = 0; i < tutorial.elements.length; i++) {
        $(tutorial.elements[i]).removeClass('tutorial');
    }
    $('.menu-cover').removeClass('tutorial-cover');
    $(tutorial.selector).popover('hide');
    tutorial.viewed = true;
    $('.popover-btn').off('click');

    // save viewed tutorials in localStorage (possibly in DB later?)
    localStorage.setItem("tutorials", JSON.stringify(tutorials));

    if (tutorial.next) {
        showTutorial(tutorial.next);
    }
}

/**
 * Resets the viewed attribute of each tutorial to false.
 * Essentially means each tutorial hasn't been viewed yet.
 */
function unViewAllTutorials() {
    localStorage.removeItem("tutorials");
    for (var key in tutorials) {
        if (!tutorials.hasOwnProperty(key))
            continue;
        tutorials[key].viewed = false;
    }
}

/*
** NOTE: Unneeded as of now, menu is always present on screen **

function showMenu() {
    // Menu display is controlled by CSS classes
    $('.side-menu').removeClass('hidden');
    $('.menu-cover').addClass('show');
    $('.non-menu-content').addClass('blur');
    setTimeout(function() {
        showTutorial('menu');
    }, 300);
}

function hideMenu() {
    // Menu display is controlled by CSS classes
    $('.side-menu').addClass('hidden');
    $('.menu-cover').removeClass('show');
    $('.non-menu-content').removeClass('blur');
}
*/

$(function() {
    // Load tutorial data from localStorage if exists
    const tuts = localStorage.getItem("tutorials");
    if (tuts) {
        tutorials = Object.assign(tutorials, JSON.parse(tuts));
    }
});