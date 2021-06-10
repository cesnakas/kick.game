"use strict";
document.addEventListener("DOMContentLoaded", function () {
    var e = document.querySelector("body");
    e.classList.remove("preload");
    var t = document.getElementById("cookieSection"), n = document.querySelectorAll(".cookieAccept");

    function o(e) {
        for (var t = document.cookie.split(";"), n = 0; n < t.length; n++) {
            var o = t[n].trim().split("=") || [];
            if (o != [] && e == o[0]) return o[1]
        }
        return ""
    }

    t && ("" === o("kickgame") && (t.style.display = "inherit"), n.forEach(function (e) {
        e.addEventListener("click", function (e) {
            var t;
            e.preventDefault(), t = o("kickgame"), e = document.getElementById("cookieSection"), "" === t ? (e.style.display = "none", function (e, t) {
                var n = new Date;
                n.setTime(n.getTime() + 6048e5), n = "expires=" + n.toGMTString(), document.cookie = e + "=" + t + ";" + n + "; path=/", document.getElementById("cookieSection").style.display = "none"
            }("kickgame", "Accept")) : e.style.display = "inherit"
        })
    }));
    var r = document.querySelector(".inputFileScrinPubg"), i = document.querySelector(".fileUploadedScrinPubg");
    r && i && r.addEventListener("change", function () {
        p(r.files[0], i, r)
    });
    var a = document.querySelector(".inputFileScrinPubgNext"), c = document.querySelector(".fileUploadedScrinPubgNext");
    a && c && a.addEventListener("change", function () {
        p(a.files[0], c, a)
    });
    var l = document.querySelector(".inputFile"), s = document.querySelector(".fileUploaded");
    l && s && l.addEventListener("change", function () {
        p(l.files[0], s, l)
    });
    var u = document.querySelector(".inputFileAvatar"), d = document.querySelector(".fileAvatarUploaded");

    function p(e, t, n) {
        if (!["image/jpeg", "image/png", "image/gif"].includes(e.type)) return alert("Только изображения"), void (n.value = "");
        20971520 < e.size ? alert("Файл не проходит по размеру") : ((n = new FileReader).onload = function (e) {
            console.log(e.target), t.setAttribute("style", "background-image: url(".concat(e.target.result, ")"))
        }, n.onerror = function (e) {
            alert("Err")
        }, n.readAsDataURL(e))
    }

    u && d && u.addEventListener("change", function () {
        p(u.files[0], d, u)
    });
    var m = document.querySelector(".form-field__eyes");
    m && m.addEventListener("click", function () {
        var e, t;
        e = ".form-field__input_pass", t = m, "password" === (e = document.querySelector(e)).type ? (e.type = "text", t.className = "form-field__eyes form-field__eyes_hide") : (e.type = "password", t.className = "form-field__eyes")
    });
    var f, g, v, y, h = document.getElementById("navbar-link"), b = document.getElementById("navbar__burger");
    f = "navbar__burger", g = "navbar-link", v = document.getElementById(f), y = document.getElementById(g), v && y && v.addEventListener("click", function (e) {
        y.classList.toggle("show"), v.classList.toggle("active"), e.stopPropagation()
    }), h && e.addEventListener("click", function (e) {
        e.target.closest(".navbar-link") || (h.classList.remove("show"), b.classList.remove("active"))
    })
}), $(function () {
    $("#auth-phone").intlTelInput({
        initialCountry: "auto",
        geoIpLookup: function (t) {
            jQuery.get("//ip-api.com/json/", function () {
            }, "jsonp").always(function (e) {
                e = e && e.countryCode ? e.countryCode : "ru";
                t(e)
            })
        },
        utilsScript: "/local/templates/kickgame/dist/js/utils.js",
        nationalMode: !1,
        formatOnDisplay: !0,
        hiddenInput: "full_number",
        preferredCountries: ["ru"]
    });
    $(".form-team").validate({
        rules: {
            nameTeam: {required: !0},
            tagTeam: {required: !0},
            descriptionTeam: {required: !0},
            logoTeam: {required: !0}
        },
        messages: {
            nameTeam: {required: "Введи название команды"},
            tagTeam: {required: "Введи тег команды"},
            descriptionTeam: {required: "Введи описание команды"},
            logoTeam: {required: "Прикрепи логотип команды"}
        }
    }), $(".form-scrin-pubgid").validate({
        rules: {scrinPubg: {required: !0}, modalID: {required: !0, digits: true}, modalNickname: {required: !0}},
        messages: {scrinPubg: {required: "Прикрепи скриншот"}, modalID: {required: "Укажи свой PUBG ID", digits: "Сюда можно вводить только цифры"}, modalNickname: {required: "Укажи свой Nickname"}}
    }), $(".form-scrin-pubgidnext").validate({
        rules: {scrinPubg: {required: !0}, comments: {required: !0}, modalID: {required: !0, digits: true}, modalNickname: {required: !0}},
        messages: {scrinPubg: {required: "Прикрепи скриншот"}, comments: {required: "Введи текст"}, modalID: {required: "Укажи свой PUBG ID", digits: "Сюда можно вводить только цифры"}, modalNickname: {required: "Укажи свой Nickname"}}
    }), $("#c-all").on("change", function () {
        var e = $(this), t = e.closest(".flex-table").find('input[type="checkbox"]');
        e.is(":checked") ? t.prop("checked", !0) : t.prop("checked", !1)
    });
    var e = {bootstrapCollapse: $(".card-custom")};
    if (e.bootstrapCollapse.length) for (var t = 0; t < e.bootstrapCollapse.length; t++) console.log("ok"), function (e) {
        e.find("a.collapsed").length || e.addClass("active"), e.on("show.bs.collapse", function () {
            e.addClass("active")
        }), e.on("hide.bs.collapse", function () {
            e.removeClass("active")
        })
    }($(e.bootstrapCollapse[t]));
    0 < $("body #bx-panel").length && $(".header").css({top: "39px"}), $(function () {
        var t = !1;
        $(document).on("click", "#ajax_next_page", function (e) {
            if (e.preventDefault(), t) return !1;
            e = $(this).attr("href");
            t = !0, $.ajax({
                url: e, type: "POST", data: {IS_AJAX: "Y"}, success: function (e) {
                    $("#ajax_next_page").after(e), $("#ajax_next_page").remove(), t = !1
                }
            })
        })
    }), $(".collapse").on("shown.bs.collapse", function (e) {
        var t = $(this).closest(".card");
        $("html,body").animate({scrollTop: t.offset().top - "72"}, 500), e.stopPropagation()
    });
    var n = document.querySelectorAll(".inputFile");
    Array.prototype.forEach.call(n, function (e) {
        var n = e.nextElementSibling, o = n.innerHTML;
        e.addEventListener("change", function (e) {
            var t = "";
            (t = this.files && 1 < this.files.length ? (this.getAttribute("data-multiple-caption") || "").replace("{count}", this.files.length) : e.target.value.split("\\").pop()) ? n.querySelector("span").innerHTML = t : n.innerHTML = o
        })
    })
});