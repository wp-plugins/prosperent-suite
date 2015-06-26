var shortCode = {
    local_ed: "ed",
    init: function(b) {
        shortCode.local_ed = b;
        tinyMCEPopup.resizeToInnerSize()
    },
    insert: function(b) {
        b = getNewCurrent() ? getNewCurrent() : "";
        var c = jQuery("#" + b + "fetch").val(),
            d = jQuery("#" + b + "query").val(),
            e = jQuery("#" + b + "d").val(),
            f = jQuery("#" + b + "limit").val(),
            h = jQuery("#" + b + "view:checked").val(),
            k = jQuery("#" + b + "goTo:checked").val(),
            l = jQuery("#" + b + "b").val(),
            m = jQuery("#" + b + "visit").val(),
            n = jQuery("#" + b + "id").val(),
            p = jQuery("#topic").val(),
            q = jQuery("#useTags").is(":checked"),
            r = jQuery("#useTitle").is(":checked"),
            t = jQuery("#onSale").is(":checked"),
            u = jQuery("#gridimgsz").val(),
            g = jQuery("#prosperSC").val(),
            v = jQuery("#css").val(),
            w = jQuery("#searchFor:checked").val(),
            x = jQuery("#sBarText").val(),
            y = jQuery("#sButtonText").val(),
            z = jQuery("#pricerangea").val(),
            A = jQuery("#pricerangeb").val(),
            B = jQuery("#noShow:checked").val(),
            C = jQuery("#" + b + "ImageType").val(),
            D = jQuery("#sBarWidth").val(),
            E = jQuery("#widthStyle:checked").val(),
            F = jQuery("#percentrangea").val(),
            G = jQuery("#percentrangeb").val(),
            H = jQuery("#" + b + "category").val(),
            I = jQuery("#prosperHeldURL").val(),
            a =
            "[" + g;
        d && (a += ' q="' + d + '"');
        k && (a += ' gtm="' + k + '"');
        l && (a += ' b="' + l + '"');
        e && (a += ' mid="' + e + '"');
        f && (a += ' l="' + f + '"');
        h && (a += ' v="' + h + '"');
        n && (a += ' id="' + n + '"');
        c && (a += ' ft="' + c + '"');
        v && (a += ' css="' + v + '"');
        p && (a += ' q="' + p + '"');
        q && (a += ' utg="' + q + '"');
        r && (a += ' utt="' + r + '"');
        t && (a += ' sale="' + t + '"');
        w && (a += ' sf="' + w + '"');
        x && (a += ' sbar="' + x + '"');
        y && (a += ' sbu="' + y + '"');
        m && (a += ' vst="' + m + '"');
        B && (a += ' noShow="' + B + '"');
        C && (a += ' imgt="' + C + '"');
        H && (a += ' cat="' + H + '"');
        D && (a += ' w="' + D + '"');
        E && (a += ' ws="' + E + '"');
        I && I != 'http://' && (a += ' ahl="' + I + '"');
        (z ||
            A) && (a += ' pr="' + z + "," + A + '"');
        (F || G) && (a += ' po="' + F + "," + G + '"');
        u && "prod" == b && (a += ' gimgsz="' + u + '"');
        //"linker" == g && shortCode.local_ed.selection.getContent().match(/\[linker [^\]]*\](.+)\[\/linker\]/) && (newContent = shortCode.local_ed.selection.getContent().match(/\[linker [^\]]*\](.+)\[\/linker\]/));
        a += "]" + (shortCode.local_ed.selection.getContent() ? (shortCode.local_ed.selection.getContent()).replace(/(<[^img]([^>]+)>)/ig, ""): '') + "[/" + g + "]";
        a = replaceShortcodes(a);
        tinyMCEPopup.execCommand("mceReplaceContent", !1, a);
        tinyMCEPopup.close()
    }
};
tinyMCEPopup.onInit.add(shortCode.init, shortCode);
document.write('<base href="' + tinymce.baseURL + '" />');

function getAttr(b, c) {
    return (c = (new RegExp(c + '="([^"]+)"', "g")).exec(b)) ? window.decodeURIComponent(c[1]) : ""
}

function html(b, c, d, e) {
    c = window.encodeURIComponent(c);
    content = window.encodeURIComponent(d);
    if ("linker" == c) return '<span class="' + b + '" title="' + c + " " + d.replace(/"/g, "") + '" style="display:inline-block;text-decoration:underline;color:red;" data-sh-attr="' + c + '" data-sh-content="' + content + '" data-mce-resize="false" data-mce-placeholder="1">' + e + "</span>";
    placeholder = location.protocol + '//' + location.hostname + "/wp-content/plugins/prosperent-suite/includes/img/" + ("compare" == c ? 'prosperInsert' : c) + "Placeholder.png";
    return '<img class="' + b + '" title="' + c + " " + (d ? d.replace(/"/g, "") : "") + '" alt="' + c + " " + (d ? d.replace(/"/g, "") : "") + '" src="' + placeholder + '" data-sh-attr="' +
    c + '" data-sh-content="' + content + '" data-mce-resize="false" data-mce-placeholder="1" />';
}

function replaceShortcodes(b) {
    return b.replace(/\[(linker|prosper_store|prosper_search|prosperInsert|contentInsert|compare) ([^\]]*)\](.+)?\[\/(linker|prosper_store|prosper_search|prosperInsert|contentInsert|compare)\]/g, function(b, d, e, f) {
        return html("prosperShort", d, e, f)
    })
}

function getNewCurrent() {
    var b;
    jQuery("#products_tab").hasClass("current") ? b = "prod" : b = "merchant";
    return b
};