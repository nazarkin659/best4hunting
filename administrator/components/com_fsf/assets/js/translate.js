
var translate_html = false;

function doTranlsateLoaded() {
    // load in existing translate data into fields

    // load in existing values

    for (field in to_translate) {
        if (to_translate[field].type == "html") {
            translate_html = true;
            var val = jQuery('[name="' + field + '"]').html();
            jQuery('#current-' + field).html("'" + val + "'");
        } else {
            var val = jQuery('#' + field).val();
            jQuery('#current-' + field).html("'" + val + "'");
        }
    }

    try
    {
        var data = jQuery.parseJSON(jQuery('#translation').val());

        for (field in data) {
            for (lang in data[field]) {
                var value = data[field][lang];
                jQuery('#tran-' + field + '-' + lang).val(value);
            }
        }

        if (translate_html) {
            var k = tinyMCE.init({
                // General
                directionality: "ltr",
                editor_selector: "mce_editable",
                language: "en",
                mode: "specific_textareas",
                skin: "default",
                theme: "advanced",
                // Cleanup/Output
                inline_styles: true,
                gecko_spellcheck: true,
                entity_encoding: "raw",
                extended_valid_elements: "hr[id|title|alt|class|width|size|noshade|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],a[id|class|name|href|hreflang|target|title|onclick|rel|style]",
                force_br_newlines: false, force_p_newlines: true, forced_root_block: 'p',
                invalid_elements: "script,applet,iframe",
                // URL
                relative_urls: true,
                remove_script_host: false,

                // Advanced theme
                theme_advanced_toolbar_location: "top",
                theme_advanced_toolbar_align: "left",
                theme_advanced_source_editor_height: "550",
                theme_advanced_source_editor_width: "750",
                theme_advanced_resizing: true,
                theme_advanced_resize_horizontal: false,
                theme_advanced_statusbar_location: "bottom", theme_advanced_path: true
            });


            jQuery('.tbox').css('position', 'absolute');
            jQuery('.tinner').css('height', 'auto');
            jQuery('.tbox').css('top', '100px');
            jQuery('.tcontent').css('overflow', 'auto');
            window.scrollTo(0, 0);
        }
    } catch (err) {

    }
}

function saveTranslated() {

    if (translate_html) {

        for (field in to_translate) {
            if (to_translate[field].type == "html") {
                for (lang in tr_langs) {
                    tinyMCE.get('tran-' + field + '-' + lang).save();
                }
                
            }
        }
    }

    var target = {};

    for (field in to_translate) {
        for (lang in tr_langs) {
            var val = jQuery('#tran-' + field + '-' + lang).val();
            if (val) {
                if (!target[field]) target[field] = {};
                target[field][lang] = val;
            }
        }
    }

    jQuery('#translation').val(JSON.stringify(target));

    TINY.box.hide();

    displayTranslations();
}

function displayTranslations() {

    try {
        var data = jQuery.parseJSON(jQuery('#translation').val());

        for (field in to_translate) {
            jQuery('#trprev_' + field).html("");
        }

        for (field in data) {
            var html = "";
            for (lang in data[field]) {
                var value = data[field][lang];
                html += "<b>" + tr_langs[lang] + "</b>:";
                html += value;
                html += "<br />";
            }
            jQuery('#trprev_' + field).html(html);
        }
    } catch (err) {

    }
}