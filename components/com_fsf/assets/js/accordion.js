jQuery(document).ready(function () {

    if (window.ie6) var heightValue = '100%';
    else var heightValue = '';

    var togglerName = 'div.accordion_toggler_';
    var contentName = 'div.accordion_content_';

    var acc_elem = null;
    var acc_toggle = null;

    var counter = 1;
    var toggler = $$(togglerName + counter);
    var content = $$(contentName + counter);

    while (toggler.length > 0) {
        // Accordion anwenden
        new Fx.Accordion(toggler, content, {
            opacity: false,
            alwaysHide: true,
            display: -1,
            onActive: function (toggler, content) {
                acc_elem = content;
                acc_toggle = toggler;

                jQuery('.fsf_faq_answer').removeClass('shown');
                jQuery('.fsf_faq_question').removeClass('shown');
                content.addClass('shown');
                toggler.addClass('shown');
            },
            onBackground: function (toggler, content) {
            },
            onComplete: function () {
                var element = jQuery(this.elements[this.previous]);
                if (element && element.offsetHeight > 0) element.setStyle('height', heightValue);

                if (!acc_elem)
                    return;

                var scroll = new Fx.Scroll(window, {
                    wait: false,
                    duration: 250,
                    transition: Fx.Transitions.Quad.easeInOut
                });

                if (typeof (scroll.scrollTo) == "undefined")
                    return;

                var window_top = window.pageYOffset;
                var window_bottom = window_top + window.innerHeight;
                var elem_top = acc_toggle.getPosition().y;
                var elem_bottom = elem_top + acc_elem.offsetHeight + acc_toggle.offsetHeight;

                // is element off the top of the displayed windows??
                if (elem_top < window_top) {
                    scroll.scrollTo(window.pageXOffset, acc_toggle.getPosition().y);
                } else if (elem_bottom > window_bottom) {
                    var howmuch = elem_bottom - window_bottom;
                    if (elem_top - howmuch > 0) {
                        scroll.scrollTo(window.pageXOffset, window_top + howmuch + 22);
                    } else {
                        scroll.scrollTo(window.pageXOffset, acc_toggle.getPosition().y);
                    }
                }
            }
        });

        counter++;
        toggler = $$(togglerName + counter);
        content = $$(contentName + counter);
    }
});
