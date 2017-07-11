! function(e) {
    e.fn.mbPagination = function(s) {
        function a() {
            if (e(".number-li:not(.active)").hide(), e(".dots").each(function() {
                    e(this).remove(".dots")
                }), e(".number-li").hasClass("active")) {
                var s = e(".number-li.active");
                s.show(), s.prev().html() !== e(".number-li").first().html() ? s.prev(".number-li").show().before('<li class="dots"><span>...</span></li>') : e(".number-li").first().show(),e(".number-li").last().show(), s.next().html() !== e(".number-li").last().html() ? s.next(".number-li").show().after('<li class="dots"><span>...</span></li>')  : e(".number-li").first().show(),e(".number-li").last().show()
            }
        }

        function i() {
            e(".non-number-li").prev().hasClass("active") ? (e(".next-list").addClass("disabled").next().addClass("disabled"), e(".prev-list").removeClass("disabled").prev().removeClass("disabled")) : e(".non-number-li").next().hasClass("active") ? (e(".next-list").removeClass("disabled").next().removeClass("disabled"), e(".prev-list").addClass("disabled").prev().addClass("disabled")) : e(".non-number-li").each(function() {
                e(this).removeClass("disabled")
            })
        }
        for (var t = {
                perPage: 10,
                prevText: "&#10092;",
                nextText: "&#10093;",
                showFirstLast: !1,
                firstText: "&#10092;&#10092;",
                lastText: "&#10093;&#10093;"
            }, l = e.extend({}, t, s), r = l.perPage, n = this, o = e(n).children(), v = e(".number-li"), b = o.length, u = 0; b > u; u++) u > l.perPage - 1 && o.eq(u).hide();
       
        var h = Math.ceil(b / r);
        if (b > r) {
            var m = '<ul class="mb-pagination">';
            l.showFirstLast && (m += '<li class="first-list non-number-li"><a href="javascript:;">' + l.firstText + "</a></li>"), m += '<li class="prev-list non-number-li"><a href="javascript:;">' + l.prevText + "</a></li>";
            for (var u = 1; h >= u; u++) {
                var d = "";
                1 == u && (d = " active"), m += '<li class="number-li' + d + '"><a href="javascript:;" >' + u + "</a></li>"
            }
            m += '<li class="next-list non-number-li"><a href="javascript:;">' + l.nextText + "</a></li>", l.showFirstLast && (m += '<li class="last-list non-number-li"><a href="javascript:;">' + l.lastText + "</a></li>"), m += "</ul>", e(this).after(m)
        }
        v.first().addClass("active"), i(), a(), e("body").on("click", ".mb-pagination a", function(s) {
            function t(e, s) {
                for (var a = e; s > a; a++) o.eq(a).show();
                for (var a = 0; b > a; a++)(e > a || a >= s) && o.eq(a).hide()
            }

            function l(e) {
                e.addClass("active").siblings().removeClass("active")
            }
            var n = e(this),
                u = e(this).parent(),
                h = e(".number-li.active");
            if (u.hasClass("number-li")) l(n.parent()), t((n.text() - 1) * r, n.text() * r);
            else {
                if (u.hasClass("prev-list")) {
                    if (h.prev().hasClass("non-number-li")) return !1;
                    var m = (Number(h.prev(v).text()) - 1) * r,
                        d = Number(h.prev(v).text()) * r;
                    t(m, d), l(h.prev())
                } else if (u.hasClass("next-list")) {
                    if (h.next().hasClass("non-number-li")) return !1;
                    var m = (Number(h.next(v).text()) - 1) * r,
                        d = Number(h.next(v).text()) * r;
                    t(m, d), l(h.next())
                }
                u.hasClass("last-list") && (t(b - r, b), l(e(".number-li").eq(e(".number-li").length - 1))), u.hasClass("first-list") && (t(0, r), l(e(".number-li").eq(0)))
            }
            return a(), i(), !1
        })
    }
}(jQuery);