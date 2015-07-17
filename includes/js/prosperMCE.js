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
            v = jQuery("#prosperSC").val(),
            w = jQuery("#css").val(),
            x = jQuery("#searchFor:checked").val(),
            y = jQuery("#sBarText").val(),
            z = jQuery("#sButtonText").val(),
            A = jQuery("#pricerangea").val(),
            B = jQuery("#pricerangeb").val(),
            C = jQuery("#noShow:checked").val(),
            D = jQuery("#" + b + "ImageType").val(),
            E = jQuery("#sBarWidth").val(),
            F = jQuery("#widthStyle:checked").val(),
            G = jQuery("#percentrangea").val(),
            H = jQuery("#percentrangeb").val(),
            I = jQuery("#" + b + "category").val(),
            g = jQuery("#prosperHeldURL").val(),
            queryString = b == 'prod' ? window.prodqueryString : window.merchantqueryString,          		
            a = "[" + v;
        d && (a += ' q="' + d + '"');
        k && (a += ' gtm="' + k + '"');
        l && (a += ' b="' + l + '"');
        e && (a += ' mid="' + e + '"');
        f && (a += ' l="' + f + '"');
        h && (a += ' v="' + h + '"');
        n && (a += ' id="' + (n.replace(/notfound~/g, '')) + '"');
        c && (a += ' ft="' + c + '"');
        w && (a += ' css="' + w + '"');
        p && (a += ' q="' + p + '"');
        q && (a += ' utg="' + q + '"');
        r && (a += ' utt="' + r + '"');
        t && (a += ' sale="' + t + '"');
        x && (a += ' sf="' + x + '"');
        y && (a += ' sbar="' + y + '"');
        z && (a += ' sbu="' + z + '"');
        m && (a += ' vst="' + m + '"');
        C && (a += ' noShow="' + C + '"');
        D && (a += ' imgt="' + D + '"');
        I && (a += ' cat="' + I + '"');
        queryString && (a += ' fb="' + queryString + '"');
        E && (a +=
            ' w="' + E + '"');
        F && (a += ' ws="' + F + '"');
        g && "http://" != g && (a += ' ahl="' + g + '"');
        (A || B) && (a += ' pr="' + A + "," + B + '"');
        (G || H) && (a += ' po="' + G + "," + H + '"');
        u && "prod" == b && (a += ' gimgsz="' + u + '"');
        a += "]" + (shortCode.local_ed.selection.getContent() ? shortCode.local_ed.selection.getContent().replace(/(<[^img]([^>]+)>)/ig, "") : "") + "[/" + v + "]";
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
    placeholder = location.protocol + "//" + location.hostname + "/wp-content/plugins/prosperent-suite/includes/img/" + ("compare" == c ? "prosperInsert" : c) + "Placeholder.png";
    return '<img class="' + b + '" title="' + c + " " + (d ? d.replace(/"/g, "") : "") + '" alt="' + c + " " + (d ? d.replace(/"/g, "") : "") + '" src="' + placeholder + '" data-sh-attr="' + c + '" data-sh-content="' + content + '" data-mce-resize="false" data-mce-placeholder="1" />'
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